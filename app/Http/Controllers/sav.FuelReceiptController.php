<?php
/*
$fuel_receiptlist = FuelReceiptList::where('fuel_receipt_id',
$request->id)->firstOrFail();
 */

namespace App\Http\Controllers;

use Log;
use App\Models\Company;
use App\Models\FuelReceipt;
use App\Models\FuelReceiptdetails;
use App\Models\FuelReceiptList;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;
use App\Http\Controllers\SyncSalesController;
use App\Models\FuelFulltankReceipt;
use App\Models\FuelFulltankReceiptdetails;
use App\Models\FuelFulltankReceiptlist;
use PDF;

class FuelReceiptController extends Controller
{
    //

    public function fuelReceipList(Request $request)
    {
        $date = $request->date;

        $company = Company::first();
        $approved_at = $company->approved_at;

        return view('fuel_receipt.fuel_receiptlist', compact('date', 'approved_at'));
    }

    public function CreateFuelList(Request $request)
    {
        Log::debug('***** CreateFuelList(): ' . json_encode($request->all()));
        try {
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $systemid = Systemid::fuelreceipt_system_id($terminal->id);
            $pump_hardware = DB::table('local_pump')->where("pump_no", $request->pump_no)->first();
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

            $currencyarr = DB::table('currency')->where('id', $company->currency_id)->orderBy('code')->get()->first();

            $receipt->currency = $currencyarr->code ?? 'MYR';

            $receipt->status = "active";
            $receipt->remark = "NULL";
            $receipt->transacted = "pos";

            $receipt->pump_id = $pump_hardware->id;
            $receipt->pump_no = $request->pump_no;

            $receipt->transacted = "pos";
            $receipt->save();

            Log::debug('CreateFuelList: $receipt = ' . json_encode($receipt));

            // Store Data in Fuel Receiptlist Table
            $this->storeFuelReceiptList($receipt, $request->dose, $request->filled);

            $receiptproductsdiscount = 0;

            $receiptproduct_id = DB::table('fuel_receiptproduct')->insertGetId([
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

            DB::table('fuel_itemdetails')->insert([
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
                $wallet = $request->dose + $request->cal_rounding;
            } elseif ($receipt->payment_type == "creditac") {
                $creditac = $request->dose + $request->cal_rounding;
            } else {
                $creditcard = $request->dose + $request->cal_rounding;
            }


            DB::table('fuel_receiptdetails')->insert([
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
            $data['fuel_receipt'] = DB::table('fuel_receipt')->
				whereId($receipt->id)->first();

            $data['fuel_receiptdetails'] = DB::table('fuel_receiptdetails')->
				where('receipt_id', $receipt->id)->first();

            $data['fuel_receiptproduct'] = DB::table('fuel_receiptproduct')->
				where('receipt_id', $receipt->id)->first();

            $data['fuel_itemdetails'] = DB::table('fuel_itemdetails')->
				where('receiptproduct_id', $receiptproduct_id)->first();

            $data['fuel_receiptlist'] = DB::table('fuel_receiptlist')->
				where('fuel_receipt_id', $receipt->id)->first();

            $data['og_localfuelprice'] = DB::table('og_localfuelprice')->get();

            $query = "select t.systemid from terminal t, fuel_receipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-fuel-receipt', json_encode($data));
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

                $brancheoddata = DB::table('brancheod')->where('id', '=', $brancheod)->first();
                \Illuminate\Support\Facades\Log::info("yes");
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);
                Log::info("Data for EOD : " . json_encode($dataForEod));
                \Illuminate\Support\Facades\Log::info("after");

                $idEoddetail = DB::table('eoddetails')->insertGetId([
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
                        "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                        "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                        "opt" => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $eoddetail_id = $idEoddetail;
            } else {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);
                Log::info("Data for EOD : " . json_encode($dataForEod));

                $eod_detail = DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->first();

                if (empty($eod_detail)) {
                    $idEoddetail = DB::table('eoddetails')->insertGetId([
                            "eod_id" => $brancheoddata->id,
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
                            "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                            "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                            "opt" => 0,
                            "created_at" => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                } else {
                    DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->update([
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
                            "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                            "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                            "opt" => 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                $eoddetail = DB::table('eoddetails')->
					where('eod_id', $brancheoddata->id)->
					first();

                if (!empty($eoddetail)) {
                    $eoddetail_id = $eoddetail->id;
                }
            }

            if ($eoddetail_id != null) {
                $currentLoginOut = FuelReceipt::getCurrentLoginOut();
                DB::table('pshift')->where(
                    'id',
                    $currentLoginOut->shift_id
                )->update([
                    'eoddetails_id' => $eoddetail_id,
                ]);

                $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();

                if ($dataPshiftdetails == null) {

                    Log::info(["eoddetail_id" => "start"]);

                    DB::table('pshiftdetails')->insert([
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
						"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
						"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
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
						"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
						"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
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
    public function storeFuelReceiptList($receipt, $dose, $filled)
    {
        Log::info('storeFuelReceiptList: receipt=' . json_encode($receipt));
        Log::info('storeFuelReceiptList: dose=' . $dose . ', filled=' . $filled);

        try {
            $fuel = $dose;
            DB::table('fuel_receiptlist')->insert([
                "fuel_receipt_id" => $receipt->id,
                "fuel_receipt_systemid" => $receipt->systemid,
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

    /* Display a fuel receipt modal in Fuel Receipt List via a link */
    public function fuelReceipt(Request $request)
    {
        Log::debug('fuelReceipt: all=' . json_encode($request->all()));
        Log::debug('fuelReceipt:' . json_encode($request));

        try {
            $location = Location::first();
            $company = Company::first();

            Log::debug('fuelReceipt: location=' . json_encode($location));
            Log::debug('fuelReceipt: company =' . json_encode($company));
            Log::debug('fuelReceipt: source  =' . $request->source);
            Log::debug('fuelReceipt: id      =' . $request->id);

            //if ($request->source == 'fuel') {

            $receipt = FuelReceipt::with("user")->
				where('id', $request->id)->first();

            $receiptdetails = FuelReceiptdetails::where('receipt_id', $request->id)->first();

            // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->
				where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuel_receiptproduct')->
				where('receipt_id', $request->id)->
				get();

            // This is obsoleted. Get refund data from fuel_receiptlist table
            $refund = FuelReceiptList::where("fuel_receipt_id", $request->id)->
				join('users', 'users.id', 'fuel_receiptlist.refund_staff_user_id')->
				first();

            $receiptCount = DB::table('creditact_ledger')->
				where('document_no', $request->id)->
				get()->count();

            /* Don't get the current logged in terminal. Fetch the original
			   terminal stored in the receipt. May be different from the
			   current terminal */
            $terminal = DB::table('terminal')->
				where('id', $receipt->terminal_id)->
				first();

            Log::debug('FuelReceipt: receiptdetails=' . json_encode($receiptdetails));

            return view("fuel_receipt.fuel_receipt", compact(
                "location",
                "user",
                "receiptproduct",
                "terminal",
                "company",
                "receipt",
                "receiptdetails",
                "refund",
                "receiptCount"
            ));
        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }

    public function fuelRefund(Request $request)
    {
        $price = DB::table('fuel_receiptproduct')->
			join('fuel_receipt', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')->
			join('product', 'product.id', 'fuel_receiptproduct.product_id')->
			where('fuel_receipt.status', '!=', 'voided')->
			where('fuel_receipt.id', $request->id)->
			select('fuel_receiptproduct.price')->first();

        // var_dump($price->price);exit();
        DB::table('fuel_receipt')->where([ "id" => $request->id, ])->
		update([
            "status" => 'refunded',
            "updated_at" => now(),
        ]);

        $rec = DB::table('fuel_receipt')->
			select('fuel_receipt.*', 'fuel_receiptdetails.*')->
			join('fuel_receiptdetails', 'fuel_receiptdetails.receipt_id',
				'fuel_receipt.id')->
			where('fuel_receipt.id', $request->id)->
			first();

        $refund = ($request->fuel - $request->filled) * 100;
        //$sst = round($rec->sst - (($refund - ($refund / (1 + ($rec->service_tax / 100))))));

        $refunded_sst = $refund - ($refund / (1 + ($rec->service_tax / 100)));
        $sst = round($rec->sst - $refunded_sst);

        if (!empty($rec) && !empty($rec->sst) && !empty($refunded_sst)) {
            Log::info("sst=" . ($rec->sst - $refunded_sst));
            Log::info("rounded sst=" . round($rec->sst - $refunded_sst));
            Log::info("rec->sst=" . $rec->sst . ", refunded_sst=" . $refunded_sst);
        }

        $item_amount = round(($rec->total - $refund) / (1 + ($rec->service_tax / 100)));
        $total = $rec->total - $refund;
        // var amount_total = ((5 * Math.round((parseFloat(sum_of_raw_amount) * 100) / 5)) / 100);
        $x = $total / 100;
        $r_total = ((5 * round(($x * 100) / 5)) / 100) * 100;
        Log::info("total : " . $total . " : r_total : " . $r_total);

        $round = ($r_total) - $total;
        // update fuel receiptlist
        DB::table('fuel_receiptlist')->
			where(["fuel_receipt_id" => $request->id, ])->
			update([
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
        
        $this->generate_refundPDF($request->id); 
        $brancheoddata = DB::table('brancheod')->
			whereDate('created_at', '=', date('Y-m-d',
				strtotime($rec->created_at)))->
			first();

        Log::info(json_encode($brancheoddata));

        if ($brancheoddata != null) {
            $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);
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
				"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
				"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
				"opt" => 0,
				'updated_at' => date('Y-m-d H:i:s'),
			]);

            $eoddetail = DB::table('eoddetails')->
				where('eod_id', $brancheoddata->id)->first();

            $loginOut = FuelReceipt::getCurrentLoginOut();

            $eoddetail_id = $eoddetail->id;

            $dataPshiftdetails = DB::table('pshiftdetails')->
				where('pshift_id', '=', $loginOut->shift_id)->
				first();

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
						"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
						"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
						"opt" => 0,
						'updated_at' => date('Y-m-d H:i:s'),
					]);
                }
                
            }
        }   
                
        return [
            "message" => "fuelRefund: successfully refunded",
            "error" => false
        ];
    }


	

    public function dataTable(Request $request)
    {
        try {
            $data = DB::table('fuel_receipt')->
			join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id',
					'fuel_receipt.id')->
			selectRaw('fuel_receipt.status,
				fuel_receiptlist.id,
				fuel_receiptlist.fuel_receipt_systemid,
				fuel_receiptlist.total,
				fuel_receiptlist.filled,
				fuel_receiptlist.refund,
				fuel_receiptlist.pump_no,
				fuel_receiptlist.fuel_receipt_id AS receipt_id,
				fuel_receiptlist.created_at AS created_at')->
			whereNull('fuel_receiptlist.deleted_at')->
			whereDate('fuel_receiptlist.created_at', date('Y-m-d',
				strtotime($request->date)))->
			orderBy('fuel_receiptlist.id', 'DESC')->
			get();

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
                    $systemid = !empty($data->fuel_receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_receipt_systemid . '</a>' : 'Receipt ID';
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
                })->addColumn('filled', function ($data) {
                    $filled = !empty($data->filled) ? number_format($data->filled / 100, 2) : '0.00';
                    return <<<EOD
					$filled
EOD;
                })->addColumn('refund', function ($data) {
                    $refund = "0.00";

                    $refund = number_format((($data->total / 100) - ($data->filled / 100)), 2);
                    \Illuminate\Support\Facades\Log::info($refund);
                    return <<<EOD
					$refund
EOD;
                })->addColumn('action', function ($data) {
                    $action = '';
                    return <<<EOD
					$action
EOD;
                })->addColumn('action', function ($row) {
                    $refund = ($row->total / 100) - ($row->filled / 100);
                    if ($row->status != "refunded" && $refund > 0 && $row->status != "voided" && $row->filled != "0.00") {
                        $btn = '<a  href="javascript:void(0)"  onclick="refundMe(' . $row->receipt_id . ', ' . $row->total / 100 . ', ' . $row->filled / 100 . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '" alt=""> </a>';
                        return $btn;
                    } else {
                        $btn = '<a  href="javascript:void(0)"  style=" filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""> </a>';
                        return $btn;
                    }
                })->addColumn('status_color', ' ')->editColumn('status_color', function ($row) {
                    $status = "none";
                    if ($row->status === "voided") {
                        $status = "red";
                    }
                    if ($row->status === "refunded") {
                        $status = "#ff7e30";
                    }
                    return $status;
                })->editColumn('systemid', function ($data) {
                    $systemid = !empty($data->fuel_receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_receipt_systemid . '</a>' : 'Receipt ID';
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



    public function datatable_search_pump(Request $request)
    {
        try {
            $data = DB::table('fuel_receipt')->
				join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id',
					'fuel_receipt.id')->
				selectRaw('fuel_receipt.status,
					fuel_receiptlist.id,
					fuel_receiptlist.fuel_receipt_systemid,
					fuel_receiptlist.total,
					fuel_receiptlist.filled,
					fuel_receiptlist.refund,
					fuel_receiptlist.pump_no,
					fuel_receiptlist.fuel_receipt_id AS receipt_id,
					fuel_receiptlist.created_at AS created_at')->
				whereNull('fuel_receiptlist.deleted_at')->
				where('fuel_receiptlist.pump_no', 'like', $request->pump_no . '%')->
				whereDate('fuel_receiptlist.created_at',
					date('Y-m-d', strtotime($request->date)))->
				orderBy('fuel_receiptlist.id', 'DESC')->
				get();

            return $this->process_datatable_search($data);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }


    public function datatable_search_ptype(Request $request)
    {
        if (strpos($request->ptype, 'creditac') !== false) {
            $request->ptype = 'creditac';
        }
        try {
			$data = DB::table('fuel_receipt')->
				join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id',
					'fuel_receipt.id')->
				selectRaw('fuel_receipt.status,
					fuel_receiptlist.id,
					fuel_receiptlist.fuel_receipt_systemid,
					fuel_receiptlist.total,
					fuel_receiptlist.filled,
					fuel_receiptlist.refund,
					fuel_receiptlist.pump_no,
					fuel_receiptlist.fuel_receipt_id AS receipt_id,
					fuel_receiptlist.created_at AS created_at')->
			  	whereNull('fuel_receiptlist.deleted_at')->
				where('fuel_receipt.payment_type', 'like', $request->ptype . '%')->
				whereDate('fuel_receiptlist.created_at',
					date('Y-m-d', strtotime($request->date)))->
				orderBy('fuel_receiptlist.id', 'DESC')->
				get();

            return $this->process_datatable_search($data);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }



    public function process_datatable_search($data)
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
                    $systemid = !empty($data->fuel_receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_receipt_systemid . '</a>' : 'Receipt ID';
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
                })->addColumn('filled', function ($data) {
                    $filled = !empty($data->filled) ? number_format($data->filled / 100, 2) : '0.00';
                    return <<<EOD
      $filled
EOD;
                })->addColumn('refund', function ($data) {
                    $refund = "0.00";

                    $refund = number_format((($data->total / 100) - ($data->filled / 100)), 2);
                    \Illuminate\Support\Facades\Log::info($refund);
                    return <<<EOD
      $refund
EOD;
                })->addColumn('action', function ($data) {
                    $action = '';
                    return <<<EOD
      $action
EOD;
                })->addColumn('action', function ($row) {
                    $refund = ($row->total / 100) - ($row->filled / 100);
                    if ($row->status != "refunded" && $refund > 0 && $row->status != "voided" && $row->filled != "0.00") {
                        $btn = '<a  href="javascript:void(0)"  onclick="refundMe(' . $row->receipt_id . ', ' . $row->total / 100 . ', ' . $row->filled / 100 . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '" alt=""> </a>';
                        return $btn;
                    } else {
                        $btn = '<a  href="javascript:void(0)"   disabled="disabled" style=" filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""> </a>';
                        return $btn;
                    }
                })->addColumn('status_color', ' ')->editColumn('status_color', function ($row) {
                    $status = "none";
                    if ($row->status === "voided") {
                        $status = "red";
                    }
                    if ($row->status === "refunded") {
                        $status = "#ff7e30";
                    }
                    return $status;
                })->editColumn('systemid', function ($data) {
                    $systemid = !empty($data->fuel_receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_receipt_systemid . '</a>' : 'Receipt ID';
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


    public function voidedReceipt(Request $request)
    {
        try {
            Log::debug('voidedReceipt: receipt_id=' . $request->receipt_id);

            // We have a phantom receipt! We quickly return with error
            if (empty($request->receipt_id)) {
                Log::error("Error: voidedReceipt(): Phantom receipt error!");
                return ["data" => ['Phantom receipt error'], 'error' => true];
            }

            $fuel_receipt = FuelReceipt::find($request->receipt_id);

            Log::debug('voidedReceipt: receipt=' . json_encode($fuel_receipt));

            $fuel_receipt->status = "voided";
            $fuel_receipt->voided_at = now();
            $fuel_receipt->voided_at = now();
            $fuel_receipt->void_user_id = Auth::user()->id;
            $fuel_receipt->save();

            /* DB::table('fuel_receiptlist')->where('fuel_receipt_id', $request->receipt_id)
            ->update([
            'total' => 0.00,
            ]);
            DB::table('fuel_receiptproduct')->where('receipt_id', $request->receipt_id)
            ->update([
            'quantity' => 0.00,
            ]);*/

            $brancheoddata = DB::table('brancheod')->
				whereDate('created_at', '=', date('Y-m-d',
					strtotime($fuel_receipt->created_at)))->first();

            Log::info(json_encode($brancheoddata));

            /*  $creditact = DB::table('creditact')->
                where('fuel_receipt_id', $request->receipt_id)->update([
                    "amount" => 0.00
                ]);
            */

            //void credit acount ledger new logic
            $creditact_ledger = DB::table('creditact_ledger')->
			where([
				'document_no' => $request->receipt_id,
				'source' => 'fuel'
			])->first();

            // Protect if this is not a credit account transaction
            if (!empty($creditact_ledger)) {
                $creditact = DB::table('creditact')->
				whereId($creditact_ledger->creditact_id)->first();

                DB::table('creditact_ledger')->
				where('document_no', $request->receipt_id)->
				update([
					"amount" => 0.00
				]);

                DB::table('creditact')->
				where('id', $creditact_ledger->creditact_id)->
				update([
					'amount' => $creditact->amount - $creditact_ledger->amount,
					'updated_at' => date('Y-m-d H:i:s')
				]);

            } else {
                Log::debug('voidReceipt: creditact_ledger record NOT found!');
            }

            if ($brancheoddata != null) {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);

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
					"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
					"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
					"opt" => 0,
					'updated_at' => date('Y-m-d H:i:s'),
				]);

                $eoddetail = DB::table('eoddetails')->
					where('eod_id', $brancheoddata->id)->
					first();

                $loginOut = DB::table('loginout')->
					where("user_id", $fuel_receipt->staff_user_id)->
					where("login", "<=", $fuel_receipt->created_at)->
					where("logout", ">", $fuel_receipt->created_at)->
					first();

                if ($loginOut == null) {
                    $loginOut = DB::table('loginout')->
						where("user_id", $fuel_receipt->staff_user_id)->
						where("login", "<=", $fuel_receipt->created_at)->
						where("logout", null)->first();
                }

                $eoddetail_id = $eoddetail->id;
                $dataPshiftdetails = DB::table('pshiftdetails')->
					where('pshift_id', '=', $loginOut->shift_id)->
					first();

                if ($eoddetail_id != null) {
                    $dataForEod = FuelReceipt::getUserReceiptValueWithoutVoid($loginOut);

                    if (empty($dataForEod["totalCashRound"])) {
                        $dataForEod["totalCashRound"] = 0;
                        Log::info('voidReceipt: totalCashRound empty');
                    }
                    if (empty($dataForEod["totalCreditCardRound"])) {
                        $dataForEod["totalCreditCardRound"] = 0;
                        Log::info('voidReceipt: totalCreditCardRound empty');
                    }
                    if (empty($dataForEod["totalWalletRound"])) {
                        $dataForEod["totalWalletRound"] = 0;
                        Log::info('voidReceipt: totalWalletRound empty');
                    }
                    if (empty($dataForEod["totalCreditAcRound"])) {
                        $dataForEod["totalCreditAcRound"] = 0;
                        Log::info('voidReceipt: totalCreditAcRound empty');
                    }
                    if (empty($dataForEod["totalOewRound"])) {
                        $dataForEod["totalOewRound"] = 0;
                        Log::info('voidReceipt: totalOewRound empty');
                    }

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
							"creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
							"oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
							"opt" => 0,
							'updated_at' => date('Y-m-d H:i:s'),
						]);
                    }
                }
            }

            return ["data" => $fuel_receipt, 'error' => false];
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
                'error' => false,
            ]);
            return ["data" => [], 'error' => true];
        }
    }

    public function updateFilled(Request $request)
    {
        try {
            $fuel_receiptlist = FuelReceiptList::where(
                'fuel_receipt_id',
                $request->id
            )->firstOrFail();
            $fuel_receiptlist->filled = $request->filled * 100;
            // $fuel_receiptlist->refund = $request->refund * 100;
            $fuel_receiptlist->save();
            return [
                "message" => "Successfully updated fuel_receiptlist",
                "error" => false,
            ];
        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }

    public static function generate_refundPDF($id) {
       
        $dimension= array(0,0,226.77,600);
        try {
            $location = Location::first();
            $company = Company::first();
            Log::debug('Generated Refund PDF fuelReceipt: id      =' . $id);

            //if ($request->source == 'fuel') {

            $receipt = FuelReceipt::with("user")->where('id', $id)->first();
            $receiptdetails = FuelReceiptdetails::where('receipt_id', $id)->first();

            // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuel_receiptproduct')->where('receipt_id', $id)->get();

            // This is obsoleted. Get refund data from fuel_receiptlist table
            $refund = FuelReceiptList::where("fuel_receipt_id", $id)->join('users', 'users.id', 'fuel_receiptlist.refund_staff_user_id')->first();

            $receiptCount = DB::table('creditact_ledger')->where('document_no', $id)->get()->count();

            $terminal = DB::table('terminal')->where('id', $receipt->terminal_id)->first();
            Log::debug('FuelReceiptVoid: receiptdetails=' . json_encode($receiptdetails));

            $invoiceName = Carbon::now()->format('Ymd');
            $pdf = PDF::loadView('fuel_receipt.fuel_receipt_autorefundpdf', compact(
                "location",
                "user",
                "receiptproduct",
                "terminal",
                "company",
                "receipt",
                "receiptdetails",
                "refund",
                "receiptCount"
            ))->setPaper($dimension);

            $pdf->save(Storage::disk('local')->put($invoiceName . '/'. $receipt->systemid .'-refund.pdf' , $pdf->output()));

        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
	}

}
