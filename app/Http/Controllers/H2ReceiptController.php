<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

use App\Models\H2ReceiptDetails;
use App\Models\H2ReceiptList;
use App\Models\Location;
use App\Models\User;
use App\Models\FuelReceipt;
use App\Models\H2Receipt;
use App\Models\H2ReceiptProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;
use \App\Http\Controllers\SyncSalesController;


class H2ReceiptController extends Controller
{
    public function h2ReceiptList(Request $request){
        $date = isset($request->date)?$request->date:"";
        return view('h2_receipt.h2_receiptlist', compact('date'));
    }


    // H2receipt
    public function h2_Receipt(Request $request)
    {


        try {
            $location = Location::first();
            $company = Company::first();

            $receipt = H2Receipt::with("staff_user")
                ->where('id', $request->id)->first();

/*
             $receipt =DB::select(DB::raw(
                "select h2receipt.*,users.* from h2receipt
                inner join users on users.id =h2receipt.staff_user_id
                where h2receipt.id=".$request->id."
                limit 1
                "
            ));

 */
            // $receipt =DB::table("h2receipt")
            //     ->join('users', 'users.id', 'h2receipt.staff_user_id')
            //     ->select("users.name","h2receipt.*")
            //     ->where('h2receipt.id', $request->id)->first();



                $receiptdetails = H2ReceiptDetails::
                 where('receipt_id', $request->id)->first();


                // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->
                where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('h2receiptproduct')->
                where('receipt_id', $request->id)->get();
//

            $refund = H2ReceiptList::where("h2receipt_id", $request->id)->
                join('users', 'users.id', 'h2receiptlist.refund_staff_user_id')->
                first();

            $receiptCount = 0;
            $terminal = DB::table('terminal')->
                where('id', $receipt->terminal_id)->first();


            return view("h2_receipt.h2_receipt", compact(
            "location", "user", "receiptproduct", "terminal", "company",
            "receipt", "receiptdetails", "refund", "receiptCount"));

        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }

    // h2PersonalShiftReceipt
    public function h2PersonalShiftReceipt(Request $request)
    {
        try {
            $location       = Location::first();
            $company        = Company::first();
            $date = $request->_date;
            $start = date('Y-m-d H:i:s', strtotime($request->_date));
            $stop = date('Y-m-d',strtotime($request->_date));
            $receipt        = H2Receipt::with("user")->where('id', $request->id)->first();


            $receiptdetail=DB::select("

            select h2receipt.systemid,
            h2receipt.id,
            h2receipt.staff_user_id,

            sum(h2receiptdetails.total) as `total`,
            sum(h2receiptdetails.rounding) as rounding,
            sum(h2receiptdetails.item_amount) as item_amount,
            sum(h2receiptdetails.sst) as sst,
            sum(h2receiptdetails.cash_received) as cash_received,
            sum(h2receiptdetails.wallet) as wallet,
            sum(h2receiptdetails.change) as `change`
            FROM h2receipt
            INNER JOIN h2receiptdetails ON h2receiptdetails.receipt_id= h2receipt.id and
            `h2receiptdetails`.`created_at`
            between '".$start."' and '".$stop." 23:59:59'
            where `h2receipt`.`deleted_at` is null and `h2receipt`.`staff_user_id` = '".$request->staff_user_id."'
            group by `h2receipt`.`staff_user_id` order by `h2receipt`.`id`

            ");// H2ReceiptDetails::where('receipt_id', $request->id)->first();

            $receiptdetails  = $receiptdetail[0];
            $user = DB::table('users')->
                where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('h2receiptproduct')->
                where('receipt_id', $request->id)->get();


            // This is obsoleted. Get refund data from fuel_receiptlist table
            $refund = H2ReceiptList::where("h2receipt_id", $request->id)->
                join('users', 'users.id', 'h2receiptlist.refund_staff_user_id')->
                first();
            $receiptCount = 0;

            // $receiptCount = DB::table('creditact')->
            //         where('h2receipt_id', $request->id)->
            //         get()->count();

            // var_dump($refund);

            // Here you have to fetch:
            /*
            $refund->fullname        from users.fullname
            $refund->systemid        from fuel_receipt.systemid
            $refund->created_at      from fuel_receiptlist.refund_tstamp
            $refund->refund          from fuel_receiptlist.refund
            */

            /* Don't get the current logged in terminal. Fetch the original
            terminal stored in the receipt. May be different from the
            current terminal */
            $terminal = DB::table('terminal')->
                where('id', $receipt->terminal_id)->first();

            return view("h2_receipt.h2_personalshift_receipt", compact(
                "location", "user", "receiptproduct", "terminal", "company",
                "receipt", "receiptdetails", "refund", "receiptCount","date"));

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
                $data = DB::table('h2receipt')->
                join('h2receiptlist', 'h2receiptlist.h2receipt_id', 'h2receipt.id')->
                selectRaw('h2receipt.status,
                h2receipt.staff_user_id,
                h2receiptlist.id,
                h2receiptlist.h2receipt_systemid,
                h2receiptlist.total,
                h2receiptlist.filled,
                h2receiptlist.refund,
                h2receiptlist.pump_no,
                h2receiptlist.h2receipt_id AS receipt_id,
                h2receiptlist.created_at AS created_at')->
                whereNull('h2receiptlist.deleted_at')->
                whereDate('h2receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->
                orderBy('h2receiptlist.id', 'DESC')->get();

            } else {
                $data = DB::table('h2receipt')->
                join('h2receiptlist', 'h2receiptlist.h2receipt_id', 'h2receipt.id')->
                selectRaw('
                h2receipt.staff_user_id,
                h2receipt.status,
                h2receiptlist.id,
                h2receiptlist.h2receipt_systemid,
                h2receiptlist.total,
                h2receiptlist.filled,
                h2receiptlist.refund,
                h2receiptlist.pump_no,
                h2receiptlist.h2receipt_id AS receipt_id,
                h2receiptlist.created_at AS created_at')->
                whereNull('h2receiptlist.deleted_at')->
                // whereDate('h2receiptlist.created_at', date('Y-m-d', strtotime($request->date)))->
                orderBy('h2receiptlist.id', 'DESC')->get();

            }

            return Datatables::of($data)
            ->setRowClass(function ($rw) {
                return 'row-class-'.$rw->id;
            })
            ->setRowId(function ($data) {
                return 'pump_receipt_data_' . $data->pump_no . '-' . $data->receipt_id;
            })->
                addIndexColumn()->
                addColumn('date', function ($data) {
                return date("dMy h:i:s", strtotime($data->created_at));

            })->
                addColumn('isrefunded', function ($data) {
                $systemid = ($data->status == "refunded") ? 1 : 0;
                return <<<EOD
					$systemid
EOD;
            })->
                addColumn('systemid', function ($data) {
                $systemid = !empty($data->h2receipt_systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getH2ReceiptList(' . $data->receipt_id . ')" > ' . $data->h2receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
					$systemid
EOD;
            })->
                addColumn('total', function ($data) {
                if ($data->status === "voided") {
                    $total = '0.00';
                } else {
                    $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                }//style="background-color: rgb(255, 126, 48);"
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
                // $refund = number_format((($row->total / 100) - ($row->filled / 100)), 2);

                // if ($row->status != "refunded" && $refund > 0 && $row->status != "voided" && $row->filled > 0) {
                if ($row->status != "refunded" && $refund > 0 && $row->status != "voided" ) {
                    $btn = '<a  href="javascript:void(0)" class="refund-row" onclick="refundMe(' . $row->receipt_id . ', ' . $row->total / 100 . ', ' . $row->filled / 100 . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '" alt=""> </a>';
                    return $btn;
                } else {
                    $btn = '<a  href="javascript:void(0)"   disabled="disabled" style=" filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""></a>';
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
                $systemid = !empty($data->h2receipt_systemid) ? '<a href="#" style="text-decoration:none" onclick="getH2ReceiptList(' . $data->receipt_id . ')" > ' . $data->h2receipt_systemid . '</a>' : 'Receipt ID';
                return <<<EOD
                        $systemid
EOD;
            })->
                rawColumns(['action'])->
                escapeColumns([])->
                make(true);

        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function h2_receipt_list(Request $request){

        try {
            $location = Location::first();

            $company = Company::first();
            // print_r(session());
            // die("::::");

            $receipt = H2Receipt::with("user")->
                where('id', $request->id)->first();
            $receiptdetails = H2ReceiptDetails::where('receipt_id', $request->id)->first();

			// Fetch staff user from receipt NOT from currently
			// logged in user
            $user = DB::table('users')->
				where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuel_receiptproduct')->
                where('receipt_id', $request->id)->get();

            // This is obsoleted. Get refund data from h2receiptlist table
            $refund = H2ReceiptList::where("h2receipt_id", $request->id)->
                join('users', 'users.id', 'h2receiptlist.refund_staff_user_id')->
                first();

            $receiptCount = DB::table('creditact')->
                    where('h2receipt_id', $request->id)->
                    get()->count();

            // var_dump($refund);

            // Here you have to fetch:
            /*
            $refund->fullname        from users.fullname
            $refund->systemid        from fuel_receipt.systemid
            $refund->created_at      from h2receiptlist.refund_tstamp
            $refund->refund          from h2receiptlist.refund
             */

			/* Don't get the current logged in terminal. Fetch the original
			   terminal stored in the receipt. May be different from the
			   current terminal */
            $terminal = DB::table('terminal')->
                where('id', $receipt->terminal_id)->first();
                // $date = "01-10-2021";
            return view("h2_receipt.h2_receiptlist", compact(
                "location", "user", "receiptproduct", "terminal", "company",
                "receipt", "receiptdetails", "refund", "receiptCount"));

        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }

    }

    public function store_h2_reciept_list(Request $request){

        // try{

        //     $H2ReceiptDetails               = new H2ReceiptDetails();
        //     $H2ReceiptDetails->total        = $request->total;
        //     $H2ReceiptDetails->rounding     = $request->rounding;
        //     $H2ReceiptDetails->item_amount  = $request->item_amount;
        //     $H2ReceiptDetails->sst          = $request->sst;
        //     $H2ReceiptDetails->discount     = "";//$request->discount;
        //     $H2ReceiptDetails->cash_received = $request->cash_received;
        //     $H2ReceiptDetails->wallet       = "";//$request->wallet;
        //     $H2ReceiptDetails->creditac     = "";
        //     $H2ReceiptDetails->change       = $request->change;
        //     $H2ReceiptDetails->creditcard   = "";//$request->creditcard;
        //     $H2ReceiptDetails->void         = "";//$request->creditcard;

        //     $H2ReceiptDetails->save();


        //     $H2ReceiptProduct = new H2ReceiptProduct;

        //     $H2ReceiptProduct->receipt_id   = $H2ReceiptDetails->id;
        //     $H2ReceiptProduct->name         = $request->name;
        //     $H2ReceiptProduct->product_id   = $request->product_id;
        //     $H2ReceiptProduct->quantity     = $request->qty;
        //     $H2ReceiptProduct->price        = $request->price;
        //     $H2ReceiptProduct->filled       = $request->filled;

        //     return response()->json(["data"=>"ok"]);
        // }
        // catch (\Exception $e) {
        //     return [
        //         "message" => $e->getMessage(),
        //         "error" => false,
        //     ];
        // }









        // try {
        //     $client_ip = request()->ip();
        //     $terminal = DB::table('terminal')->
        //         where('client_ip', $client_ip)->first();

        //     $user = Auth::user();
        //     $company = Company::first();

        //     $location = DB::table('location')->first();//Location::first();
        //     $systemid = Systemid::h2receipt_system_id($terminal->id);

        //     $pump_hardware = DB::table('local_h2pump')->
        //         where("pump_no", $request->pump_no)->first();

        //     //     print_r($pump_hardware);
        //     // die();
        //     $receipt = new H2Receipt();
        //     $receipt->systemid = $systemid;
        //     $receipt->location_id = $location->id;

        //     if ($request->payment_type == "card") {
        //         $receipt->payment_type = "creditcard";
        //         $receipt->creditcard_no = $request->creditcard_no ?? 0;
        //         //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
        //         $receipt->cash_received = 0;

        //     } elseif ($request->payment_type == 'wallet') {
        //         $receipt->payment_type = "wallet";
        //         //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
        //         $receipt->cash_received = 0;

        //     }
        //     //  elseif ($request->payment_type == 'creditac') {
        //     //     $receipt->payment_type = "creditac";
        //     //     //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
        //     //     $receipt->cash_received = 0;

        //     // }
        //      else {
        //         $receipt->payment_type = isset($request->payment_type)?$request->payment_type:"cash";
        //         $receipt->cash_received = ($request->cash_received ?? 0) * 100;
        //         $receipt->cash_change = ($request->change_amount ?? 0) * 100;
        //     }

        //     // print_r($receipt->cash_received);
        //     // echo("::::".$request->cash_received);
        //     // die($receipt->cash_change."<br>::::".$request->change_amount);


        //     $receipt->service_tax = $terminal->tax_percent;
        //     $receipt->terminal_id = $terminal->id;
        //     $receipt->mode = $terminal->mode;

        //     $receipt->staff_user_id = $user->id;
        //     $receipt->company_id = $company->id;
        //     $receipt->company_name = $company->name;
        //     $receipt->gst_vat_sst = $company->gst_vat_sst;
        //     $receipt->business_reg_no = $company->business_reg_no;
        //     $receipt->receipt_logo = $company->corporate_logo;
        //     $receipt->receipt_address = $company->office_address;

        //     $currencyarr = DB::table('currency')->where('id', $company->currency_id)->orderBy('code')->get()->first();

        //     $receipt->currency = $currencyarr->code ?? 'MYR';

        //     $receipt->status = "active";
        //     $receipt->remark = "NULL";
        //     $receipt->transacted = "pos";

        //     $receipt->pump_id = $pump_hardware->id;
        //     $receipt->pump_no = $request->pump_no;

        //     $receipt->transacted = "pos";
        //     $receipt->save();

        //     Log::debug('CreateFuelList: $receipt = ' . json_encode($receipt));

        //     // Store Data in Fuel Receiptlist Table
        //     $this->storeH2ReceiptList($receipt, $request->dose, $request->filled);

        //     $receiptproductsdiscount = 0;

        //     $receiptproduct_id = DB::table('h2receiptproduct')->insertGetId([
        //         "receipt_id" => $receipt->id,
        //         "product_id" => $request->product_id,
        //         "name" => $request->name,
        //         "quantity" => $request->qty,
        //         "price" => $request->price * 100,
        //         "discount_pct" => 0,
        //         "discount" => 0,
        //         "created_at" => date('Y-m-d H:i:s'),
        //         'updated_at' => date('Y-m-d H:i:s'),
        //     ]);

        //     $amount = (float) number_format($request->item_amount);
        //     $price = (float) number_format($request->price);
        //     $sst = (float) number_format($request->sst);
        //     $total_amount = (float) number_format($request->dose);
        //     $rounding = (float) number_format($request->cal_rounding);

        //     DB::table('fuel_itemdetails')->insert([
        //         "receiptproduct_id" => $receiptproduct_id,
        //         "amount" => $request->item_amount * 100,
        //         "rounding" => $request->cal_rounding,
        //         "price" => $request->price * 100,
        //         "sst" => $request->sst * 100,
        //         "discount" => 0,
        //         "created_at" => $receipt->created_at,
        //         'updated_at' => $receipt->created_at,
        //     ]);

        //     $cash_received = 0;
        //     $cash_change = 0;
        //     $creditcard = 0;
        //     $creditac = 0;
        //     if ($receipt->payment_type == "cash") {
        //         $cash_received = $request->cash_received;
        //     } elseif ($receipt->payment_type == "wallet") {
        //         $wallet = $request->dose;
        //     } elseif ($receipt->payment_type == "creditac") {
        //         $creditac = $request->dose;
        //     } else {
        //         $creditcard = $request->dose;
        //     }

        //     DB::table('h2receiptdetails')->insert([
        //         "receipt_id" => $receipt->id,
        //         "total" => $request->dose * 100,
        //         "rounding" => $request->cal_rounding * 100,
        //         "item_amount" => $request->item_amount * 100,
        //         "sst" => $request->sst * 100,
        //         "discount" => $receiptproductsdiscount * 100,
        //         "cash_received" => $cash_received * 100,
        //         "change" => $request->change_amount * 100,
        //         "creditcard" => $creditcard * 100,
        //         "wallet" => ($wallet ?? 0) * 100,
        //         "creditac" => ($creditac ?? 0) * 100,
        //         "created_at" => $receipt->created_at,
        //         'updated_at' => $receipt->created_at,
        //     ]);

        //     $brancheoddata = DB::table('brancheod')->
        //         whereDate('created_at', '=', date('Y-m-d'))->first();

        //     \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

        //     $loginOut = H2Receipt::getCurrentLoginOut();
        //     $dataPshiftdetails = DB::table('pshiftdetails')->
        //         where('pshift_id', '=', $loginOut->shift_id)->first();
        //     $eoddetail_id = null;
        //     // die("::".$location->id);

        //     if ($brancheoddata == null) {
        //         $brancheod = DB::table('brancheod')->insertGetId([
        //             "eod_presser_user_id" => $user->id,
        //             "location_id" => $location->id,
        //             "terminal_id" => $terminal->id,
        //             "created_at" => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         ]);

        //         $brancheoddata = DB::table('brancheod')->
        //             where('id', '=', $brancheod)->first();
        //         \Illuminate\Support\Facades\Log::info("yes");

        //         $dataForEod = H2Receipt::getReceiptValueWithoutVoid($brancheoddata);

        //         \Illuminate\Support\Facades\Log::info("after");

        //         $idEoddetail = DB::table('eoddetails')->
        //             insertGetId([
        //             "eod_id" => $brancheod,
        //             "startdate" => date('Y-m-d'),
        //             "total_amount" => $dataForEod["eodTotal"],
        //             "rounding" => $dataForEod["eodRound"],
        //             "sales" => $dataForEod["eodItemAmount"],
        //             "sst" => $dataForEod["eodTax"],
        //             "discount" => $dataForEod["eodDiscount"],
        //             "cash" => $dataForEod["eodCash"],
        //             "cash_change" => $dataForEod["eodChange"],
        //             "creditcard" => $dataForEod["eodCreditCard"],
        //             "wallet" => $dataForEod["eodWallet"],
        //             "creditac" => $dataForEod["eodcreditAccount"],
        //             "opt" => 0,
        //             "created_at" => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         ]);

        //         $eoddetail_id = $idEoddetail;

        //     } else {
        //         $dataForEod = H2Receipt::getReceiptValueWithoutVoid($brancheoddata);
        //         DB::table('eoddetails')->
        //             where('eod_id', $brancheoddata->id)->
        //             update([
        //             "startdate" => date('Y-m-d'),
        //             "total_amount" => $dataForEod["eodTotal"],
        //             "rounding" => $dataForEod["eodRound"],
        //             "sales" => $dataForEod["eodItemAmount"],
        //             "sst" => $dataForEod["eodTax"],
        //             "discount" => $dataForEod["eodDiscount"],
        //             "cash" => $dataForEod["eodCash"],
        //             "cash_change" => $dataForEod["eodChange"],
        //             "creditcard" => $dataForEod["eodCreditCard"],
        //             "wallet" => $dataForEod["eodWallet"],
        //             "creditac" => $dataForEod["eodcreditAccount"],
        //             "opt" => 0,
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         ]);

        //         $eoddetail = DB::table('eoddetails')->
        //             where('eod_id', $brancheoddata->id)->first();

        //         $eoddetail_id = $eoddetail->id;
        //     }

        //     if ($eoddetail_id != null) {
        //         $currentLoginOut = H2Receipt::getCurrentLoginOut();
        //         DB::table('pshift')->where(
        //             'id', $currentLoginOut->shift_id
        //         )->update([
        //             'eoddetails_id' => $eoddetail_id,
        //         ]);

        //         $dataForEod = H2Receipt::getUserLoginReceiptValueWithoutVoid();

        //         if ($dataPshiftdetails == null) {
        //             \Illuminate\Support\Facades\Log::info(["eoddetail_id" => "start"]);

        //             DB::table('pshiftdetails')->
        //                 insert([
        //                 "pshift_id" => $loginOut->shift_id,
        //                 "eoddetails_id" => $eoddetail_id,
        //                 "startdate" => date('Y-m-d'),
        //                 "total_amount" => $dataForEod["eodTotal"],
        //                 "rounding" => $dataForEod["eodRound"],
        //                 "sales" => $dataForEod["eodItemAmount"],
        //                 "sst" => $dataForEod["eodTax"],
        //                 "discount" => $dataForEod["eodDiscount"],
        //                 "cash" => $dataForEod["eodCash"],
        //                 "cash_change" => $dataForEod["eodChange"],
        //                 "creditcard" => $dataForEod["eodCreditCard"],
        //                 "wallet" => $dataForEod["eodWallet"],
        //                 "creditac" => $dataForEod["eodcreditAccount"],
        //                 "opt" => 0,
        //                 "created_at" => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);

        //         } else {
        //             DB::table('pshiftdetails')->
        //                 where('id', $dataPshiftdetails->id)->
        //                 update([
        //                 "total_amount" => $dataForEod["eodTotal"],
        //                 "rounding" => $dataForEod["eodRound"],
        //                 "sales" => $dataForEod["eodItemAmount"],
        //                 "sst" => $dataForEod["eodTax"],
        //                 "discount" => $dataForEod["eodDiscount"],
        //                 "cash" => $dataForEod["eodCash"],
        //                 "cash_change" => $dataForEod["eodChange"],
        //                 "creditcard" => $dataForEod["eodCreditCard"],
        //                 "wallet" => $dataForEod["eodWallet"],
        //                 "creditac" => $dataForEod["eodcreditAccount"],
        //                 "opt" => 0,
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //         }
        //     }

        //     return $receipt->id;

        // } catch (\Exception $e) {
        //     \Log::error([
        //         'Error' => $e->getMessage(),
        //         "File" => $e->getFile(),
        //         "Line" => $e->getLine(),
        //     ]);

        //     return $e;
        // }


        try {

          //  dd($request->all());

            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->
                where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = \App\Models\Location::first();

            // echo json_encode($request->all(),JSON_PRETTY_PRINT);
            // die("");
            $pump_hardware = DB::table('local_pump')->
                where("pump_no", $request->pump_no)->first();

            $systemid = Systemid::h2receipt_system_id($terminal->id);

            // $ev_mode = CarparklotSettingMode::getCurrentCarparkMode();
            $receipt = new H2Receipt();
            $receipt->systemid = $systemid;

            // $receipt->hours     = $request->hours;
            // $receipt->rate      = ($request->rate ?? 0) * 100;
            // $receipt->kwh       = ($request->kwh ?? 0) * 100;
            // $receipt->ev_mode   = $ev_mode;

            if ($request->payment_type == "card") {
                $receipt->payment_type = "creditcard";
                $receipt->creditcard_no = $request->creditcard_no ?? 0;
                //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_received = 0;

            } elseif ($request->payment_type == 'wallet') {
                $receipt->payment_type = "wallet";
                //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_received = 0;

            }
            // elseif ($request->payment_type == 'creditac') {
            //     $receipt->payment_type = "creditac";
            //     //$receipt->cash_received = ($request->cash_received ?? 0) * 100;
            //     $receipt->cash_received = 0;

            // }
            else {
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

            $receipt->pump_id = $request->pump_no;
            $receipt->pump_no = $request->pump_no;

            $receipt->transacted = "pos";
            $location = DB::table('lic_locationkey')->first();
            $receipt->location_id = $location->location_id;
            $receipt->save();

            Log::debug('CreateEVReceiptList: $receipt = ' . json_encode($receipt));

            // Store Data in h2 Receiptlist Table
            $this->storeH2ReceiptList($receipt, $request);

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
            DB::table('h2receiptdetails')->insert([
                "receipt_id" => $receipt->id,
                "total" => $request->currency * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->item_amount * 100,
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


            $H2ReceiptProduct = new H2ReceiptProduct;

            $H2ReceiptProduct->receipt_id   = $receipt->id;
            $H2ReceiptProduct->name         = $request->name;
            $H2ReceiptProduct->product_id   = $request->product_id;
            $H2ReceiptProduct->quantity     = $request->qty;
            $H2ReceiptProduct->price        = $request->price*100;
            $H2ReceiptProduct->filled       = $request->filled;

            $H2ReceiptProduct->save();

            // // Insert values to evreceiptcarparklot table
            // DB::table('h2receiptcarparklot')->insert([
            //     "evreceipt_id" => $receipt->id,
            //     "carpark_oper_id" => $request->carparkoper_id,
            //     "created_at" => $receipt->created_at,
            //     'updated_at' => $receipt->created_at,
            // ]);

            // update values to carpark_oper table
            // DB::table('carpark_oper')->where('carparklot_id', $request->carparklot_id)
            // ->update([
            //     "status" => 'paid',
            //     "amount" => $request->itemAmount * 100,
            //     "payment" => $request->total * 100,
            //     "created_at" => $receipt->created_at,
            //     'updated_at' => $receipt->created_at,
            // ]);

            // EOD Details
            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->first();

            \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

            $loginOut = H2Receipt::getCurrentLoginOut();
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->first();

            $eoddetail_id = null;

            if (empty($brancheoddata)) {

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
                print_r($dataForEod);

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
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail_id = $idEoddetail;
                // print_r($brancheoddata);

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
                //
                // print_r($eoddetail);
                // echo "<br>";
                // print_r($brancheoddata);
                // die($brancheoddata->id."::::::::::::::::::".$eoddetail_id);


                $eoddetail_id = $eoddetail->id;
            }
            // EOD Details
            // PShift Details
            if ($eoddetail_id != null) {
                $currentLoginOut = H2Receipt::getCurrentLoginOut();
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
            // PShift Details

            // $carparkOperas = app('App\Http\Controllers\CarparkController')->getCustomCarParkOpera();
            // $current_setting_mode = CarparklotSettingMode::getCurrentCarparkMode();
            // $stop_count = CarparkOper::query()->where('stop_timestamp', '!=', null)->count();
            // $transaction_count = CarparkOper::count();
            // $paid_count = CarparkOper::query()->where('status', 'paid')->count();
            // return view("carpark.carparklot_table", compact("carparkOperas","current_setting_mode",
            // 'stop_count','transaction_count','paid_count'));

               $h2receipt = Db::table('h2receipt')->whereId($receipt->id)->get();
               $h2receiptdetails = Db::table('h2receiptdetails')->where('receipt_id' , $receipt->id)->get();
               $h2receiptlist = Db::table('h2receiptlist')->where('h2receipt_id' , $receipt->id)->get();
               $h2receiptproduct = Db::table('h2receiptproduct')->where('receipt_id' , $receipt->id)->get();

               // dd($h2receiptproduct , $h2receiptdetails , $h2receipt , $h2receiptlist);

               $data['h2receipt'] = $h2receipt;
               $data['h2receiptdetails'] = $h2receiptdetails;
               $data['h2receiptlist'] = $h2receiptlist;
               $data['h2receiptproduct'] = $h2receiptproduct;

               $query = "select t.systemid from terminal t, h2receipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
               $data['terminal_systemid'] = DB::select(DB::raw($query));

               SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-h2-fuel' , json_encode($data));


              $query = http_build_query($data, '', '&');

            $url = env('MOTHERSHIP_URL') . '/store/h2/receipt/' . $query;
            Log::debug("url=" . $url);
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);
            $data = json_decode($apiResponse, true);


            return $receipt->id;
        } catch (\Exception $e) {

            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            return $e;
        }

    }//end



    /* Store New Transaction */
    public function storeH2ReceiptList($receipt, $request)
    {
        Log::info('storeH2ReceiptList: receipt=' . json_encode($receipt));

        try {

            DB::table('h2receiptlist')->insert([
                "h2receipt_id" => $receipt->id,
                "h2receipt_systemid" => $receipt->systemid,
                "refund_staff_user_id" => $receipt->staff_user_id,
                "pump_no" => $request->pump_no,
                "total" => $request->currency * 100,
                "fuel" => $request->dose * 100,
                "filled" => $request->filled,
                "status" => $receipt->status,
                "created_at" => Carbon::now(),
                // ---
                "refund" => null,
                "refund_qty" => null,
            ]);


        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
        }
    }



    public function h2Refund(Request $request)
    {
        $price = DB::table('h2receiptproduct')->
            join('h2receipt', 'h2receipt.id', 'h2receiptproduct.receipt_id')->
            join('product', 'product.id', 'h2receiptproduct.product_id')->
            where('h2receipt.status', '!=', 'voided')
            ->where('h2receipt.id', $request->id)
            ->select('h2receiptproduct.price')->first();
        // var_dump($price->price);exit();

        $h2rec = DB::table("h2receipt")
            ->join("h2receiptdetails", "h2receipt.id", "h2receiptdetails.receipt_id")
            ->where("h2receipt.id", $request->id)->first();
        DB::table('h2receipt')->where([
            "id" => $request->id,
        ])->update([
            "status" => 'refunded',
            "updated_at" => now(),
        ]);
        // update fuel receiptlist
        DB::table('h2receiptlist')->where([
            "h2receipt_id" => $request->id,
        ])->update([
            "status" => 'refunded',
            // "refund" => ($request->fuel - $request->filled) * 100,
            "refund" => ($request->fuel - $request->filled) * 100,
            "refund_staff_user_id" => Auth::id(),
            "refund_tstamp" => now(),
            "refund_qty" => ($request->fuel - $request->filled) * 100 / $price->price,
            "updated_at" => now(),
        ]);

        $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d', strtotime($h2rec->created_at)))->first();
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
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail = DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->first();
                $loginOut = DB::table('loginout')->
                    where("user_id", $h2rec->staff_user_id)->
                    where("login", "<=", $h2rec->created_at)->
                    where("logout", ">", $h2rec->created_at)->
                    first();

                if ($loginOut == null) {
                    $loginOut = DB::table('loginout')->
                        where("user_id", $h2rec->staff_user_id)->
                        where("login", "<=", $h2rec->created_at)->
                        where("logout", null)->
                        first();
                }

                $eoddetail_id = $eoddetail->id;
                $dataPshiftdetails = DB::table('pshiftdetails')->
                    where('pshift_id', '=', $loginOut->shift_id)->first();

                if ($eoddetail_id != null) {
                    $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();

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

        return ["message" => "h2Refund: successfully refunded",
            "error" => false];
    }



}
