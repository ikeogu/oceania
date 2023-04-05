<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FuelFulltankReceipt;
use App\Models\FuelFulltankReceiptdetails;
use App\Models\FuelFulltankReceiptList;
use App\Models\FuelReceipt;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;

class FuelFulltankController extends Controller
{
    public function FTfuelReceipList(Request $request)
    {
        $date = $request->date;
        $company = Company::first();
        $approved_at = $company->approved_at;

        return view('fuel_fulltank.fuel_fulltank_receiptlist', compact('date', 'approved_at'));
    }


    public function FTCreateFuelList(Request $request)
    {
        try {
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();
            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $systemid = Systemid::fuelfulltank_receipt_system_id($terminal->id);
            $pump_hardware = DB::table('local_pump')->where("pump_no", $request->pump_no)->first();
            $receipt = new FuelFulltankReceipt();
            $receipt->systemid = $systemid;

            if ($request->payment_type == "card") {
                $receipt->payment_type = "creditcard";
                $receipt->creditcard_no = $request->creditcard_no ?? 0;
                //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_received = 0;
            } elseif ($request->payment_type == 'wallet') {
                $receipt->payment_type = "wallet";
                //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_received = 0;
            } elseif ($request->payment_type == 'creditac') {
                $receipt->payment_type = "creditac";
                //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_received = 0;
            } else {
                $receipt->payment_type = $request->payment_type;
                $receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_change = ($request->change_amount ?? 0) * 100;
            }

            $receipt->service_tax = $terminal->tax_percent;
            $receipt->terminal_id = $terminal->id;
            $receipt->mode = $terminal->mode;
            $receipt->location_id = $location->id;
            $receipt->staff_user_id = $user->id;
            $receipt->company_id = $company->id;
            $receipt->company_name = $company->name;
            $receipt->gst_vat_sst = $company->gst_vat_sst;
            $receipt->business_reg_no = $company->business_reg_no;
            $receipt->receipt_logo = $company->corporate_logo;
            $receipt->receipt_address = $company->office_address;

            $currencyarr = DB::table('currency')->where('id', $company->currency_id)->orderBy('code')->get()->first();

            $receipt->currency = $currencyarr->code ?? 'MYR';

            $receipt->status = "active";
            $receipt->remark = "NULL";
            $receipt->transacted = "pos";

            $receipt->pump_id = $pump_hardware->id;
            $receipt->pump_no = $request->pump_no;

            $receipt->transacted = "pos";
            $receipt->save();

            Log::debug('FT-CreateFuelList: $receipt = ' . json_encode($receipt));

            // Store Data in Fuel Receiptlist Table
            $this->FTStoreFuelReceiptList($receipt, $request->dose, $request->filled);

            $receiptproductsdiscount = 0;

            $receiptproduct_id = DB::table('fuelfulltank_receiptproduct')->insertGetId([
                "fulltank_receipt_id" => $receipt->id,
                "product_id" => $request->product_id,
                "name" => $request->product,
                "quantity" => $request->qty,
                "price" => $request->price * 100,
                "discount_pct" => 0,
                "discount" => 0,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $amount = (float) number_format($request->item_amount);
            $price = (float) number_format($request->price);
            $sst = (float) number_format($request->sst);
            $total_amount = (float) number_format($request->dose);
            $rounding = (float) number_format($request->cal_rounding);

            $cash_received = 0;
            $cash_change = 0;
            $creditcard = 0;
            $creditac = 0;
            if ($receipt->payment_type == "cash") {
                $cash_received = $request->cash_received;
            } elseif ($receipt->payment_type == "wallet") {
                $wallet = $request->dose;
            } elseif ($receipt->payment_type == "creditac") {
                $creditac = $request->dose;
            } else {
                $creditcard = $request->dose;
            }

            /* ERROR LOGGED:
			'SQLSTATE[22003]: Numeric value out of range: 1264 Out of range value for column \'change\' at row 1 (SQL: insert into `fuelfulltank_receiptdetails` (`fulltank_receipt_id`, `total`, `rounding`, `item_amount`, `sst`, `discount`, `cash_received`, `change`, `creditcard`, `wallet`, `creditac`, `created_at`, `updated_at`) values (467, 12003, 2, 12003, 0, 0, 0, -1.9999999999996, 0, 12003, 0, 2022-04-06 17:03:04, 2022-04-06 17:03:04))'
			*/

            DB::table('fuelfulltank_receiptdetails')->insert([
                "fulltank_receipt_id" => $receipt->id,
                "total" => $request->dose * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->item_amount * 100,
                "sst" => $request->sst * 100,
                "discount" => $receiptproductsdiscount * 100,
                "cash_received" => $cash_received * 100,
                "change" => (float) $request->change_amount * 100,
                "creditcard" => $creditcard * 100,
                "wallet" => ($wallet ?? 0) * 100,
                "creditac" => ($creditac ?? 0) * 100,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            //new code for EOD and personal shift.

            $brancheoddata = DB::table('brancheod')->whereDate('created_at', '=', date('Y-m-d'))->first();

            \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

            $loginOut = FuelFulltankReceipt::getFulltankCurrentLoginOut();

            $dataPshiftdetails = DB::table('pshiftdetails')->where('pshift_id', '=', $loginOut->shift_id)->first();
            $eoddetail_id = null;

            if (empty($brancheoddata)) {
                $brancheod = DB::table('brancheod')->insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $brancheoddata = DB::table('brancheod')->where('id', '=', $brancheod)->first();
                \Illuminate\Support\Facades\Log::info("yes FT");
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata);
                \Illuminate\Support\Facades\Log::info("after FT");
                $idEoddetail = DB::table('eoddetails')->insertGetId([
                    "eod_id" => $brancheod,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"],
                    "wallet" => $dataForEod["eodWallet"],
                    "creditac" => $dataForEod["eodcreditAccount"],
                    "opt" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $eoddetail_id = $idEoddetail;
            } else {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata);
                //print_r($dataForEod);
                DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->update([
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"],
                    "wallet" => $dataForEod["eodWallet"],
                    "creditac" => $dataForEod["eodcreditAccount"],
                    "opt" => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail = DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->first();
                //print_r($eoddetail);
                $eoddetail_id = $eoddetail->id;
            }

            if ($eoddetail_id != null) {
                $currentLoginOut = FuelFulltankReceipt::getFulltankCurrentLoginOut();
                DB::table('pshift')->where(
                    'id',
                    $currentLoginOut->shift_id
                )->update([
                    'eoddetails_id' => $eoddetail_id,
                ]);

                $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();
                Log::info('FT - PS : User Login Receipt Aggregate = ' . json_encode($dataForEod));
                //var_dump($dataForEod);
                if ($dataPshiftdetails == null) {
                    \Illuminate\Support\Facades\Log::info(["eoddetail_id" => "start"]);
                    DB::table('pshiftdetails')->insert([
                        "pshift_id" => $loginOut->shift_id,
                        "eoddetails_id" => $eoddetail_id,
                        "startdate" => date('Y-m-d'),
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"],
                        "wallet" => $dataForEod["eodWallet"],
                        "creditac" => $dataForEod["eodcreditAccount"],
                        "opt" => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    DB::table('pshiftdetails')->where('id', $dataPshiftdetails->id)->update([
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"],
                        "wallet" => $dataForEod["eodWallet"],
                        "creditac" => $dataForEod["eodcreditAccount"],
                        "opt" => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }


            //new code for EoD and personal shift.
            return $receipt->id;
        } catch (\Exception $e) {
            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);

            return $e;
        }
    }
    public function FTStoreFuelReceiptList($receipt, $dose, $filled)
    {

        Log::info('FT - storeFuelReceiptList: receipt=' . json_encode($receipt));
        Log::info('FT - storeFuelReceiptList: dose=' . $dose . ', filled=' . $filled);

        try {
            $fuel = $dose;
            DB::table('fuelfulltank_receiptlist')->insert([
                "fuel_fulltank_receipt_id" => $receipt->id,
                "fuel_fulltank_receipt_systemid" => $receipt->systemid,
                "pump_no" => $receipt->pump_no,
                "total" => $dose * 100,
                "fuel" => $dose * 100,
                "filled" => $filled,
                "refund" => 0,
                "refund_qty" =>  0,
                "status" => $receipt->status,
                "created_at" => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
        }
    }


    public function FTfuelReceipt(Request $request)
    {
        try {
            $location = Location::first();
            $company = Company::first();

            $receipt = FuelFulltankReceipt::where('id', $request->id)->first();
            $receiptdetails = FuelFulltankReceiptdetails::where('fulltank_receipt_id', $request->id)->first();

            // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuelfulltank_receiptproduct')->where('fulltank_receipt_id', $request->id)->get();

            // This is obsoleted. Get refund data from fuel_receiptlist table
            /*$refund = FuelFulltankReceiptList::where("fuel_fulltank_receipt_id", $request->id)->
                join('users', 'users.id', 'fuelfulltank_receiptlist.refund_staff_user_id')->
                first();
            */
            /*$receiptCount = DB::table('creditact')->
                    where('fue_receipt_id', $request->id)->
                    get()->count();*/

            $terminal = DB::table('terminal')->where('id', $receipt->terminal_id)->first();

            return view("fuel_fulltank.fuel_fulltank_receipt", compact(
                "location",
                "user",
                "receiptproduct",
                "terminal",
                "company",
                "receipt",
                "receiptdetails"
            ));
        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }
    public function ft_dataTable(Request $request)
    {
        try {
            $data = DB::table('fuelfulltank_receipt')->join('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', 'fuelfulltank_receipt.id')->selectRaw('fuelfulltank_receipt.status,
			fuelfulltank_receiptlist.id,
			fuelfulltank_receiptlist.fuel_fulltank_receipt_systemid,
			fuelfulltank_receiptlist.total,
			fuelfulltank_receiptlist.pump_no,
			fuelfulltank_receiptlist.fuel_fulltank_receipt_id AS receipt_id,
			fuelfulltank_receiptlist.created_at AS created_at')->whereNull('fuelfulltank_receiptlist.deleted_at')->whereDate('fuelfulltank_receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->orderBy('fuelfulltank_receiptlist.id', 'DESC')->get();

            return Datatables::of($data)->setRowId(function ($data) {
                return 'pump_receipt_data_' . $data->pump_no . '-' . $data->receipt_id;
            })->addIndexColumn()->addColumn('date', function ($data) {
                $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                return <<<EOD
					$created_at
EOD;
            })->addColumn('isrefunded', function ($data) {
                $systemid = ($data->status == "refunded") ? 1 : 0;
                return <<<EOD
					$systemid
EOD;
            })->addColumn('systemid', function ($data) {
                $systemid = !empty($data->fuel_fulltank_receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_fulltank_receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
					$systemid
EOD;
            })->addColumn('total', function ($data) {
                if ($data->status === "voided") {
                    $total = '0.00';
                } else {
                    $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                }
                return <<<EOD
					$total
EOD;
            })->addColumn('fuel', function ($data) {
                $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                return <<<EOD
					$total
EOD;
            })->editColumn('systemid', function ($data) {
                $systemid = !empty($data->fuel_fulltank_receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_fulltank_receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
                        $systemid
EOD;
            })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function ft_datatable_search_pump(Request $request)
    {
        try {
            $data = DB::table('fuelfulltank_receipt')->join('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', 'fuelfulltank_receipt.id')->selectRaw('fuelfulltank_receipt.status,
			fuelfulltank_receiptlist.id,
			fuelfulltank_receiptlist.fuel_fulltank_receipt_systemid,
			fuelfulltank_receiptlist.total,
			fuelfulltank_receiptlist.pump_no,
			fuelfulltank_receiptlist.fuel_fulltank_receipt_id AS receipt_id,
			fuelfulltank_receiptlist.created_at AS created_at')->whereNull('fuelfulltank_receiptlist.deleted_at')->where('fuelfulltank_receiptlist.pump_no', 'like', $request->pump_no . '%')->whereDate('fuelfulltank_receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->orderBy('fuelfulltank_receiptlist.id', 'DESC')->get();

            return $this->ft_process_datatable_search($data);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }



    public function ft_datatable_search_ptype(Request $request)
    {
        if (strpos($request->ptype, 'creditac') !== false) {
            $request->ptype = 'creditac';
        }

        try {
            $data = DB::table('fuelfulltank_receipt')->join('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', 'fuelfulltank_receipt.id')->selectRaw('fuelfulltank_receipt.status,
    			fuelfulltank_receiptlist.id,
    			fuelfulltank_receiptlist.fuel_fulltank_receipt_systemid,
    			fuelfulltank_receiptlist.total,
    			fuelfulltank_receiptlist.pump_no,
    			fuelfulltank_receiptlist.fuel_fulltank_receipt_id AS receipt_id,
    			fuelfulltank_receiptlist.created_at AS created_at')->whereNull('fuelfulltank_receiptlist.deleted_at')->where('fuelfulltank_receipt.payment_type', 'like', $request->ptype . '%')->whereDate('fuelfulltank_receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->orderBy('fuelfulltank_receiptlist.id', 'DESC')->get();

            return $this->ft_process_datatable_search($data);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }


    public function ft_process_datatable_search($data)
    {
        try {
            return Datatables::of($data)->setRowId(function ($data) {
                return 'pump_receipt_data_' . $data->pump_no . '-' . $data->receipt_id;
            })->addIndexColumn()->addColumn('date', function ($data) {
                $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                return <<<EOD
          $created_at
EOD;
            })->addColumn('isrefunded', function ($data) {
                $systemid = ($data->status == "refunded") ? 1 : 0;
                return <<<EOD
          $systemid
EOD;
            })->addColumn('systemid', function ($data) {
                $systemid = !empty($data->fuel_fulltank_receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_fulltank_receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
          $systemid
EOD;
            })->addColumn('total', function ($data) {
                if ($data->status === "voided") {
                    $total = '0.00';
                } else {
                    $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                }
                return <<<EOD
          $total
EOD;
            })->addColumn('fuel', function ($data) {
                $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                return <<<EOD
          $total
EOD;
            })->editColumn('systemid', function ($data) {
                $systemid = !empty($data->fuel_fulltank_receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_fulltank_receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
                        $systemid
EOD;
            })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (\Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }


    /* Fulltank Terminal Sync Data Insertion, Deletion and Fetch*/
    function nicehoe()
    {
        return response("WTF", 200);
    }
    function ftSyncData()
    {
        Log::debug('***** FT- Full tank syncData() *****');
        try {

            $data = [];
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            Log::debug('FT - syncData pump_no=' . request()->pump_no);
            Log::debug('FT - syncData terminal=' . $terminal->id);

            $data['master_terminal_id']    = $terminal->id;
            $data['product_id']        = 0;
            $data['pump_no']        = request()->pump_no;
            $data['payment_status'] = "Not Paid";
            $data['dose']            = 0.00;
            $data['price']            = 0.00;
            $data['litre']            = 0;
            $data['ft_receipt_id']    = null;
            //dd($data);
            Log::debug($data);

            $is_exist = DB::table('ft_mtermsync')->where([
                'pump_no'        => request()->pump_no
            ])->first();

            if (!empty($is_exist)) {
                DB::table('ft_mtermsync')->where([
                    'pump_no'        => request()->pump_no
                ])->update($data);
            } else {
                $data['created_at'] = now();
                $data['updated_at'] = now();
                DB::table('ft_mtermsync')->insert($data);
            }
        } catch (\Exception $e) {
            \Log::info([
                "msg"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);

            abort(404);
        }
    }


    function ftGetData()
    {
        //Log::debug('***** getData() *****');
        try {
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            $data =  DB::table('ft_mtermsync')->select('ft_mtermsync.*')->get();

            //Log::debug('getData: data='.json_encode($data));

            return response()->json($data);
        } catch (Exception $e) {
            Log::info([
                "msg"    => $e->getMessage(),
                "File"    => $e->getFile(),
                "Line"    => $e->getLine()
            ]);

            abort(404);
        }
    }


    function ftDeleteData()
    {
        Log::debug('***** FT - fulltank deleteData() *****');

        try {
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            Log::debug('FT - deleteData: pump_no=' . request()->pump_no);
            Log::debug('FT - deleteData: terminal=' . $terminal->id);


            DB::table('ft_mtermsync')->where([
                'master_terminal_id'    => $terminal->id,
                'pump_no' => request()->pump_no
            ])->delete();
        } catch (Exception $e) {
            Log::error([
                "msg"    => $e->getMessage(),
                "file"    => $e->getFile(),
                "line"    => $e->getLine()
            ]);
        }
    }
    /* Fulltank Terminal Sync Data Insertion, Deletion and Fetch*/
}
