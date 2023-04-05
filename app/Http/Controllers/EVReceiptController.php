<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EvreceiptCarpark;
use App\Models\ReceiptDetails;
use App\Models\ReceiptRefund;
use App\Models\FuelReceipt;
use App\Models\Terminal;
use App\Models\CarparklotSettingMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Location;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;
use App\Models\CarparkOper;
use App\Models\Evreceipt;
use App\Models\User;
use DB;
use Carbon\Carbon;

class EVReceiptController extends Controller
{

    function evList()
    {

        try {
            $ev = [];

            return view("ev_receipt.ev_receiptlist", compact("ev"));

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    function evListData(Request $request)
    {
        try {
            
            $data = DB::table('evreceipt')->
                join('evreceiptlist', 'evreceiptlist.evreceipt_id', 'evreceipt.id')->
                selectRaw('evreceipt.status,
            evreceiptlist.id,
            evreceiptlist.evreceipt_systemid,
            evreceiptlist.total,
            evreceiptlist.evreceipt_id AS receipt_id,
            evreceiptlist.created_at AS created_at')->
            orderBy('evreceiptlist.created_at', 'DESC')
                ->whereNull('evreceiptlist.deleted_at');
                
            if( $request->date ){
                $data = $data->whereDate('evreceiptlist.created_at', date('Y-m-d', strtotime($request->date)))->
                orderBy('evreceiptlist.created_at', 'DESC')
                ->take(10);
            }

            $data = $data->get();
            // echo "<pre>"; print_r($data); die;
            Log::info($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('evreceipt_systemid', function ($data) {
                    $systemid = !empty($data->evreceipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getEVReceiptDetail(' . $data->receipt_id . ')" > ' . $data->evreceipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
                    $systemid
EOD;
                })
                ->addColumn('date', function ($data) {
                $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                return <<<EOD
                    $created_at
EOD;
                })
                ->editColumn('total', function ($data) {

                    if ($data->status === "voided") {
                        $total = '0.00';
                    } else {
                        $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                    }

                    return $total;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a  href="javascript:void(0)"   disabled="disabled" style=" filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""> </a>';
                    return $btn;
                })
                ->addColumn('status_color', ' ')->
                    editColumn('status_color', function ($row) {
                    $status = "none";
                    if ($row->status === "voided") {
                        $status = "red";
                    }
                    if ($row->status === "refunded") {
                        $status = "#ff7e30";
                    }
                    return $status;

                })
                ->rawColumns(['action','evreceipt_systemid'])
                ->make(true);


        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    function evReceiptDetail(Request $request)
    {
        try {

            $user = Auth::user();
            $location = \App\Models\Location::first();
            $company = Company::first();

            $receipt = Evreceipt::with("staff_user")->where('id', $request->id)->first();
            $receiptdetails = DB::table('evreceiptdetails')->where('evreceipt_id', $request->id)->first();
            $receipt['user'] = User::query()->find($receipt->staff_user_id);
            // This is obsoleted. Get refund data from evreceiptlist table
            $refund = DB::table('evreceiptlist')->where("evreceipt_id", $request->id)->
                join('users', 'users.id', 'evreceiptlist.staff_user_id')->
                join('carpark_oper', 'carpark_oper.id', 'evreceiptlist.carpark_oper_id')->
                join('carparklot', 'carparklot.id', 'carpark_oper.carparklot_id')->
                first();

            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->find($receipt->terminal_id);

            return view("ev_receipt.ev_receipt", compact(
                "location", "user", "terminal", "company",
                "receipt", "receiptdetails", "refund"));

        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }

    function evReceipList(Request $request)
    {
        $date = !empty($request->date) ? $request->date : '';

        return view('ev_receipt.ev_receiptlist', compact('date'));
    }

    function getEVReceipID(Request $request)
    {
        $client_ip  = request()->ip();
        $terminal   = DB::table('terminal')->where('client_ip', $client_ip)->first();
        if( !empty($terminal) ){
            return [
                'ev_receipt_id'=>Systemid::evreceipt_system_id($terminal->id),
                'ev_receipt_date' => date('dMy H:i:s'),
            ];
        }
        return '';
    }

    public function CreateEVList(Request $request)
    {
        try {

            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->
                where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = \App\Models\Location::first();
            $systemid = $request->ev_receipt_id;
            $pump_hardware = DB::table('local_pump')->
                where("pump_no", $request->pump_no)->first();
            

            $ev_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $receipt = new Evreceipt();
            $receipt->systemid = $systemid;

            $receipt->hours     = $request->hours;
            $receipt->rate      = ($request->rate ?? 0) * 100;
            $receipt->kwh       = ($request->kwh ?? 0) * 100;
            $receipt->ev_mode   = $ev_mode;

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

            $receipt->staff_user_id = $user->id;
            $receipt->company_id = $company->id;
            $receipt->company_name = $company->name;
            $receipt->gst_vat_sst = $company->gst_vat_sst;
            $receipt->business_reg_no = $company->business_reg_no;
            $receipt->receipt_logo = $company->corporate_logo;
            $receipt->receipt_address = $company->office_address;

            $currencyarr = DB::table('currency')->
                where('id', $company->currency_id)->
                orderBy('code')->get()->first();

            $receipt->currency = $currencyarr->code ?? 'MYR';

            $receipt->status = "active";
            $receipt->remark = "NULL";
            $receipt->transacted = "pos";

            $receipt->pump_id = 0;
            $receipt->pump_no = 0;

            $receipt->transacted = "pos";
            $receipt->save();

            Log::debug('CreateEVReceiptList: $receipt = ' . json_encode($receipt));

            // Store Data in EV Receiptlist Table
            $this->storeEVReceiptList($receipt, $request);

            $receiptproductsdiscount = 0;
            $cash_received = 0;
            $creditcard = $wallet = $creditac = 0;
            $creditac = 0;
            if ($receipt->payment_type == "cash") {
                $cash_received = ($request->cash_received ?? 0) * 100;
            } elseif ($receipt->payment_type == "wallet") {
                $wallet = $request->total * 100;
            } elseif ($receipt->payment_type == "creditac") {
                $creditac = $request->total * 100;
            } else {
                $creditcard = $request->total * 100;
            }

            // Insert values to evreceiptdetails table
            DB::table('evreceiptdetails')->insert([
                "evreceipt_id" => $receipt->id,
                "total" => $request->total * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->itemAmount * 100,
                "sst" => $request->sst * 100,
                "discount" => $receiptproductsdiscount * 100,
                "cash_received" => $cash_received,
                "change" => $request->change_amount * 100,
                "creditcard" => $creditcard,
                "wallet" => $wallet,
                "creditac" => $creditac,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            // Insert values to evreceiptcarparklot table
            DB::table('evreceiptcarparklot')->insert([
                "evreceipt_id" => $receipt->id,
                "carpark_oper_id" => $request->carparkoper_id,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            // update values to carpark_oper table
            DB::table('carpark_oper')->where('carparklot_id', $request->carparklot_id)
            ->update([
                "status" => 'paid',
                "amount" => $request->itemAmount * 100,
                "payment" => $request->total * 100,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            // EOD Details
            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->first();

            \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

            $loginOut = Evreceipt::getCurrentLoginOut();
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->first();
            $eoddetail_id = null;

            if ( empty($brancheoddata) ) {
                $brancheod = DB::table('brancheod')->insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $brancheoddata = DB::table('brancheod')->
                    where('id', '=', $brancheod)->first();
                \Illuminate\Support\Facades\Log::info("yes");
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata);
                \Illuminate\Support\Facades\Log::info("after");

                $idEoddetail = DB::table('eoddetails')->
                    insertGetId([
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
                DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    update([
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

                $eoddetail = DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->first();

                $eoddetail_id = $eoddetail->id;
            }
            // EOD Details

            // PShift Details
            if ($eoddetail_id != null) {
                $currentLoginOut = Evreceipt::getCurrentLoginOut();
                DB::table('pshift')->where(
                    'id', $currentLoginOut->shift_id
                )->update([
                    'eoddetails_id' => $eoddetail_id,
                ]);

                $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();

                if ($dataPshiftdetails == null) {
                    \Illuminate\Support\Facades\Log::info(["eoddetail_id" => "start"]);

                    DB::table('pshiftdetails')->
                        insert([
                        "pshift_id" => $loginOut->shift_id,
                        "eoddetails_id" => $eoddetail_id,
                        "startdate" => date('Y-m-d H:i:s'),
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
                    DB::table('pshiftdetails')->
                        where('id', $dataPshiftdetails->id)->
                        update([
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
            // PShift Details
            $data = array();
            $data['evreceipt'] = DB::table('evreceipt')->whereId($receipt->id)->first();
            $data['evreceiptdetails'] = DB::table('evreceiptdetails')->where('evreceipt_id',$receipt->id)->first();
            $data['evreceiptlist'] = DB::table('evreceiptlist')->where('evreceipt_id',$receipt->id)->first();

            $query = "select t.systemid from terminal t, evreceipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-ev-receipt' , json_encode($data));

            $carparkOperas = app('App\Http\Controllers\CarparkController')->getCustomCarParkOpera();
            $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
            $transaction_count = CarparkOper::count();
            $paid_count = CarparkOper::query()->where('status', 'paid')->count();
            return view("carpark.carparklot_table", compact("carparkOperas","current_setting_mode",
            'stop_count','transaction_count','paid_count'));

        } catch (\Exception $e) {

            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            return $e;
        }
    }

    public function syncTest()
    {
        $receipt = new Evreceipt();
        $receipt->id =1;
        $data = array();
            $data['evreceipt'] = DB::table('evreceipt')->whereId($receipt->id)->first();
            $data['evreceiptdetails'] = DB::table('evreceiptdetails')->where('evreceipt_id',$receipt->id)->first();
            $data['evreceiptlist'] = DB::table('evreceiptlist')->where('evreceipt_id',$receipt->id)->first();

            $query = "select t.systemid from terminal t, evreceipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            // var_dump($data['evreceipt']);
            // die;

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-ev-receipt' , json_encode($data));
            return $response_sync;
    }

    /* Store New Transaction */
    public function storeEVReceiptList($receipt, $request)
    {
        Log::info('storeEVReceiptList: receipt=' . json_encode($receipt));

        try {

            DB::table('evreceiptlist')->insert([
                "evreceipt_id" => $receipt->id,
                "evreceipt_systemid" => $receipt->systemid,
                "staff_user_id" => $receipt->staff_user_id,
                "carpark_oper_id" => $request->carparkoper_id,
                "total" => $request->total * 100,
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

    public function voidedReceipt(Request $request)
    {
        // echo "<pre>";
        //     print_r($request->all());
        // die;
        try {
            Log::debug('voidedReceipt: receipt_id=' . $request->receipt_id);

            $ev_receipt = Evreceipt::find($request->receipt_id);

            Log::debug('voidedReceipt: receipt=' . json_encode($ev_receipt));

            $ev_receipt->status = "voided";
            $ev_receipt->voided_at = now();
            $ev_receipt->voided_at = now();
            $ev_receipt->void_user_id = Auth::user()->id;
            $ev_receipt->save();
            
            DB::table('evreceiptlist')->where('evreceipt_id', $request->receipt_id)
            ->update([
                'status' => 'voided',
            ]);

            // EOD Details
            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->first();

            \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

            $loginOut = Evreceipt::getCurrentLoginOut();
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->first();
            $eoddetail_id = null;

            if ( empty($brancheoddata) ) {
                $brancheod = DB::table('brancheod')->insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $brancheoddata = DB::table('brancheod')->
                    where('id', '=', $brancheod)->first();
                \Illuminate\Support\Facades\Log::info("yes");
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata);
                \Illuminate\Support\Facades\Log::info("after");

                $idEoddetail = DB::table('eoddetails')->
                    insertGetId([
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
                DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    update([
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

                $eoddetail = DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->first();

                $eoddetail_id = $eoddetail->id;
            }
            // EOD Details

            // PShift Details
            if ($eoddetail_id != null) {
                $currentLoginOut = Evreceipt::getCurrentLoginOut();
                DB::table('pshift')->where(
                    'id', $currentLoginOut->shift_id
                )->update([
                    'eoddetails_id' => $eoddetail_id,
                ]);

                $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();

                if ($dataPshiftdetails == null) {
                    \Illuminate\Support\Facades\Log::info(["eoddetail_id" => "start"]);

                    DB::table('pshiftdetails')->
                        insert([
                        "pshift_id" => $loginOut->shift_id,
                        "eoddetails_id" => $eoddetail_id,
                        "startdate" => date('Y-m-d H:i:s'),
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
                    DB::table('pshiftdetails')->
                        where('id', $dataPshiftdetails->id)->
                        update([
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
            // PShift Details

            return ["data" => $ev_receipt, 'error' => false];

        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
                'error' => false,
            ]);
            return ["data" => [], 'error' => false];
        }

    }
}

