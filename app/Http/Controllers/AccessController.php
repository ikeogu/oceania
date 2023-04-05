<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FuelReceipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Log;

class AccessController extends Controller
{

    public function __construct()
    {
    }

    protected $landing_page = '/onehost-opossum';
    protected $mobile_landing_page = '/mob-audited-rpt';

    public function landing()
    {
        Log::info('***** landing() *****');

        $company = Company::first();
        $agent = new Agent();

        $server_ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'];
        $client_ip = request()->ip();

        $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

        /*
        Log::debug('landing:client_ip='.$client_ip);
        Log::debug('landing:terminal='.json_encode($terminal));
        Log::debug('landing:request()->all='.json_encode(request()->all()));
         */

        /*
         * Add here && false to access fuel page
         * LIKE: $isServerEnd = $server_ip == $client_ip && false;
         */

        Log::info([
            "server_ip" => $server_ip,
            "client_ip" => $client_ip,
        ]);

        $isServerEnd = $server_ip == $client_ip;

        $isLocationActive = DB::table('lic_locationkey')->
            where('has_setup', 1)->first();
        Log::debug('isLocationActive=' . json_encode($isLocationActive));

        $detectHardware = app('App\Http\Controllers\SetupController')->
            getMacLinux();

        Log::debug('detectHardware=' . json_encode($detectHardware));

        $verifyHardware = DB::table('serveraddr')->where([
            'ip_addr' => $server_ip,
            'hw_addr' => $detectHardware,
        ])->get();

        Log::info('verifyHardware=' . json_encode($verifyHardware));

        Log::info([
            'ip_addr' => $server_ip,
            'hw_addr' => $detectHardware,
            'verifyHardware' => !empty($verifyHardware),
        ]);

        $isTerminalActive = DB::table('lic_terminalkey')->where([
            'terminal_id' => $terminal->id ?? 0,
            'has_setup' => 1,
        ])->first();

        Log::debug('isTerminalActive=' . json_encode($isTerminalActive));

        if ($isServerEnd || empty($isLocationActive)) {

            if (empty($isLocationActive)) {
                $return = view(
                    'oceania_svr.landing.login',
                    compact(
                        'company',
                        'isLocationActive',
                        'isTerminalActive',
                        'isServerEnd',
                        'verifyHardware',
                        'agent'
                    )
                );
            } elseif (!empty(Auth::user())) {
                $return = view(
                    'oceania_svr.landing.landing',
                    compact('verifyHardware')
                );
            } else {
                $return = view(
                    'oceania_svr.landing.login',
                    compact(
                        'company',
                        'isLocationActive',
                        'isTerminalActive',
                        'isServerEnd',
                        'verifyHardware',
                        'agent'
                    )
                );
            }
        } else {
            $pump_hardware = DB::table('local_pump')->join(
                'local_controller',
                'local_controller.id',
                '=',
                'local_pump.controller_id'
            )->select('local_controller.ipaddress', 'local_pump.pump_no')->
                get()->unique('pump_no');

            $nozzleFuelData = DB::table('local_pumpnozzle')->
                join('local_pump', 'local_pump.id', '=', 'local_pumpnozzle.pump_id')->
                join('local_controller', 'local_controller.id', '=', 'local_pump.controller_id')->
                join('prd_ogfuel', 'prd_ogfuel.id', '=', 'local_pumpnozzle.ogfuel_id')->
                whereNull('local_pumpnozzle.deleted_at')->
                whereNull('local_pump.deleted_at')->
                whereNull('local_controller.deleted_at')->
                select('prd_ogfuel.product_id', 'local_pump.pump_no', 'local_pumpnozzle.nozzle_no')->
                get();

            $productData = DB::table('product')->
                whereptype('oilgas')->get();

            Log::info('AccessController@landing: productData=' .
                json_encode($productData));

            $return = view('landing.landing', compact(
                'company',
                'pump_hardware',
                'productData',
                'terminal',
                'nozzleFuelData',
                'isLocationActive',
                'isServerEnd',
                'verifyHardware',
                'isTerminalActive'
            ));
        }

        Log::debug('AccessController@landing: return=' .
            json_encode($return));

        return $return ?? '';
    }

    public function landingTemp()
    {
        $company = $isLocationActive = Company::first();

        $client_ip = request()->ip();
        $terminal = $isTerminalActive = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $isServerEnd = false;

        if (env('ONLY_ONE_HOST')) {
            $isTerminalActive = true;
        } else {
            $isTerminalActive = DB::table('lic_terminalkey')->where([
                'terminal_id' => $terminal->id ?? 0,
                'has_setup' => 1,
            ])->first();
        }

        $pump_hardware = DB::table('local_pump')->
            join('local_controller', 'local_controller.id', '=', 'local_pump.controller_id')->
            select('local_controller.ipaddress', 'local_pump.pump_no')->
            get()->unique('pump_no');

        $nozzleFuelData = DB::table('local_pumpnozzle')->
            join('local_pump', 'local_pump.id', '=', 'local_pumpnozzle.pump_id')->
            join('local_controller', 'local_controller.id', '=', 'local_pump.controller_id')->
            join('prd_ogfuel', 'prd_ogfuel.id', '=', 'local_pumpnozzle.ogfuel_id')->
            whereNull('local_pumpnozzle.deleted_at')->
            whereNull('local_pump.deleted_at')->
            whereNull('local_controller.deleted_at')->
            select('prd_ogfuel.product_id', 'local_pump.pump_no', 'local_pumpnozzle.nozzle_no')->get();

        $productData = DB::table('product')->
            where('ptype', 'oilgas')->get();

        $detectHardware = app('\App\Http\Controllers\SetupController')->
            getMacLinux();

        $verifyHardware = DB::table('serveraddr')->where([
            'ip_addr' => $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'],
            'hw_addr' => $detectHardware,
        ])->get();

        return view('landing.landing', compact(
            'company',
            'pump_hardware',
            'productData',
            'terminal',
            'nozzleFuelData',
            'isLocationActive',
            'isServerEnd',
            'verifyHardware',
            'isTerminalActive'
        ));
    }

    public function h2PumpInfo(Request $request)
    {
        $selected_pump = $request->selected_pump;

        $pump_details = DB::table('local_h2pump')->
            join('local_h2pumpnozzle', 'local_h2pumpnozzle.pump_id', '=', 'local_h2pump.id')->
            join('h2_localfuelprice', 'h2_localfuelprice.id', '=', 'local_h2pumpnozzle.h2fuel_id')->
            join('prd_h2fuel', 'h2_localfuelprice.h2fuel_id', '=', 'prd_h2fuel.id')->
            join('product', 'product.id', '=', 'prd_h2fuel.product_id')->
            select('h2_localfuelprice.*', 'h2_localfuelprice.price', 'prd_h2fuel.product_id', 'product.name as product_name')->
            where('local_h2pump.pump_no', $selected_pump)->get();

        $pump_data = [];
        foreach ($pump_details as $key => $value) {
            $pump_data[$value->product_id] = [
                'product_name' => $value->product_name,
                'price' => $value->price,
                'display_price' => number_format(($value->price ?? 0) / 100, 2),
            ];
        }

        return $pump_data;
    }

    public function one_host_landing_h2()
    {
        $company = $isLocationActive = Company::first();

        // Protect against table company truncation
        if (!empty($company)) {
            $currencyarr = DB::table('currency')->
                where('id', $company->currency_id)->
                orderBy('code')->get()->first();
        }

        $currency = $currencyarr->code ?? 'MYR';

        $client_ip = request()->ip();
        $terminal = $isTerminalActive = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $isServerEnd = false;

        if (env('ONLY_ONE_HOST')) {
            $isTerminalActive = true;
        } else {

            $isTerminalActive = DB::table('lic_terminalkey')->
                where([
                'terminal_id' => $terminal->id ?? 0,
                'has_setup' => 1,
            ])->first();
        }

        $ONLY_ONE_HOST = true;
        Log::info([
            'terminal_id' => $terminal->id ?? null,
            'isTerminalActive' => !empty($isTerminalActive),
        ]);

        $pump_hardware = DB::table('local_h2pump')->
            join('local_controller', 'local_controller.id', '=', 'local_h2pump.controller_id')->
            select('local_controller.ipaddress', 'local_h2pump.pump_no')->
            get()->unique('pump_no');

        $nozzleFuelData = DB::table('local_h2pumpnozzle')->
            join('local_h2pump', 'local_h2pump.id', '=', 'local_h2pumpnozzle.pump_id')->
            join('local_controller', 'local_controller.id', '=', 'local_h2pump.controller_id')->
            join('prd_h2fuel', 'prd_h2fuel.id', '=', 'local_h2pumpnozzle.h2fuel_id')->
            whereNull('local_h2pumpnozzle.deleted_at')->
            whereNull('local_h2pump.deleted_at')->
            whereNull('local_controller.deleted_at')->
            select('prd_h2fuel.product_id', 'local_h2pump.pump_no', 'local_h2pumpnozzle.nozzle_no')->get();

        // $productData = DB::table('product')->where('ptype','h2')->get();
        $productData = DB::table('product')->
            join('prd_h2fuel', 'prd_h2fuel.product_id', '=', 'product.id')->
            where('product.ptype', 'h2')->get(
            [
                "prd_h2fuel.id as h2fuel_id",
                "prd_h2fuel.product_id",
                "prd_h2fuel.kg",
                "prd_h2fuel.price",
                "prd_h2fuel.upper_price",
                "prd_h2fuel.lower_price",
                "prd_h2fuel.wholesale_price",
                "prd_h2fuel.status",
                "prd_h2fuel.loyalty",
                "product.*",
            ]
        );

        $detectHardware = app('\App\Http\Controllers\SetupController')->
            getMacLinux();

        $verifyHardware = DB::table('serveraddr')->where([
            'ip_addr' => $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'],
            'hw_addr' => $detectHardware,
        ])->get();

        $fuel_grade_string = app('\App\Http\Controllers\LocalFuelController')->
            fuelgradesConfig();

        Log::debug('$fuel_grade_string=' . json_encode($fuel_grade_string));

        return view('h2.h2_landing', compact(
            'currency',
            'company',
            'pump_hardware',
            'productData',
            'terminal',
            'nozzleFuelData',
            'isLocationActive',
            'isServerEnd',
            'verifyHardware',
            'isTerminalActive',
            'fuel_grade_string',
            'ONLY_ONE_HOST'
        ));
    }

    public function one_host_landing()
    {

        Log::info('***** one_host_landing() *****');

        $company = Company::first();
        $agent = new Agent();

        $isLocationActive = DB::table('lic_locationkey')->
            where('has_setup', 1)->first();

        // Protect against table company truncation
        if (!empty($company)) {
            $currencyarr = DB::table('currency')->
                where('id', $company->currency_id)->
                orderBy('code')->get()->first();
        }

        $currency = $currencyarr->code ?? 'MYR';

        $client_ip = request()->ip();
        $terminal = $isTerminalActive = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $isServerEnd = false;

        if (env('ONLY_ONE_HOST')) {
            $isTerminalActive = true;
        } else {

            $isTerminalActive = DB::table('lic_terminalkey')->where([
                'terminal_id' => $terminal->id ?? 0,
                'has_setup' => 1,
            ])->first();
        }

        $ONLY_ONE_HOST = true;
        Log::info([
            'terminal_id' => $terminal->id ?? null,
            'isTerminalActive' => !empty($isTerminalActive),
            'isLocationActive' => !empty($isLocationActive),
        ]);

        $pump_hardware = DB::table('local_pump')->
            join('local_controller', 'local_controller.id', '=', 'local_pump.controller_id')->
            select('local_controller.ipaddress', 'local_pump.pump_no')->
            get()->unique('pump_no');

        $nozzleFuelData = DB::table('local_pumpnozzle')->
            join('local_pump', 'local_pump.id', '=', 'local_pumpnozzle.pump_id')->
            join('local_controller', 'local_controller.id', '=', 'local_pump.controller_id')->
            join('prd_ogfuel', 'prd_ogfuel.id', '=', 'local_pumpnozzle.ogfuel_id')->
            whereNull('local_pumpnozzle.deleted_at')->
            whereNull('local_pump.deleted_at')->
            whereNull('local_controller.deleted_at')->
            select('prd_ogfuel.product_id', 'local_pump.pump_no', 'local_pumpnozzle.nozzle_no')->get();

        $productData = DB::table('product')->
            where('product.ptype', 'oilgas')->
            join('prd_ogfuel', 'prd_ogfuel.product_id', 'product.id')->
            select('product.*', 'prd_ogfuel.id as og_id')->get();

        $productData->map(function ($f) {
            $price = 0;
            $f->price = app('\App\Http\Controllers\LocalFuelController')->
                getControllerPrice($f->og_id);

            if ($price == 0) {
                $price = app('App\Http\Controllers\LocalFuelController')->
                    getPrice($f->og_id);
            }
            $f->price = $price;
        });

        $detectHardware = app('\App\Http\Controllers\SetupController')->
            getMacLinux();

        Log::debug('detectHardware=' . json_encode($detectHardware));

        $server_ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'];

        Log::debug('server_ip=' . $server_ip);

        $verifyHardware = DB::table('serveraddr')->
			where('ip_addr', $server_ip)->
			where('hw_addr', $detectHardware)->
			get();

        Log::debug('verifyHardware=' . json_encode($verifyHardware));

        Log::info([
            'ip_addr' => $server_ip,
            'hw_addr' => $detectHardware,
            'verifyHardware' => !empty($verifyHardware),
        ]);

        $fuel_grade_string = app('\App\Http\Controllers\LocalFuelController')->
            fuelgradesConfig();

        Log::debug('$fuel_grade_string=' . json_encode($fuel_grade_string));
        if ($agent->isMobile()) {
            return view('landing.mob_landing', compact(
                'currency',
                'company',
                'pump_hardware',
                'productData',
                'terminal',
                'nozzleFuelData',
                'isLocationActive',
                'isServerEnd',
                'verifyHardware',
                'isTerminalActive',
                'fuel_grade_string',
                'ONLY_ONE_HOST',
                'agent'
            ));
        }

        return view('landing.landing', compact(
            'currency',
            'company',
            'pump_hardware',
            'productData',
            'terminal',
            'nozzleFuelData',
            'isLocationActive',
            'isServerEnd',
            'verifyHardware',
            'isTerminalActive',
            'fuel_grade_string',
            'ONLY_ONE_HOST',
            'agent'
        ));
    }

    public function authorizeUser(Request $request)
    {
        $user = User::where('access_code', $request->access_code)->first();
        if ($user) {
            $useru = User::find($user->id);
            $useru->last_login = now();
            $useru->save();
            Auth::loginUsingId($user->id);

            if ($useru->status != 'active' && env('ONLY_ONE_HOST') == true) {
                $return = ['login_error' => 'Unable to login, user is inactive'];
            } else {
                $return = ["landing" => url($this->landing_page)];
            }

        } else {
            $return = false;
        }

        return $return;
    }

    public function uPLogin(Request $request)
    {
        Log::debug('uPLogin: all()=' . json_encode($request->all()));

        $server_ip = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'];

        $request->session()->flash('form', 'login');

        $credentials = $request->only('email', 'password');

        $client_ip = request()->ip();

        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $user = DB::table('users')->
            where('email', $request->email)->first();

        // check if user is already logged in
		try {
			$loginout = DB::table('loginout')->
				where('user_id', $user->id)->
				whereNull('logout')->
				first();

		} catch (\Exception $e) {
            session()->flash('login_error', 'Invalid username and password');
            return redirect($this->landing_page);
		}


        if (empty($loginout) && Auth::attempt($credentials, true)) {
            // Authentication passed...
            // return redirect()->intended($this->landing_page);
            $useru = User::find(Auth::id());
            $useru->last_login = now();
            $useru->save();
            Log::debug("Logged In as: " . json_encode($useru));
            Log::debug("Logged In as " . json_encode(Auth::user()));
        }

        if (!empty($useru)) {
            Log::debug('Hosting: ' . $request->hosting);
            if ($useru->status != 'active') {
                Auth::logout();

                Log::info('uPLogin: AFTER Auth::logout()');

                // $return = ['login_error' =>
                //     'Unable to login, user is inactive'];
                session()->flash('login_error', 'Unable to login, user is inactive');
            } else {
                // Logic to present different landing pages to different
                // users by IPADDR
                switch ($terminal->client_ip) {
                    // For station manager who gets Screen D directly
                    case env('STATION_MGR_IPADDR'):
                        $return = ["landing" => url('/screen_d')];
                        break;

                    default:
                        $agent = new Agent();

                        if ($agent->isMobile()) {
                            Log::debug('uPLogin: MOBILE LOGIN DETECTED!');
                            $return = ["landing" =>
                                url($this->mobile_landing_page)];
                        } else {
                            // Standard OPOSsum POS cashier users
                            Log::debug('uPLogin: Regular OPOSsum POS login');
                            $return = ["landing" =>
                                url($this->landing_page)];
                        }
                }
            }

			if (!empty($terminal)) {
				if (!empty($detectHardware)) {
					Log::info(["detectHardware" => $detectHardware]);
					if ($terminal->hw_addr != $detectHardware[0]['MAC']) {
						Auth::logout();
						$return = ['login_error' =>
							'Unable to login, please contact your administrator'];
					}
				}
			}

        } elseif (!empty($loginout)) {
            // $return = ['login_error' => 'This account is already in use. Please log in with another user ID.'];

            session()->flash('login_error',
				'This account is already in use. Please log in with another user ID.');
            return redirect($this->landing_page);

        } else {
            // $return = ['login_error' => 'Invalid username and password'];

            session()->flash('login_error', 'Invalid username and password');
            return redirect($this->landing_page);
        }

        if (isset($return['landing'])) {
            $this->loginlogout('login');
            Log::debug("populated loginout table");
        }

        // return $return;
        Log::debug("Value of Auth:: " . json_encode(Auth::user()));
        return redirect($this->landing_page);
    }

    public function uLoginView(Request $request)
    {
        return redirect()->route('main.view.onehost');
    }

    public function logout(Request $request)
    {
        // $this->loginlogout('logout');
        $location = DB::table('location')->first();
        $currentLoginOut = FuelReceipt::getCurrentLoginOut();
        Log::info('LO loginlogout: $currentLoginOut=' .
            json_encode($currentLoginOut));

        if (!empty($currentLoginOut)) {
            DB::table('pshift')->where(
                'id',
                $currentLoginOut->shift_id
            )->update([
                'endpshift_presser_user_id' =>
                $currentLoginOut->user_id,
            ]);
        }

        if (Auth::check()) {

            DB::table('loginout')->where([
                'location_id' => $location->id,
                'user_id' => Auth::user()->id,
            ])->whereNull('logout')->update([
                'logout' => now(),
            ]);

            DB::table('nshift')->where([
                'staff_systemid' => Auth::user()->systemid,
            ])->whereNull('out')->update([
                'out' => date('Y-m-d H:i:s'),
            ]);
        }

        Auth::logout();

        \Session::flush();

        // Log::debug('Key' . $request->session()->get('key'));
        // $session = $request->session()->get('key');
        // $request->session()->forget('key');

        /*
        if($session == 'opossum') {
			return redirect('/onehost-opossum');
        } else {
			return redirect('/');
        }
         */

        return redirect('/onehost-opossum');
    }

    public function loginlogout($type)
    {
        $location = DB::table('location')->first();

        if ($type == 'login') {
            $lastlogin = DB::table('loginout')->
                where('user_id', Auth::user()->id)->
                latest()->first();

            /*
            Log::info('LO loginlogout: '.$type.', $lastlogin='.
            json_encode($lastlogin));

            Log::info('LO loginlogout: Auth::user()->id='.
            Auth::user()->id);
             */

            // Protect from empty loginout record
            // if (!empty($lastlogin)) {
            //     DB::table('loginout')->
            //         where('user_id', Auth::user()->id)->
            //         whereNull('logout')->update([

            //         'logout' => date(
            //             "Y-m-d 23:59:59",
            //             strtotime($lastlogin->login)
            //         ),
            //         'updated_at' => now(),
            //     ]);
            // }

            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->
                where('client_ip', $client_ip)->first();

            $idShift = DB::table('pshift')->insertGetId([
                'terminal_id' => $terminal->id,
                'location_id' => $location->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('loginout')->insert([
                'login' => now(),
                'location_id' => $location->id,
                'user_id' => Auth::user()->id,
                'shift_id' => $idShift,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user = DB::table('users')
                ->where('id', Auth::user()->id)
                ->first();
            DB::table('nshift')->insert([
                'staff_systemid' => Auth::user()->systemid,
                'staff_name' => $user->fullname,
                'in' => date('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } elseif ($type == 'logout') {
            $currentLoginOut = FuelReceipt::getCurrentLoginOut();

            Log::info('LO loginlogout: $currentLoginOut=' .
                json_encode($currentLoginOut));

            if (!empty($currentLoginOut)) {
                DB::table('pshift')->where(
                    'id',
                    $currentLoginOut->shift_id
                )->update([
                    'endpshift_presser_user_id' =>
                    $currentLoginOut->user_id,
                ]);
            }

            if (Auth::check()) {
                DB::table('loginout')->where([
                    'location_id' => $location->id,
                    'user_id' => Auth::user()->id,
                ])->whereNull('logout')->update([
                    'logout' => now(),
                ]);
                DB::table('nshift')->where([
                    'staff_system_id' => Auth::user()->id,
                ])->whereNull('out')->update([
                    'out' => now(),
                ]);
            }

        }
    }

    public function log2laravel(Request $request)
    {
        $level = $request->level;
        $string = $request->string;

        switch ($level) {
            case 'error':
                Log::error($string);
                break;
            case 'debug':
                Log::debug($string);
                break;
            case 'info':
            default:
                Log::info($string);
        }
    }

    public function postDateToOceania(Request $request)
    {
        $response = array('response' => 'Data inserted', 'success' => true);

        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:oneway',
            'self_merchant_id' => 'required',
            'company_name' => 'required',
            'business_reg_no' => 'required',
            'address' => 'required',
            'contact_name' => 'required',
            'mobile_no' => 'required',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $id = DB::table('oneway')->insertGetId([
                'id' => $request->input('id'),
                'self_merchant_id' => $request->input('self_merchant_id'),
                'company_name' => $request->input('company_name'),
                'business_reg_no' => $request->input('business_reg_no'),
                'address' => $request->input('address'),
                'contact_name' => $request->input('contact_name'),
                'mobile_no' => $request->input('mobile_no'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');

            DB::table('onewayrelation')->insert([
                'oneway_id' => $id,
                'status' => 'active',
                'ptype' => 'dealer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('onewaylocation')->insert([
                'oneway_id' => $id,
                'location_id' => $request->input('location_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $response;
    }

    public function postDateToOceaniatwoway(Request $request)
    {
        Log::debug("data = " . json_encode($request->all()));
        //$response = "yes";
        $response = array('response' => 'Data inserted', 'success' => true);

        $validator = Validator::make($request->all(), [
            'id' => 'required|unique:merchantlink',
            'initiator_user_id' => 'required',
            'responder_user_id' => 'required',
            'selfMerchantId' => 'required',
        ]);

        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $merchantlink_id = DB::table('merchantlink')->insertGetId([
                'id' => $request->input('id'),
                'responder_user_id' => $request->input('responder_user_id'),
                'initiator_user_id' => $request->input('initiator_user_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');

            if (
                DB::table('company')->whereId($request->input('company_id'))->count() == 0
            ) {

                $company_id = DB::table('company')->insertGetId([
                    'id' => $request->input('company_id'),
                    'name' => $request->input('name'),
                    'business_reg_no' => $request->input('business_reg_no'),
                    'systemid' => $request->input('systemid'),
                    'corporate_logo' => $request->input('corporate_logo'),
                    'owner_user_id' => $request->input('owner_user_id'),
                    'gst_vat_sst' => $request->input('gst_vat_sst'),
                    'currency_id' => $request->input('currency_id'),
                    'office_address' => $request->input('office_address'),
                    'status' => $request->input('status'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id');
            }

            $twoway_id = DB::table('merchantlinkrelation')->insertGetId([
                'company_id' => $company_id,
                'merchantlink_id' => $merchantlink_id,
                'default_location_id' => $request->input('location_id'),
                'ptype' => 'dealer',
                'created_at' => now(),
                'updated_at' => now(),

            ], 'id');

            $twoway_id = DB::table('twoway')->insertGetId([
                'initiator_user_id' => $request->input('initiator_user_id'),
                'responder_user_id' => $request->input('responder_user_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');

            $merchantrelation_id = DB::table('merchantrelation')->insertGetId([
                'self_merchant_id' => $request->input('selfMerchantId'),
                'twoway_id' => $twoway_id,
                'partner_merchant_id' => $request->input('responder_user_id'),
                'partner_oneway_id' => 0,
                'is_dealer' => true,
                'default_location_id' => $request->input('location_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id');
        }

        return $response;
        //Log::info($request);;
    }
}
