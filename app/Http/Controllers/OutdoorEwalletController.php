<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Company;
use App\Models\OEWReceipt;
use App\Models\OEWReceiptdetails;
use App\Models\OEWReceiptList;
use App\Models\Location;
use App\Models\User;
use App\Models\FuelReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;
use App\Http\Controllers\SyncSalesController;


class OutdoorEwalletController extends Controller
{
    public function OutdoorEwalletList (Request $request){
      $date = isset($request->date)?$request->date:"";
         return view('outdoor_ewallet.oew_receiptlist', compact('date'));
    }

    public function OutdoorEWalletProviderInformation(){

         return view('outdoor_ewallet.outdoor_e_wallet_provider_information');
    }

    public function syncOEW(Request $request)
    {
        try {
            $receipt = new FuelReceipt();
            $receipt->id = 1;

            //Sync tables
            $data = array();
            $data['oew_receipt'] = DB::table('oew_receipt')->whereId($receipt->id)->first();
            $data['oew_receiptdetails'] = DB::table('oew_receiptdetails')->where('receipt_id',$receipt->id)->first();
            $data['oew_receiptproduct'] = DB::table('oew_receiptproduct')->where('receipt_id',$receipt->id)->first();
            $data['oew_receiptlist'] = DB::table('oew_receiptlist')->where('oew_receipt_id',$receipt->id)->first();

            $query = "select t.systemid from terminal t, oew_receipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));
          //  return $data;

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-oew-receipt' , json_encode($data));
            return $response_sync;

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

    public function CreateOeWList(Request $request)
    {
      Log::debug('***** CreateOeWList(): '.json_encode($request->all()));
        try {
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->
                where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $systemid = Systemid::fuelreceipt_system_id($terminal->id);
            $pump_hardware = DB::table('local_pump')->
                where("pump_no", $request->pump_no)->first();
            $receipt = new FuelReceipt();
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

            $receipt->pump_id = $pump_hardware->id;
            $receipt->pump_no = $request->pump_no;

            $receipt->transacted = "pos";
            $receipt->save();

            Log::debug('CreateOeWList: $receipt = ' . json_encode($receipt));

            // Store Data in Fuel Receiptlist Table
            $this->storeOeWReceiptList($receipt, $request->dose, $request->filled);

            $receiptproductsdiscount = 0;

            $receiptproduct_id = DB::table('oew_receiptproduct')->insertGetId([
                "receipt_id" => $receipt->id,
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

            DB::table('oew_itemdetails')->insert([
                "receiptproduct_id" => $receiptproduct_id,
                "amount" => $request->item_amount * 100,
                "rounding" => $request->cal_rounding,
                "price" => $request->price * 100,
                "sst" => $request->sst * 100,
                "discount" => 0,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

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

            DB::table('oew_receiptdetails')->insert([
                "receipt_id" => $receipt->id,
                "total" => $request->dose * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->item_amount * 100,
                "sst" => $request->sst * 100,
                "discount" => $receiptproductsdiscount * 100,
                "cash_received" => $cash_received * 100,
                "change" => $request->change_amount * 100,
                "creditcard" => $creditcard * 100,
                "wallet" => ($wallet ?? 0) * 100,
                "creditac" => ($creditac ?? 0) * 100,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            //Sync tables
            $data = array();
            $data['oew_receipt'] = DB::table('oew_receipt')->
				whereId($receipt->id)->first();

            $data['oew_receiptdetails'] = DB::table('oew_receiptdetails')->
				where('receipt_id',$receipt->id)->first();

            $data['oew_receiptproduct'] = DB::table('oew_receiptproduct')->
				where('receipt_id',$receipt->id)->first();

            $data['oew_itemdetails'] = DB::table('oew_itemdetails')->
				where('receiptproduct_id',$receiptproduct_id)->first();

            $data['oew_receiptlist'] = DB::table('oew_receiptlist')->
				where('oew_receipt_id',$receipt->id)->first();

            $data['og_localfuelprice'] = DB::table('og_localfuelprice')->get();

            $query = "select t.systemid from terminal t, oew_receipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-fuel-receipt' , json_encode($data));
            //return $response_sync;

            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->first();

            \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

            $loginOut = FuelReceipt::getCurrentLoginOut();
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->first();
            $eoddetail_id = null;

            if ($brancheoddata == null) {
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
                Log::info("Data for EOD : " . json_encode($dataForEod));
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
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "opt" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail_id = $idEoddetail;

            } else {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata);
                Log::info("Data for EOD : " . json_encode($dataForEod));

                DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    update([
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "opt" => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail = DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->first();

                $eoddetail_id = $eoddetail->id;
            }

            if ($eoddetail_id != null) {
                $currentLoginOut = FuelReceipt::getCurrentLoginOut();
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
                        "startdate" => date('Y-m-d'),
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                        "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                        "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                        "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
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
                        "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                        "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                        "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                        "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                        "opt" => 0,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

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


        /* Store New Transaction */
        public function storeOeWReceiptList($receipt, $dose, $filled)
        {
            Log::info('storeOeWReceiptList: receipt=' . json_encode($receipt));
            Log::info('storeOeWReceiptList: dose=' . $dose . ', filled=' . $filled);

            try {
                $fuel = $dose;
                DB::table('oew_receiptlist')->insert([
                    "oew_receipt_id" => $receipt->id,
                    "oew_receipt_systemid" => $receipt->systemid,
                    "pump_no" => $receipt->pump_no,
                    "total" => $dose * 100,
                    "fuel" => $dose * 100,
                    "filled" => $filled,
                    // "refund" => ($fuel - $filled) * 100,
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

    /* Display a oew receipt modal in OeW Receipt List via a link */
      public function oewReceipt(Request $request)
      {
  		Log::debug('oewReceipt: all='.json_encode($request->all()));
  		Log::debug('oewReceipt:'.json_encode($request));

          try {
              $location = Location::first();
              $company = Company::first();

  			Log::debug('oewReceipt: location='.json_encode($location));
  			Log::debug('oewReceipt: company ='.json_encode($company));
  			Log::debug('oewReceipt: source  ='.$request->source);
  			Log::debug('oewReceipt: id      ='.$request->id);

              //if ($request->source == 'fuel') {

  				$receipt = OEWReceipt::with("user")->
  					where('id', $request->id)->first();
  				$receiptdetails = OEWReceiptdetails::
          join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receiptdetails.receipt_id')->
  					where('receipt_id', $request->id)->first();

  				// Fetch staff user from receipt NOT from currently
  				// logged in user
  				$user = DB::table('users')->
  					where('id', $receipt->staff_user_id)->first();

  				$receiptproduct = DB::table('oew_receiptproduct')->
  					where('receipt_id', $request->id)->get();

  				// This is obsoleted. Get refund data from oew_receiptlist table
  				$refund = OEWReceiptList::where("oew_receipt_id", $request->id)->
  					join('users', 'users.id', 'oew_receiptlist.refund_staff_user_id')->
  					first();

  				$receiptCount = DB::table('creditact_ledger')->
  						where('document_no', $request->id)->
  						get()->count();


  			/* This else clause is trying to differentiate fuel and fulltank for creditaccount
  			   update in creditact and creditact_ledger. This is wrong because the "source"
  			   is fake. This clause should be done in the Fuel Fulltank Receipt Controller */
  			/*
              } else {
                  $receipt = FuelFulltankReceipt::
  					where('id', $request->id)->first();
                  $receiptdetails = FuelFulltankReceiptdetails::
                      where('fulltank_receipt_id', $request->id)->first();

                  // Fetch staff user from receipt NOT from currently
                  // logged in user
                  $user = DB::table('users')->
                      where('id', $receipt->staff_user_id)->first();

                  $receiptproduct = DB::table('fuelfulltank_receiptproduct')->
                      where('fulltank_receipt_id', $request->id)->get();

                  // This is obsoleted. Get refund data from oew_receiptlist table
                  $refund = FuelFulltankReceiptlist::where("fuel_fulltank_receipt_id", $request->id)->
                      join('users', 'users.id', 'fuelfulltank_receiptlist.refund_staff_user_id')->
                      first();

                  $receiptCount = DB::table('creditact_ledger')->
                          where('document_no', $request->id)->
                          get()->count();
              }
  			*/

              // var_dump($refund);

              // Here you have to fetch:
              /*
              $refund->fullname        from users.fullname
              $refund->systemid        from oew_receipt.systemid
              $refund->created_at      from oew_receiptlist.refund_tstamp
              $refund->refund          from oew_receiptlist.refund
               */

  			/* Don't get the current logged in terminal. Fetch the original
  			   terminal stored in the receipt. May be different from the
  			   current terminal */
              $terminal = DB::table('terminal')->
                  where('id', $receipt->terminal_id)->first();
                  Log::debug('cleger: creadita='.json_encode($receiptdetails));
              return view("outdoor_ewallet.oew_receipt", compact(
                  "location", "user", "receiptproduct", "terminal", "company",
                  "receipt", "receiptdetails", "refund", "receiptCount"));

          } catch (\Exception $e) {
              return [
                  "message" => $e->getMessage(),
                  "error" => false,
              ];
          }
      }

      public function dataTable(Request $request)
      {
          try {
            if($request->date!=""){
                $data = DB::table('oew_receipt')->
                join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')->
                selectRaw('oew_receipt.status,
                oew_receipt.staff_user_id,
                oew_receiptlist.id,
                oew_receiptlist.oew_receipt_systemid,
                oew_receiptlist.total,
                oew_receiptlist.filled,
                oew_receiptlist.refund,
                oew_receiptlist.pump_no,
                oew_receiptlist.oew_receipt_id AS receipt_id,
                oew_receiptlist.created_at AS created_at')->
                whereNull('oew_receiptlist.deleted_at')->
                whereDate('oew_receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->
                orderBy('oew_receiptlist.id', 'DESC')->get();

            } else {
                $data = DB::table('oew_receipt')->
                join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')->
                selectRaw('
                oew_receipt.staff_user_id,
                oew_receipt.status,
                oew_receiptlist.id,
                oew_receiptlist.oew_receipt_systemid,
                oew_receiptlist.total,
                oew_receiptlist.filled,
                oew_receiptlist.refund,
                oew_receiptlist.pump_no,
                oew_receiptlist.oew_receipt_id AS receipt_id,
                oew_receiptlist.created_at AS created_at')->
                whereNull('oew_receiptlist.deleted_at')->
                // whereDate('oew_receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->
                orderBy('oew_receiptlist.id', 'DESC')->get();

            }
              return Datatables::of($data)->
                  setRowId(function ($data) {
                  return 'pump_receipt_data_' . $data->pump_no . '-' . $data->receipt_id;
              })->
                  addIndexColumn()->
                  addColumn('date', function ($data) {
                  $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                  return <<<EOD
            $created_at
  EOD;
              })->
                  addColumn('isrefunded', function ($data) {
                  $systemid = ($data->status == "refunded") ? 1 : 0;
                  return <<<EOD
            $systemid
  EOD;
              })->
                  addColumn('systemid', function ($data) {
                  $systemid = !empty($data->oew_receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getOeWReceiptlist(' . $data->receipt_id . ')" > ' . $data->oew_receipt_systemid . '</a>' : 'Receipt ID';
                  return <<<EOD
            $systemid
  EOD;
              })->
                  addColumn('total', function ($data) {
                  if ($data->status === "voided") {
                      $total = '0.00';
                  } else {
                      $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                  }
                  return <<<EOD
            $total
  EOD;
              })->
                  addColumn('fuel', function ($data) {
                  $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                  return <<<EOD
            $total
  EOD;
              })->

                  addColumn('filled', function ($data) {
                  $filled = !empty($data->filled) ? number_format($data->filled / 100, 2) : '0.00';
                  return <<<EOD
            $filled
  EOD;
              })->
                  addColumn('refund', function ($data) {
                  $refund = "0.00";
                  $refund = number_format((($data->total / 100) - ($data->filled / 100)), 2);
                  \Illuminate\Support\Facades\Log::info($refund);
                  return <<<EOD
            $refund
  EOD;
              })->
                  addColumn('action', function ($data) {
                  $action = '';
                  return <<<EOD
            $action
  EOD;
              })->
                  addColumn('action', function ($row) {
                  $refund = ($row->total / 100) - ($row->filled / 100);
                  if ($row->status != "refunded" && $refund > 0 && $row->status != "voided") {
                      $btn = '<a  href="javascript:void(0)"  onclick="refundMe(' . $row->receipt_id . ', ' . $row->total / 100 . ', ' . $row->filled / 100 . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '" alt=""> </a>';
                      return $btn;
                  } else {
                      $btn = '<a  href="javascript:void(0)"   disabled="disabled" style=" filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""> </a>';
                      return $btn;
                  }
              })->
                  addColumn('status_color', ' ')->
                  editColumn('status_color', function ($row) {
                  $status = "none";
                  if ($row->status === "voided") {
                      $status = "red";
                  }
                  if ($row->status === "refunded") {
                      $status = "#ff7e30";
                  }
                  return $status;

              })->
                  editColumn('systemid', function ($data) {
                  $systemid = !empty($data->oew_receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getOeWReceiptlist(' . $data->receipt_id . ')" > ' . $data->oew_receipt_systemid . '</a>' : 'Receipt ID';
                  return <<<EOD
                          $systemid
  EOD;
              })->
                  rawColumns(['action'])->
                  escapeColumns([])->
                  make(true);

          } catch (Exception $e) {
              Log::info([
                  "Errory" => $e->getMessage(),
                  "Filey" => $e->getFile(),
                  "Line No" => $e->getLine(),
              ]);
              abort(404);
          }
      }

      public function oewRefund(Request $request)
      {

          $price = DB::table('oew_receiptproduct')->
              join('oew_receipt', 'oew_receipt.id', 'oew_receiptproduct.receipt_id')->
              join('product', 'product.id', 'oew_receiptproduct.product_id')->
              where('oew_receipt.status', '!=', 'voided')
              ->where('oew_receipt.id', $request->id)
              ->select('oew_receiptproduct.price')->first();

          // var_dump($price->price);exit();
          DB::table('oew_receipt')->where([
              "id" => $request->id,
          ])->update([
              "status" => 'refunded',
              "updated_at" => now(),
          ]);
          $rec = DB::table('oew_receipt')
              ->select('oew_receipt.*', 'oew_receiptdetails.*')
              ->join('oew_receiptdetails', 'oew_receiptdetails.receipt_id', 'oew_receipt.id')
              ->where('oew_receipt.id', $request->id)
              ->first();
          $refund = ($request->fuel - $request->filled) * 100;
          $sst = round($rec->sst - (($refund - ($refund / (1 + ($rec->service_tax / 100))))));
          $item_amount = round(($rec->total - $refund) / (1 + ($rec->service_tax / 100)));
          $total = $rec->total - $refund;
          // var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
          $x = $total / 100;
          $r_total = ((5 * round(($x * 100) / 5)) / 100) * 100;
          Log::info("total : " . $total . " : r_total : " . $r_total);
          $round = ($r_total) - $total;
          // update fuel receiptlist
          DB::table('oew_receiptlist')->where([
              "oew_receipt_id" => $request->id,
          ])->update([
              "status" => 'refunded',
              "refund" => ($request->fuel - $request->filled) * 100,
              "refund_staff_user_id" => Auth::id(),
              "refund_tstamp" => now(),
              "refund_qty" => ($request->fuel - $request->filled) * 100 / $price->price,
              "newsales_item_amount" => $item_amount,
              "newsales_tax" => $sst,
              "newsales_rounding" => $round,
              "updated_at" => now(),
          ]);

          /*$rec = DB::table('oew_receipt')
              ->select('oew_receipt.*', 'oew_receiptdetails.*', 'oew_receiptlist.refund', 'oew_receiptlist.refund_qty')
              ->join('oew_receiptdetails', 'oew_receiptdetails.receipt_id', 'oew_receipt.id')
              ->join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')
              ->where('oew_receipt.id', $request->id)
              ->first();

          $item_amount = 0;
          $round = 0;
          $sst = 0;
          $card = 0;
          $ac = 0;
          $cash = 0;
          $wallet = 0;
          Log::info("Ref Receipt: " . json_encode($rec));
          if($rec->payment_type == 'cash'){
              $cash = $rec->cash_received - $rec->refund;
          } else if($rec->payment_type == 'creditcard'){
              $card = $rec->creditcard - $rec->refund;
          } else if ($rec->payment_type == 'wallet'){
              $wallet = $rec->wallet - $rec->refund;
          } else if($rec->payment_type == 'creditac'){
              $ac = $rec->creditac - $rec->refund;
          }
          $sst = round($rec->sst - (($rec->refund - ($rec->refund / (1 + ($rec->service_tax / 100))))));
          $item_amount = round(($rec->total - $rec->refund) / (1 + ($rec->service_tax / 100)));
          $total = $rec->total - $rec->refund;
          $round = $total - $item_amount - $sst;
          Log::info("REF DATA: Item Amount : " . $item_amount . " : SST: " . $sst . " : Total: " . $total . " : Round:" . $round);
          DB::table('oew_receiptdetails')->where([
              "receipt_id" => $request->id,
          ])->update([
              "item_amount" => $item_amount,
              "total" => $total,
              "rounding" => $round,
              "sst" => $sst,
              "creditcard" => $card,
              "creditac" => $ac,
              "cash_received" => $cash,
              "wallet" => $wallet,

          ]);
          DB::table('oew_receiptproduct')->where([
              "receipt_id" => $request->id,
          ])->update([
              "quantity" => DB::raw('quantity - ' . $rec->refund_qty),
          ]);*/
          $brancheoddata = DB::table('brancheod')->
          whereDate('created_at', '=', date('Y-m-d', strtotime($rec->created_at)))->first();
           \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

          if ($brancheoddata != null) {
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
                  "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                  "cash_change" => $dataForEod["eodChange"],
                  "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                  "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                  "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                  "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                  "opt" => 0,
                  'updated_at' => date('Y-m-d H:i:s'),
              ]);

              $eoddetail = DB::table('eoddetails')->
                  where('eod_id', $brancheoddata->id)->first();
              $loginOut = FuelReceipt::getCurrentLoginOut();

              $eoddetail_id = $eoddetail->id;
              $dataPshiftdetails = DB::table('pshiftdetails')->
                  where('pshift_id', '=', $loginOut->shift_id)->first();

              if ($eoddetail_id != null) {
                  $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();
                  Log::info("DATAPS : " . json_encode($dataForEod));
                  if ($dataPshiftdetails != null) {
                      DB::table('pshiftdetails')->
                          where('id', $dataPshiftdetails->id)->
                          update([
                          "total_amount" => $dataForEod["eodTotal"],
                          "rounding" => $dataForEod["eodRound"],
                          "sales" => $dataForEod["eodItemAmount"],
                          "sst" => $dataForEod["eodTax"],
                          "discount" => $dataForEod["eodDiscount"],
                          "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                          "cash_change" => $dataForEod["eodChange"],
                          "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                          "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                          "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                          "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                          "opt" => 0,
                          'updated_at' => date('Y-m-d H:i:s'),
                      ]);
                  }
              }
          }

          return ["message" => "oewRefund: successfully refunded",
              "error" => false];
      }


          public function updateFilled(Request $request)
          {
              try {
                  $oew_receiptlist = OEWReceiptList::where('oew_receipt_id',
                      $request->id)->firstOrFail();
                  $oew_receiptlist->filled = $request->filled * 100;
                  // $oew_receiptlist->refund = $request->refund * 100;
                  $oew_receiptlist->save();
                  return [
                      "message" => "Successfully updated oew_receiptlist",
                      "error" => false,
                  ];

              } catch (\Exception $e) {
                  return [
                      "message" => $e->getMessage(),
                      "error" => false,
                  ];
              }
          }
}
