<?php

namespace App\Http\Controllers;

use App\Models\CarparklotSettingKwh;
use App\Models\EvReceipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Classes\SystemID;
use App\Models\CarPark;
use App\Models\CarparkLotConnector;
use App\Models\CarParkLotSetting;
use App\Models\CarparkOper;
use App\Models\CarparklotSettingMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Terminal;
use App\Models\Company;
use App\Models\EvChargePoint;
use App\Models\EvConnector;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Message\Response;

class CarparkController extends Controller
{
    function carPark()
    {
        try {

            $test = null;

            return view('carpark.carpark_setting',
                compact('test'));

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }


    function listCarPark()
    {
        try {

            $data = CarPark::all();
            return DataTables::of($data)->
            addIndexColumn()->
            editColumn('lot_no', function ($data) {
                $lot_no = $data;
                return $lot_no;
            })->
            editColumn('rate', function ($data) {
                $rate = $data;
                return $rate;
            })->
            editColumn('kwh', function ($data) {
                $rate = $data;
                return $rate;
            })->
            addColumn('action', function ($row) {
                $btn = '<a  href="javascript:void(0)" onclick="deleteMe(' .
					$row->id . ')" data-row="' . $row->id .
					'" class="delete"> <img width="25px" src="images/redcrab_50x50.png" alt=""> </a>';
                return $btn;
            })->addColumn('bluecrab', function ($row) {
                $btn = '<a  href="javascript:void(0)" onclick="" data-row="' .
					$row->id . '" class=""> <img width="25px" src="images/bluecrab_50x50.png" alt=""> </a>';
                return $btn;
            })->rawColumns(['bluecrab', 'action'])
                ->make(true);


        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);

            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    function listCarParkOpera()
    {
        try {

            $data = CarPark::with("carparkoper")->get();
            return DataTables::of($data)->
            addIndexColumn()->
            make(true);


        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);

            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    function save(Request $request)
    {
        try {
            $systemid = new SystemID("carparklot");
            $carparksetting = CarParkLotSetting::orderByDesc("id")->first();
            $carparksettingkwh = CarparklotSettingKwh::orderByDesc("id")->first();
            $allCp = CarPark::all();
            $carpark = CarPark::create([
                "systemid" => $systemid,
                "lot_no" => (sizeof($allCp) + 1),
                "rate" => $carparksetting ? $carparksetting->default_rate : 0,
                "kwh" => $carparksettingkwh ? $carparksettingkwh->default_kwh : 0
            ]);

            // CarparkOper::create([
            //     "carparklot_id" => $carpark->id,
            //     "in" => null,
            //     "out" => null,
            //     "amount" => 0,
            //     "payment" => 0,
            // ]);

            return ["data" => $carpark, "error" => false];

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    function updateValue(Request $request)
    {
        Log::info($request->all());
        if ($request->element === "default_rate") {
            $data = [
                "default_rate" => $request->value,
            ];

            $carParkLotSetting = CarParkLotSetting::create($data);
            return ["data" => $carParkLotSetting, "error" => false, "other" => "default_rate"];

        } else {

            if ($request->element === "default_kwh") {
                $data = [
                    "default_kwh" => $request->value,
                ];

                $carParkLotSetting = CarparklotSettingKwh::create($data);
                return ["data" => $carParkLotSetting, "error" => false, "other" => "default_kwh"];
            }else{
                $data = [
                    $request->key => $request->value,
                ];

                $prdOpen = CarPark::where("id", $request->element)->update($data);
                return ["data" => $prdOpen, "error" => false, "other" => $request->key];
            }


        }

    }


    function carParkLanding()
    {
        try {

            $company = Company::first();

            Log::debug('carParkLanding: company=' . json_encode($company));

            $currency = DB::table('currency')->
            where('id', $company->currency_id)->
            orderBy('code')->get()->first();

            // Protect against NULL currency
            if (empty($currency)) {
                $currency = DB::table('currency')->
                where('code', 'MYR')->get()->first();
            }

            Log::debug('carParkLanding: currency=' . json_encode($currency));
            $terminal_all_value = $this->getTerminalInfo();
            $terminal = (object)array('currency' => $currency->code);

            $carparkOperas = $this->getCustomCarParkOpera();
            $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
            $transaction_count = CarparkOper::count();
            $paid_count = CarparkOper::query()->where('status', 'paid')->count();
            return view('carpark.carpark_landing',
                compact('carparkOperas', 'terminal','terminal_all_value','current_setting_mode',
            'stop_count','transaction_count','paid_count'));

        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }

    public function changeMode(Request $request)
    {
        $mode = !empty( $request->mode ) ? $request->mode : '';
        if( !empty( $mode ) ){
            CarparklotSettingMode::changeCurrentCarparkMode($mode);
        }
    }

    function getTerminalInfo(){
        $client_ip = request()->ip();
        $terminal_all_value = DB::table('terminal')->where('client_ip', $client_ip)->first();
        return $terminal_all_value;
    }


    function getCustomCarParkOpera()
    {   
        $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();

        if( empty($current_setting_mode) ){

            $current_setting_mode = CarparklotSettingMode::changeCurrentCarparkMode('hour');
        }

        $carparkOperas = CarPark::with("carparkoper")
        ->doesntHave("carparkoper")
        ->orWhereHas('carparkoper', function ($query) {
            $query->orWhere('status', '!=', 'paid');
        })
        ->get();

        $carparkOperasNew = [];
        foreach ($carparkOperas as $opera) {
            try {
                $client = new Client([
                    'headers' => [ 'Content-Type' => 'application/json' ]
                ]);
                $connector_carparks = CarparkLotConnector::query()
                ->where('carparklot_id', $opera->id)
                ->get();
                foreach($connector_carparks as $connector_carpark)
                {
                    $con_park = $connector_carpark;
                }
            } catch (\Exception $e) {
                Log::error('**** QUERY ERROR: CarparkLotConnector::query()
                ->where(carparklot_id, $request->id)
                ->get(); ****');
                Log::error([
                    "Cause" => "Relation between carpark and connector not present",
                    "Error" => $e->getMessage(),
                    "File"  => $e->getFile(),
                    "Line"  => $e->getLine()
                ]);
            }
            try {
                $connector = EvConnector::query()->find($con_park->connector_id);
            } catch (\Exception $e) {
                Log::error('**** EvConnector::query()->find($con_park->connector_id) ****');
                Log::error([
                    "Cause" => "Connector not found",
                    "Error" => $e->getMessage(),
                    "File"  => $e->getFile(),
                    "Line"  => $e->getLine()
                ]);
            }
            Log::debug('connector : '. $connector);
            try {
                $chargePoint = EvChargePoint::query()->find($connector->chargepoint_id);
            } catch (\Exception $e) {
                Log::error('**** EvChargePoint::query()->find($connector->chargepoint_id) ****');
                Log::error([
                    "Cause" => "ChargePoint not found",
                    "Error" => $e->getMessage(),
                    "File"  => $e->getFile(),
                    "Line"  => $e->getLine()
                ]);
            }
            try {
                $url = 'http://'.env('THUNDER_IPADDR')
                .
                '/steve/get-status?charge_box_id='
                .
                $chargePoint->name;
            $response = $client->request('GET', $url,  
                    ['body' => json_encode(
                        [
                            'flag' => 'start'
                        ]
                    )]);
            Log::debug('url: '. $url);
            Log::debug('chargePoint: '. $chargePoint->name);
            Log::debug('response: '. $response->getBody());

            $opera['heartbeat'] = $response->getBody();
            if($opera['heartbeat'] != 'none')
            {
                $last_heartbeat = Carbon::parse($opera['heartbeat']);
                $now = Carbon::now();
                $minutes = $last_heartbeat->DiffInMinutes($now);
                if($minutes < 15)
                {
                    $opera['heartbeat'] = 'yes';
                }else{
                    $opera['heartbeat'] = 'no';
                }
            }else{
                $opera['heartbeat'] = 'no';
            }
            } catch (\Exception $e) {
                $opera['heartbeat'] = "not attached";
            }
            

            $hours = 0;
            $amount = 0;
            if (( isset($opera->carparkoper->in) && $opera->carparkoper->in != null &&
                isset($opera->carparkoper->out) && $opera->carparkoper->out != null)) {
                $date1 = new \DateTime($opera->carparkoper->in);
                $date2 = new \DateTime($opera->carparkoper->out);

                $diff = $date2->diff($date1);
                $min = 0;
                if ($diff->i > 0 || $diff->s > 0) {
                    $min = 1;
                }
                $hours  = $diff->h;
                $hours  = $hours + ($diff->days * 24) + $min;
                // $amount = $hours * $opera->rate;
                if( $current_setting_mode == 'hour' ){
                    $amount = $hours * $opera->rate;
                }
                else{
                    $hours  = 2;
                    $amount = $hours * $opera->kwh;
                }
            }

            if( $current_setting_mode == 'hour' ){
                $opera->current_rate = $opera->rate;
            }
            else{
                $opera->current_rate = $opera->kwh;
            }
            $opera["hours"] = $hours;
            $opera["amount"] = $amount;
            if($opera->carparkoper()->exists())
            {
                $opera["start_meter"] = $opera->carparkoper->start_meter;
                $opera["stop_meter"] = $opera->carparkoper->stop_meter;
            }else{
                $opera["start_meter"] = 0;
                $opera["stop_meter"] = 0;
            }
            

            array_push($carparkOperasNew, $opera);
        }

        $carparkOperas = $carparkOperasNew;
        return $carparkOperas;
    }


    function loadDefaultRate()
    {
        try {

            $data = CarParkLotSetting::orderByDesc("id")->first();
            $carparksettingkwh = CarparklotSettingKwh::orderByDesc("id")->first();

            return ["data" => $data,"carparksettingkwh"=>$carparksettingkwh];

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }

    function lotDelete(Request $request)
    {
        try {

            CarPark::find($request->id)->delete();
            CarparkOper::where("carparklot_id", $request->id)->delete();
            return ["message" => "delete done", "Error" => false];

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }


    function actionStatus(Request $request)
    {
        
        // try {
        //     $client = new Client();
            
        //     $client->get('192.167.1.55', [
        //         'connect_timeout' => 5, // Connection timeout
        //     ]);        
        // } catch (GuzzleHttp\Exception\GuzzleException $e) {
            
        //         return 'no connection';
            
        // }
        $url = 'http://'.env('THUNDER_IPADDR');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (200!=$retcode){
            $showmodal = true;
            $carparkOperas = $this->getCustomCarParkOpera();
            $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            return view("carpark.carparklot_table",
				compact("carparkOperas","current_setting_mode","showmodal",
                'stop_count','transaction_count','paid_count'));
        }
        $response = "no response, error occurred";

        try {
            if( !empty($request->carparkoper_id) ){
                try {
                    $connector_carparks = CarparkLotConnector::query()->
						where('carparklot_id', $request->id)->
						get();

                    foreach($connector_carparks as $connector_carpark) {
                        $con_park = $connector_carpark;
                    }

                } catch (\Exception $e) {
                    Log::error('**** CarparkLotConnector::query()
                    ->where(carparklot_id, $request->id)
                    ->first() ****');
                    Log::error([
                        "Cause" => "Carpark doesnt have any connectors",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
                }

                try {
                    $connector = EvConnector::query()->find($con_park->connector_id);
                } catch (\Exception $e) {
                    Log::error('**** EvConnector::query()->find($connector_carpark->connector_id) ****');
                    Log::error([
                        "Cause" => "Connector not found",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
                }

                try {
                    $chargePoint = EvChargePoint::query()->find($connector->chargepoint_id);
                } catch (\Exception $e) {
                    Log::error('**** EvChargePoint::query()->find($connector->chargepoint_id) ****');
                    Log::error([
                        "Cause" => "ChargePoint not found",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
                }

                try {
                    $carparkopera = CarparkOper::find($request->carparkoper_id);
                } catch (\Exception $e) {
                    Log::error('**** CarparkOper::find($request->carparkoper_id) ****');
                    Log::error([
                        "Cause" => "Carpark Operation not found",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
                }

                try {

                    $client = new Client([
                        'headers' => [ 'Content-Type' => 'application/json' ]
                    ]);
                    $url = 'http://'.env('THUNDER_IPADDR').
                    '/steve/remote_stop_transaction?chargePointSelectList=JSON%3B'
                    .
                    $chargePoint->name 
                    .
                    '%3B-&transactionId='
                    .
                    $carparkopera->transaction_id;
                    Log::debug('URL: '. $url);
                    $response = $client->request('POST', $url
                        , 
                        ['body' => json_encode(
                            [
                                'flag' => 'start'
                            ]
                    )]);
                    $data = json_decode($response->getBody(), true);
    
                    $carparkopera['stop_meter'] = $data['stop_value'];
                    $carparkopera['stop_timestamp'] = Carbon::now();
                    if ($carparkopera->in == null) {
                        $carparkopera->in = now();
                    } else {
                        $carparkopera->out = now();
                        $carparkopera->amount = intval($request->int_amount);
                    }
                    $carparkopera->save();
                    // $carpark = Carpark::query()->find($request->id);
                    // $carpark['kwh'] = 
                    // (($carparkopera['stop_meter'] - $carparkopera['start_meter']) / 1000) * $carpark['rate']
				} catch (\Exception $e) {
                    Log::error([
                        "Cause" => "Stop Transaction Failed",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
				}

            }else{
                try {
                    $client = new Client([
                        'headers' => [ 'Content-Type' => 'application/json' ]
                    ]);
                    $connector_carparks = CarparkLotConnector::query()
                    ->where('carparklot_id', $request->id)
                    ->get();
                    foreach($connector_carparks as $connector_carpark)
                    {
                        $con_park = $connector_carpark;
                    }
                } catch (\Exception $e) {
			        Log::error('**** QUERY ERROR: CarparkLotConnector::query()
                    ->where(carparklot_id, $request->id)
                    ->get(); ****');
                    Log::error([
                        "Cause" => "Relation between carpark and connector not present",
                        "Error" => $e->getMessage(),
                        "File"  => $e->getFile(),
                        "Line"  => $e->getLine()
                    ]);
                }

				Log::debug('connector pivot: '. $con_park .  $request->id);

				try {
					$connector = EvConnector::query()->find($con_park->connector_id);
				} catch (\Exception $e) {
					Log::error('**** EvConnector::query()->find($con_park->connector_id) ****');
					Log::error([
						"Cause" => "Connector not found",
						"Error" => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}
				Log::debug('connector : '. $connector);

				try {
					$chargePoint = EvChargePoint::query()->find($connector->chargepoint_id);
				} catch (\Exception $e) {
					Log::error('**** EvChargePoint::query()->find($connector->chargepoint_id) ****');
					Log::error([
						"Cause" => "ChargePoint not found",
						"Error" => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}
				Log::debug('chargePoint : '. $chargePoint);

				$url = 'http://'.env('THUNDER_IPADDR')
				.
				'/steve/remote_start_transaction?connectorId='
				.
				$connector->connector_pk
				.
				'&idTag=test&chargePointSelectList=JSON%3B'
				.
				$chargePoint->name 
				. 
				'%3B-';
				Log::debug('URL: '. $url);
				$response = $client->request('POST', $url,  
					['body' => json_encode(
						[
							'flag' => 'start'
						]
					)]);

				$data = json_decode($response->getBody(), true);
				// $carpark = CarPark::query()->find($request->id);

				try {
					$chargePoint->update([
						'ocpp_protocol' => $data['ocpp_protocol'],
						'vendor' => $data['vendor'],
						'chargepoint_serial_no' => $data['chargepoint_serial_no'],
						'firmware_version' => $data['firmware_version'],
						'meter_type' => $data['meter_type'],
						'meter_serial_no' => $data['meter_serial_no'],
						// 'address' => $data['address'],
						// 'latitude' => $data['latitude'],
						// 'longitude' => $data['longitude'],
					]);                    } catch (\Exception $e) {
					Log::error('**** chargePoint->update ****');
					Log::error([
						"Cause" => "ChargePoint update failed",
						"Error" => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}

				try {
					$connector->update([
						'ocpp_version' => $data['ocpp_protocol'],
					]); 
					} catch (\Exception $e) {
					Log::error('**** $connector->update ****');
					Log::error([
						"Cause" => "Connector ocpp_version update failed",
						"Error" => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}

				try {
					CarparkOper::create([
						"carparklot_id" => $request->id,
						"in" => now(),
						"out" => null,
						"status" => 'active',
						"amount" => 0,
						"payment" => 0,
						"transaction_id" => $data['transaction_id'],
						"start_timestamp" => Carbon::now(),
						"start_meter" => $data['start_value']
					]);
					} catch (\Exception $e) {
					Log::error('**** CarparkOper::create ****');
					Log::error([
						"Cause" => "Carpark Operation record creation failed",
						"Error" => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}
            }

			Log::debug('actionStatus: '. (String) $response->getBody());

            $carparkOperas = $this->getCustomCarParkOpera();
            $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
            $transaction_count = CarparkOper::count();
            $paid_count = CarparkOper::query()->where('status', 'paid')->count();
            return view("carpark.carparklot_table",
				compact("carparkOperas","current_setting_mode",
                'stop_count','transaction_count','paid_count'));

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);
            $carparkOperas = $this->getCustomCarParkOpera();
            $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
            $transaction_count = CarparkOper::count();
            $paid_count = CarparkOper::query()->where('status', 'paid')->count();
            return view("carpark.carparklot_table",
				compact("carparkOperas","current_setting_mode",
                'stop_count','transaction_count','paid_count'));
        }
    }


    function getRounding(Request $request)
    {
        $rounding = $this->round_amount($request->amount);
        if ($rounding < 0) {
            $amount =  "0.0" .
            strval( abs($this->round_amount($request->amount)));
            $amount = -1 * abs($amount);
            
            return ["number" => $this->round_amount($request->amount),
				"text_number" => $amount];

        } else {
            return ["number" => $this->round_amount($request->amount),
				"text_number" => "0.0" .
				strval($this->round_amount($request->amount))];
        }
    }

    function round_amount($num)
    {
        $num = round($num, 2);
        $split = explode('.', $num);
        if (is_array($split)) {
            $whole = $split[0];
            $dec = $split[1] ?? 0;
            $round_fig = substr($dec, 1, 1);
            if ($round_fig <= 2 && $round_fig > 0) {
                return (int)-($round_fig);
            } else if ($round_fig < 5 && $round_fig > 2) {
                $res = 5 - $round_fig;
                return (int)("$res");
            } else if ($round_fig < 8 && $round_fig > 5) {
                $res = $round_fig - 5;
                return (int)-("$res");
            } else if ($round_fig <= 9 && $round_fig >= 8) {
                $res = 10 - $round_fig;
                return (int)("$res");
            }
            return 0;
        } else {
            return 0;
        }
    }


    function setEnter(Request $request)
    {

        $systemid = new SystemID("ev_receipt");

        $user_id = Auth::user()->id;
        $user_currency_id = Auth::user()->currency_id;

        $company = Company::first();

        $terminal = DB::table('terminal')->first();
        $ev_receipt = DB::table("ev_receipt")->insertGetId([
            "systemid" => $systemid,
            "service_tax" => $request->service_tax,
            "payment_type" => $request->payment_type,
            "terminal_id" => $terminal->id,
            "staff_user_id" => $user_id,
            "company_id" => $company->id,
            "company_name" => $company->name,
            "currency" => $user_currency_id,
            "round" => $request->round,
            "remark" => " ",
            "pump_no" => 0,
            "pump_id" => 0,
            "description" => $request->description,
            "hours" => $request->hours,
            "rate" => $request->rate,
            "myr" => $request->myr,
            "itemAmount" => $request->itemAmount,
            "tax" => $request->tax,
            "total" => $request->total,
            "cash_received" => ($request->payment_type == "cash" ? $request->cash_received : 0),

        ]);

        $carparkopera = CarparkOper::find(intval($request->carparkoper_id));
        $carparkopera->status = "paid";
        $carparkopera->save();
        DB::table("evreceiptcarparklot")->insert([
            "evreceipt_id" => $ev_receipt,
            "carparklot_id" => intval($request->carparklot_id),
            "created_at" => now()
        ]);

        $carparkOperas = $this->getCustomCarParkOpera();
        $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
        $transaction_count = CarparkOper::count();
        $paid_count = CarparkOper::query()->where('status', 'paid')->count();
        return view("carpark.carparklot_table", compact("carparkOperas",
        'stop_count','transaction_count','paid_count'));
    }


    public function multiTransactionCheck(Request $request)
    {
        $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
        $transaction_count = CarparkOper::count();
        $paid_count = CarparkOper::query()->where('status', 'paid')->count();
        if(isset($request['stop_count']) and isset($request['transaction_count'])
        and isset($request['paid_count']))
        {
            if($request['stop_count'] != $stop_count 
            or
             $request['transaction_count'] != $transaction_count
             or
             $request['paid_count'] != $paid_count)
             {
                $carparkOperas = $this->getCustomCarParkOpera();
                $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
                return view("carpark.carparklot_table",
                    compact("carparkOperas","current_setting_mode",
                    'stop_count','transaction_count','paid_count'));
             }
        return 'no change';

        }
        return 'no change';

    }
}
