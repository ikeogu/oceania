<?php

namespace App\Http\Controllers;


use \App\Classes\SystemID;
use App\Models\FuelReceipt;
use App\Models\User;
use App\Models\Company;
use App\Models\FuelReceiptdetails;
use App\Models\Receipt;
use App\Models\Evreceipt;
use App\Models\ReceiptRefund;
use App\Models\Location;
use App\Models\Terminal;
use Milon\Barcode\DNS2D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\H2Receipt;
use App\Models\H2ReceiptList;
use App\Models\H2ReceiptDetails;
use App\Models\OEWReceipt;
use App\Models\OEWReceiptlist;
use App\Models\OEWReceiptdetails;



class PrintController extends Controller
{

	/* This is print function for CStore receipt */
	public function print_receipt(Request $request) {
		$milon = new DNS2D;
		$receipt = DB::table('cstore_receipt')->find($request->receipt_id);
		//$qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
		$qrcode = null;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptproduct = DB::table('cstore_receiptproduct')->
        where('receipt_id', $receipt->id)->get();
        $receiptdetails = DB::table('cstore_receiptdetails')->
        where('receipt_id', $receipt->id)->first();


        $reference = DB::table('cstore_receiptrefund')->
			join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->
			where('cstore_receipt_id', $receipt->id)->
			select('cstore_receiptrefund.*', 'users.fullname as name',
				'users.systemid as systemid')->first();

        $refund ='';
        if($reference){
            $refund = $reference;
            return  view('printing.refund_cstore_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptdetails',
                'receiptproduct',
                'qrcode',
                'refund'
            ));
        }

        if($receipt->status == "voided"){

            $voiduser = User::find($receipt->void_user_id);

			return  view('printing.void_cstore_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptdetails',
                'receiptproduct',
                'qrcode',
                'voiduser'
            ));
        }
		return view('printing.cstore_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptdetails',
            'receiptproduct',
            'qrcode'
        ));
	}
    public function print_fulltank_receipt(Request $request) {
		$milon = new DNS2D;
		$receipt = DB::table('fuelfulltank_receipt')->find($request->receipt_id);
		//$qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
		$qrcode = null;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptproduct = DB::table('fuelfulltank_receiptproduct')->
        where('fulltank_receipt_id', $receipt->id)->get();
        $receiptdetails = DB::table('fuelfulltank_receiptdetails')->
        where('fulltank_receipt_id', $receipt->id)->first();


		return view('printing.fulltank_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptdetails',
            'receiptproduct',
            'qrcode'
        ));
	}

	public function print_fuel_receipt(Request $request) {
		$milon = new DNS2D;
		$receipt = FuelReceipt::find($request->receipt_id);
		$qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptdetails = FuelReceiptdetails::
            where('receipt_id', $request->receipt_id)->first();

        // $refund = ReceiptRefund::where("receipt_id",$request->id)->
        //     join('users' , 'users.id' , 'receiptrefund.staff_user_id')->
        //     first();

        $receiptproduct = DB::table('fuel_receiptproduct')->
        where('receipt_id', $receipt->id)->get();
        $refund_ref = DB::table('fuel_receiptlist')->
                join('users', 'fuel_receiptlist.refund_staff_user_id', '=', 'users.id')->
                where('fuel_receiptlist.status', 'refunded')->
                where('fuel_receipt_id', $receipt->id)->
                select('fuel_receiptlist.refund_qty as qty', 'fuel_receiptlist.refund as refund_amount',
                'fuel_receiptlist.fuel_receipt_id as receipt_id', 'fuel_receiptlist.refund_staff_user_id as staff_user_id',
                'fuel_receiptlist.created_at', 'fuel_receiptlist.updated_at',
                'fuel_receiptlist.newsales_item_amount',
                'fuel_receiptlist.newsales_tax',
                'fuel_receiptlist.newsales_rounding',
                'users.fullname as name', 'users.systemid as systemid')->
                first();
        /*$reference = DB::table('receiptrefund')->
                join('users', 'receiptrefund.staff_user_id', '=', 'users.id')
                ->where('receipt_id', $receipt->id)
                ->select('receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')
                ->first();*/
        $refund = '';
        if($refund_ref){
            $refund = $refund_ref;
            return  view('printing.refund_fuel_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'qrcode',
                'refund',
                'receiptdetails'
            ));
        }

        if($receipt->status == "voided"){

            $voiduser = User::find($receipt->void_user_id);
           return  view('printing.void_fuel_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'qrcode',
                'voiduser',
                'receiptdetails'
            ));
        }
		return view('printing.fuel_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptproduct',
            'qrcode',
            'receiptdetails'
        ));
	}

	public function print_oew_receipt(Request $request) {
		$milon = new DNS2D;
		$receipt = OEWReceipt::find($request->receipt_id);
		$qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptdetails = OEWReceiptdetails::
            where('receipt_id', $request->receipt_id)->first();

        // $refund = ReceiptRefund::where("receipt_id",$request->id)->
        //     join('users' , 'users.id' , 'receiptrefund.staff_user_id')->
        //     first();

        $receiptproduct = DB::table('oew_receiptproduct')->
        where('receipt_id', $receipt->id)->get();
        $refund_ref = DB::table('oew_receiptlist')->
                join('users', 'oew_receiptlist.refund_staff_user_id', '=', 'users.id')->
                where('oew_receiptlist.status', 'refunded')->
                where('oew_receipt_id', $receipt->id)->
                select('oew_receiptlist.refund_qty as qty', 'oew_receiptlist.refund as refund_amount',
                'oew_receiptlist.oew_receipt_id as receipt_id', 'oew_receiptlist.refund_staff_user_id as staff_user_id',
                'oew_receiptlist.created_at', 'oew_receiptlist.updated_at',
                'oew_receiptlist.newsales_item_amount',
                'oew_receiptlist.newsales_tax',
                'oew_receiptlist.newsales_rounding',
                'users.fullname as name', 'users.systemid as systemid')->
                first();
        /*$reference = DB::table('receiptrefund')->
                join('users', 'receiptrefund.staff_user_id', '=', 'users.id')
                ->where('receipt_id', $receipt->id)
                ->select('receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')
                ->first();*/
        $refund = '';
        if($refund_ref){
            $refund = $refund_ref;
            return  view('printing.refund_oew_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'qrcode',
                'refund',
                'receiptdetails'
            ));
        }

        if($receipt->status == "voided"){

            $voiduser = User::find($receipt->void_user_id);
           return  view('printing.void_oew_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'qrcode',
                'voiduser',
                'receiptdetails'
            ));
        }
		return view('printing.oew_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptproduct',
            'qrcode',
            'receiptdetails'
        ));
	}

    public function print_ev_receipt(Request $request) {

        $milon = new DNS2D;
        $receipt = Evreceipt::find($request->evreceipt_id);
        $qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptdetails = DB::table('evreceiptdetails')->
        join('evreceiptlist', 'evreceiptlist.evreceipt_id', 'evreceiptdetails.evreceipt_id')->
        join('users', 'users.id', 'evreceiptlist.staff_user_id')->
        join('carpark_oper', 'carpark_oper.id', 'evreceiptlist.carpark_oper_id')->
        join('carparklot', 'carparklot.id', 'carpark_oper.carparklot_id')->
        where('evreceiptdetails.evreceipt_id', $request->evreceipt_id)->
        select('evreceiptdetails.*','carparklot.lot_no')->first();

        $receiptproduct = DB::table('h2receiptproduct')->
        where('receipt_id', $receipt->id)->get();

        if($receipt->status == "voided"){

            $voiduser = User::find($receipt->void_user_id);
           return  view('printing.void_ev_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'qrcode',
                'voiduser',
                "receiptproduct",
                'receiptdetails'
            ));
        }
        return view('printing.ev_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'qrcode',
            'receiptdetails'
        ));
    }

    public function print_h2_receipt(Request $request) {

        $milon = new DNS2D;

        $receipt = H2Receipt::find($request->receipt_id);

        $qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");
        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();

        $receiptdetails = H2ReceiptDetails::where('receipt_id', $request->receipt_id)->first();


        // $receiptdetails = DB::table('evreceiptdetails')->
        // join('evreceiptlist', 'evreceiptlist.evreceipt_id', 'evreceiptdetails.evreceipt_id')->
        // join('users', 'users.id', 'evreceiptlist.staff_user_id')->
        // join('carpark_oper', 'carpark_oper.id', 'evreceiptlist.carpark_oper_id')->
        // join('carparklot', 'carparklot.id', 'carpark_oper.carparklot_id')->
        // where('evreceiptdetails.evreceipt_id', $request->evreceipt_id)->
        // select('evreceiptdetails.*','carparklot.lot_no')->first();

        // Fetch staff user from receipt NOT from currently
        // logged in user
        // $user = DB::table('users')->
        //     where('id', $receipt->staff_user_id)->first();

        $receiptproduct = DB::table('h2receiptproduct')->
            where('receipt_id', $request->receipt_id)->get();

        // This is obsoleted. Get refund data from fuel_receiptlist table

        $refund = H2ReceiptList::where("h2receipt_id", $request->receipt_id)->
            join('users', 'users.id', 'h2receiptlist.refund_staff_user_id')->
            first();

        Log::info("h2Receipt:");
        Log::info(json_encode($receipt,JSON_PRETTY_PRINT));
        // die("::::::::::::::");
        Log::info("REFUND:");
        Log::info(json_encode($refund,JSON_PRETTY_PRINT));
        // die("::::::::::::::");
        $receiptCount = 0;

        $terminal = DB::table('terminal')->
            where('id', $receipt->terminal_id)->first();

        if($receipt->status=="refunded")
        {
            return view('printing.refund_h2_receipt_template_escpos', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                "refund",
                'qrcode',
                "receiptproduct",
                'receiptdetails'
            ));
        }else
        return view('printing.h2_receipt_template_escpos', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            "refund",
            'qrcode',
            "receiptproduct",
            'receiptdetails'
        ));

    }//end

    public function round_amount($num)
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
    public function eod_print(Request $request) {

        Log::debug('EOD Print Start: '.json_encode($request->eod_date));
        //dd($request->eod_date);
        $eod_date=$request->eod_date;
        if ($request->eod_date) {
            $date_eod = date_create($request->eod_date);
            $date_eod = date_format($date_eod, 'Y-m-d');
        } else {
            $date_eod = date('Y-m-d');
        }

        $user = Auth::user();
        $location = Location::first();
        $todaydate = date('Y-m-d');

		$client_ip = request()->ip();
        $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

        $eoddetailsdata = DB::table('eoddetails')->
        whereDate('startdate', '=', $date_eod)->first();

        $receipts = Receipt::with('receiptdetails')->
			whereDate('receipt.created_at', '=', $date_eod)->
			where('receipt.status', 'voided')->
            get();


        $reverseAmount = 0;
        $reverseCash = 0;
        $reverseCard = 0;
		$reverseWallet = 0;
        $reverseTax = 0;

        foreach ($receipts as $receipt){
            if ($receipt->payment_type == 'creditcard'){
                $reverseCard += $receipt->receiptdetails->creditcard;
			} elseif ($receipt->payment_type == 'wallet'){
				$reverseWallet += $receipt->receiptdetails->wallet;
			} else{
                $reverseCash += $receipt->receiptdetails->cash_received - $receipt->receiptdetails->change;
            }
            $reverseTax += $receipt->receiptdetails->sst;
        }
        $reverseAmount += ($reverseCard+$reverseCash+$reverseWallet)-$reverseTax;

		/*
		$refund_data = DB::table('receiptrefund')->
			join('receipt','receipt.id','receiptrefund.receipt_id')->
			whereDate('receipt.created_at', '=', $date_eod)->get();*/

		$refundAmount = DB::table('receiptrefund')->
			join('receipt','receipt.id','receiptrefund.receipt_id')->
			whereDate('receipt.created_at', '=', $date_eod)->get()->sum('refund_amount');

	/*/
		$refundTax = 0;
		$refundAmount = 0;
		$refundCash = 0;
        $refundCard = 0;
        $totalRefund = 0;

		foreach ($refund_data as $receipt){
			if ($receipt->payment_type == 'creditcard'){
				$refundCard += $receipt->refund_amount * 100;
			} else {
				$refundCash += $receipt->refund_amount * 100;
			}

			$refundAmount += $refundCash + $refundCard;

			$refundTax = $refundAmount / 100 * $terminal->tax_percent;
			$refundAmount -= $refundTax;
		}


		$reverseAmount += $refundAmount;
        $reverseCash += $refundCash;
        $reverseCard += $refundCard;
        $reverseTax += $refundTax;
/*/
		$refund_p_amount = $refundAmount;
		$tax_percent = ($terminal->tax_percent ?? 6);
		$refundAmount = ($refundAmount) / (1 + ($tax_percent / 100));
		$refund_sst = $refund_p_amount - $refundAmount;
		$refund_round = $refundAmount + $refund_sst;
		$refund_round = (float) number_format($this->round_amount($refund_round) /100, 2);


        $sst_tax = ( $eoddetailsdata->sst ?? 0) - $reverseTax;
		$round = (($eoddetailsdata->sales ?? 0)  + $sst_tax - $reverseAmount) / 100;
		$round = (float) number_format($this->round_amount($round) /100, 2);


        $user = Auth::user();
        $company = Company::first();
        $location = Location::first();
        /*
        return view('printing.eod_print_template_escpos', compact(
            'company', 'terminal', 'location', 'user', 'eoddetailsdata', 'reverseAmount','reverseTax','reverseCash','reverseCard'));
            */

        if($refundAmount > 0){
            return view('printing.eod_refund_template_escpos', compact(
				'round','refund_sst','refund_round','refundAmount',
                'company', 'terminal', 'location', 'user','reverseWallet',
				'eoddetailsdata', 'reverseAmount','reverseTax',
				'reverseCash','reverseCard', 'eod_date'));
        } else {
            return view('printing.eod_template_escpos', compact(
				'round', 'company', 'terminal', 'location', 'user',
				'eoddetailsdata', 'reverseAmount','reverseTax','reverseWallet',
				'reverseCash','reverseCard', 'eod_date'));
        }
    }


    public function PersonalShiftPrint(Request $request)
    {
        Log::debug('Request: '.json_encode($request->all()));
		$login_time  	= $request->login_time;
		$logout_time 	= $request->logout_time ?? now();
		$user_systemid 	= $request->user_systemid;

		$client_ip = request()->ip();
        $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

        $company = Company::first();
        $location = Location::first();
		$user = DB::table('users')->where('systemid', $user_systemid)->first();

        $data = DB::table('pshiftdetails')->
        where("pshift_id", $request->pshiftid)->
        first();
        $sales = "0.00";
        $cash = "0.00";
        $creditcard = "0.00";
        $creditac = "0.00";
        $wallet = "0.00";
		$oew = "0.00";
		$opt = "0.00";
        $tax = "0.00";
        $round = "0.00";

        Log::info("pshiftid ".$request->pshiftid);

		$logout_time = $request->logout_time ?? '';

		$cstore_total = DB::table('pshiftdetails')->
                            where('pshift_id', $request->pshiftid)->
                            first();
		$cstore_total = number_format(($cstore_total->cstore) / 100, 2);

		$fuel_prd = DB::table('pshiftfuel')->
                            join('prd_ogfuel', 'prd_ogfuel.id', '=', 'pshiftfuel.ogfuel_id')->
                            join('product', 'product.id', '=', 'prd_ogfuel.product_id')->
                            where('pshift_id', $request->pshiftid)->
                            get();

		$non_op_cash_in = "0.00";
		$non_op_cash_out = "0.00";
		$sales_drop = "0.00";
		$actual_drawer_amount = "0.00";
		$expected_amount = "0.00";
		$difference = "0.00";

        if ($data !== null) {
            $sales = number_format($data->sales / 100, 2);
            $cash = number_format(($data->cash) / 100, 2);
            $creditcard = number_format($data->creditcard / 100, 2);
            $creditac = number_format($data->creditac / 100, 2);
            $wallet = number_format($data->wallet / 100, 2);
            $tax = number_format($data->sst / 100, 2);
            $round = number_format($data->rounding / 100, 2);
            $opt = number_format($data->opt / 100, 2);
            $oew = number_format($data->oew / 100, 2);
            $opt = number_format($data->opt / 100, 2);

			$shift_data = DB::table('pshift')->
	                		whereId($request->pshiftid)->
	                        first();

	        $non_op_cash_in = number_format($shift_data->non_op_cash_in / 100, 2);
	        $non_op_cash_out = number_format($shift_data->non_op_cash_out / 100, 2);
	        $sales_drop = number_format($shift_data->sales_drop / 100, 2);
	        $actual_drawer_amount = number_format($shift_data->actual_drawer_amount / 100, 2);

			$expected_amount = number_format((
					$data->cash + $shift_data->non_op_cash_in - $shift_data->non_op_cash_out - $shift_data->sales_drop
				) / 100, 2);
			$difference = number_format((
					$shift_data->actual_drawer_amount - ($expected_amount * 100)
				) / 100, 2);
        }

		$logout_time 	= $request->logout_time ?? '';

		return view('printing.pshift_print', compact(
			'terminal', 'company', 'user','wallet','opt','oew', 'opt',
			'location', 'sales', 'cash', 'creditcard','creditac', 'fuel_prd',
			'cstore_total', 'non_op_cash_in', 'non_op_cash_out', 'sales_drop',
			'actual_drawer_amount', 'expected_amount', 'difference',
			'tax', 'round', 'login_time','logout_time'));
    }
}
