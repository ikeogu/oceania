<?php

namespace App\Http\Controllers;

use App\Classes\FuelUsageExport;
use App\Imports\ExcelExtractor;
use App\Imports\ExcelExtrator;
use App\Models\CommReceipt;
use App\Models\Company;
use App\Models\Evreceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    //
    public function generate(Request $request)
    {
        $company = Company::first();
        $approvedDate = $company->approved_at;
        return view("sales_report.sales_report", ['approved_at' => $approvedDate]);
        //return view("landing.sample_datepicker");
    }


    public function fuelPrintPDF(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();

        $start = date('Y-m-d', strtotime($request->fuel_start_date));
        $stop = date('Y-m-d', strtotime($request->fuel_end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $products = DB::table('prd_ogfuel')->
            join('product', 'product.id', '=', 'prd_ogfuel.product_id')->
            whereNull('prd_ogfuel.deleted_at')->
            get();

        $product_details = [];
        $refund = [];
        foreach ($products as $product) {
			Log::info('WS fuelPrintPDF: product_id='.$product->product_id);

            $item_amount = DB::table('fuel_receiptdetails')->
			join('fuel_receiptproduct','fuel_receiptproduct.receipt_id','=',
				'fuel_receiptdetails.receipt_id')->
			join('fuel_receipt','fuel_receiptproduct.receipt_id','=',
				'fuel_receipt.id')->
			where('fuel_receiptproduct.product_id', '=', $product->product_id)->
			where('fuel_receipt.status', '!=', 'voided')->
			whereBetween('fuel_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('item_amount');
			//select('fuel_receiptdetails.receipt_id','item_amount')->get();

			Log::info('WS fuelPrintPDF: item_amount='.$item_amount);


            $quantity = DB::table('fuel_receiptdetails')->
			join('fuel_receiptproduct','fuel_receiptproduct.receipt_id','=',
				'fuel_receiptdetails.receipt_id')->
			join('fuel_receipt', 'fuel_receiptproduct.receipt_id','=',
				'fuel_receipt.id')->
			where('fuel_receiptproduct.product_id','=',$product->product_id)->
			where('fuel_receipt.status', '!=', 'voided')->
			whereBetween('fuel_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('fuel_receiptproduct.quantity');
			//select('fuel_receiptdetails.receipt_id','fuel_receiptproduct.quantity')->get();

			Log::info('WS fuelPrintPDF: quantity='.$quantity);


			/* Fulltank receipts added to the sales report.  No need to check
			 * for voided invoices since the fulltank doesn't have void. */

            $fulltank_item_amount = DB::table('fuelfulltank_receiptdetails')->
			join('fuelfulltank_receiptproduct',
				'fuelfulltank_receiptproduct.fulltank_receipt_id','=',
				'fuelfulltank_receiptdetails.fulltank_receipt_id')->
			join('fuelfulltank_receipt',
				'fuelfulltank_receiptproduct.fulltank_receipt_id','=',
				'fuelfulltank_receipt.id')->
			where('fuelfulltank_receiptproduct.product_id', '=',
				$product->product_id)->
			whereBetween('fuelfulltank_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('item_amount');

			//Log::info('WS fuelPrintPDF: fulltank_item_amount='.$fulltank_item_amount);

            $fulltank_quantity = DB::table('fuelfulltank_receiptdetails')->
			join('fuelfulltank_receiptproduct',
				'fuelfulltank_receiptproduct.fulltank_receipt_id','=',
				'fuelfulltank_receiptdetails.fulltank_receipt_id')->
			join('fuelfulltank_receipt',
				'fuelfulltank_receiptproduct.fulltank_receipt_id','=',
				'fuelfulltank_receipt.id')->
			where('fuelfulltank_receiptproduct.product_id','=',
				$product->product_id)->
			whereBetween('fuelfulltank_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('fuelfulltank_receiptproduct.quantity');


			//Log::info('WS fuelPrintPDF: fulltank_quantity='.$fulltank_quantity);

			/* Fulltank receipts added to the sales report.  No need to check
			 * for voided invoices since the fulltank doesn't have void. */

			/*
            Log::info("WS fuelprintPDF: product=" . json_encode($product) .
				", fulltank_item_amount=" . $fulltank_item_amount .
				", fulltank_quantity=" . $fulltank_quantity);
			*/
            /*
             * Updated query
			 * Calculate $net_quantity by dividing the $refund_amount by $price
			 * of a particular product on a particular receipt between the
			 * selected period of time ($start and $end)
             */
            $refund_amount = DB::table('fuel_receiptlist')->
				join('fuel_receipt', 'fuel_receipt.id',
					'fuel_receiptlist.fuel_receipt_id')->
				join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id',
					'=', 'fuel_receipt.id')->
				join('product', 'product.id',
					'fuel_receiptproduct.product_id')->
				where('fuel_receipt.status', '!=', 'voided')->
                where('product.ptype', 'oilgas')->
                where('fuel_receiptproduct.product_id', $product->product_id)->
                whereBetween('fuel_receiptlist.created_at',
					[$start . ' 00:00:00', $stop . ' 23:59:59'])->
                sum('fuel_receiptlist.refund');
                //select('fuel_receipt.id','fuel_receiptlist.filled','fuel_receiptlist.refund',)->get();


			//Log::info('WS fuelPrintPDF: refund_amount='.$refund_amount);

            $refund_amount_tax = DB::table('fuel_receiptlist')->
				join('fuel_receipt', 'fuel_receipt.id',
					'fuel_receiptlist.fuel_receipt_id')->
				join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id',
					'=', 'fuel_receipt.id')->
				join('product', 'product.id',
					'fuel_receiptproduct.product_id')->
				where('fuel_receipt.status', '!=', 'voided')->
                where('product.ptype', 'oilgas')->
                where('fuel_receiptproduct.product_id', $product->product_id)->
                whereBetween('fuel_receiptlist.created_at',
					[$start . ' 00:00:00', $stop . ' 23:59:59'])->
                sum(DB::raw('fuel_receiptlist.refund / (1 + fuel_receipt.service_tax / 100)'));
                //select(DB::raw('fuel_receiptlist.refund / (1 + fuel_receipt.service_tax / 100)'))->get();


			Log::info('WS fuelPrintPDF: refund_amount_tax='.$refund_amount_tax);

            $price = DB::table('fuel_receiptproduct')->
				join('fuel_receipt', 'fuel_receipt.id',
					'fuel_receiptproduct.receipt_id')->
				join('product', 'product.id',
					'fuel_receiptproduct.product_id')->
                where('fuel_receipt.status', '!=', 'voided')->
                where('product.ptype', 'oilgas')->
                where('fuel_receiptproduct.product_id', $product->product_id)->
                whereBetween('fuel_receiptproduct.created_at',
					[$start . ' 00:00:00', $stop . ' 23:59:59'])->
                select('fuel_receiptproduct.price')->
                first();

            $refund_qty = DB::table('fuel_receiptlist')->
				join('fuel_receipt', 'fuel_receipt.id',
					'fuel_receiptlist.fuel_receipt_id')->
				join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id',
					'=', 'fuel_receipt.id')->
				join('product', 'product.id',
					'fuel_receiptproduct.product_id')->
				where('fuel_receipt.status', '!=', 'voided')->
                where('product.ptype', 'oilgas')->
                where('fuel_receiptproduct.product_id', $product->product_id)->
                whereBetween('fuel_receiptlist.created_at',
					[$start . ' 00:00:00', $stop . ' 23:59:59'])->
                sum('fuel_receiptlist.refund_qty');

            if ($refund_amount > 0) {
                $prd_refund_amount = DB::table('fuel_receiptlist')->
				join('fuel_receipt', 'fuel_receipt.id',
					'fuel_receiptlist.fuel_receipt_id')->
				join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id',
					'=', 'fuel_receipt.id')->
				join('product', 'product.id',
					'fuel_receiptproduct.product_id')->
				where('fuel_receipt.status', '!=', 'voided')->
				where('product.ptype', 'oilgas')->
				where('fuel_receiptproduct.product_id', $product->product_id)->
				whereBetween('fuel_receiptlist.created_at',
					[$start . ' 00:00:00', $stop . ' 23:59:59'])->
				get();

				foreach($prd_refund_amount as $ref){
					if ($ref->refund > 0) {
						$refund[] = $ref;
					}
				}

                $refund_quantity = $refund_qty;

            } else {
                $refund_quantity = 0;
            }

            $net_quantity = $fulltank_quantity + $quantity - $refund_quantity;


            Log::info('fuelPrintPDF: net_quantity=' . $net_quantity);

            Log::debug('fuelPrintPDF: product_id=' . $product->id .
                ', refund_amount=' . $refund_amount);

            $net_branch_sales =
				($fulltank_item_amount + $item_amount - $refund_amount_tax);

            Log::info('fuelPrintPDF: net_branch_sales=' . $net_branch_sales);

            if ($item_amount > 0 || $fulltank_item_amount > 0) {
				$product->item_amount = $fulltank_item_amount + $item_amount;
				// - ($refund * 100);

                $product->quantity = $quantity + $fulltank_quantity;
                $product->net_quantity = $net_quantity;
                $product->net_branch_sales = $net_branch_sales;

                Log::debug('fuelPrintPDF product=' . json_encode($product));

                $product_details[] = $product;
            }
        }


        Log::debug('fuelPrintPDF product_details=' .
			json_encode($product_details));

        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->
			loadView('sales_report.fuel_sales_report_pdf', compact(
				'product_details',
				'requestValue',
				'location',
				'refund_amount',
				'refund',
				'currency'
			));

        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
		);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('SalesReport.pdf');
    }


    public function opt_print_pdf(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();
        $start = date('Y-m-d', strtotime($request->opt_start_date));
        $stop = date('Y-m-d', strtotime($request->opt_end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView(
            'sales_report.opt_sales_report_pdf',
            compact('requestValue', 'location')
        );

        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('OutdoorPaymentTerminalReport.pdf');
    }


    public function oew_print_pdf(Request $request)
    {
        // die("ehlwo");
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();
        $start = date('Y-m-d', strtotime($request->oew_start_date));
        Log::debug('oew_print_pdf: oew_start_date:' . $request->oew_start_date);

        $stop = date('Y-m-d', strtotime($request->oew_end_date));
        Log::debug('oew_print_pdf: oew_end_date:' . $request->oew_end_date);

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $products = DB::table('product')->get();
        //Log::debug('oew_print_pdf: products='.json_encode($products));

        $product_details = [];
        foreach ($products as $product) {
            $item_amount = DB::table('oew_receiptdetails')->
			join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=',
				'oew_receiptdetails.receipt_id')->
			join('oew_receipt', 'oew_receiptproduct.receipt_id', '=',
			'oew_receipt.id')->
			where('oew_receiptproduct.product_id', '=', $product->id)->
			where('oew_receipt.status', '!=', 'voided')->
			whereBetween('oew_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('item_amount');

            $quantity = DB::table('oew_receiptdetails')->
			join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=',
				'oew_receiptdetails.receipt_id') ->
			join('oew_receipt',
				'oew_receiptproduct.receipt_id', '=', 'oew_receipt.id')->
			where('oew_receiptproduct.product_id', '=', $product->id)->
			where('oew_receipt.status', '!=', 'voided')->
			whereBetween('oew_receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('oew_receiptproduct.quantity');

            $refund_amount = DB::table('oew_receiptlist')->
			join('oew_receipt', 'oew_receipt.id',
				'oew_receiptlist.oew_receipt_id')->
			join('oew_receiptproduct',
				'oew_receiptproduct.receipt_id','=','oew_receipt.id')->
			join('product', 'product.id','oew_receiptproduct.product_id')->
			where('oew_receipt.status', '!=','voided')->
			where('oew_receiptproduct.product_id',$product->id)->
			whereBetween('oew_receiptlist.created_at',
				[$start .  ' 00:00:00', $stop . ' 23:59:59'])->
			sum('oew_receiptlist.refund');

            $refund_amount_tax = DB::table('oew_receiptlist')->
			join('oew_receipt', 'oew_receipt.id',
				'oew_receiptlist.oew_receipt_id')->
			join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=',
				'oew_receipt.id')->
			join('product', 'product.id','oew_receiptproduct.product_id')->
			where('oew_receipt.status', '!=','voided')->
			where('oew_receiptproduct.product_id', $product->id)->
			whereBetween('oew_receiptlist.created_at',
				[$start .  ' 00:00:00', $stop . ' 23:59:59'])->
			sum(DB::raw('oew_receiptlist.refund / (1 + oew_receipt.service_tax / 100)'));

            $price = DB::table('oew_receiptproduct')->
			join('oew_receipt', 'oew_receipt.id',
				'oew_receiptproduct.receipt_id')->
			join('product', 'product.id','oew_receiptproduct.product_id')->
			where('oew_receipt.status', '!=', 'voided')->
			where('oew_receiptproduct.product_id', $product->id)->
			whereBetween('oew_receiptproduct.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			select('oew_receiptproduct.price')->
			first();

            $refund_qty = DB::table('oew_receiptlist')->
			join('oew_receipt', 'oew_receipt.id',
				'oew_receiptlist.oew_receipt_id')->
			join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=',
				'oew_receipt.id')->
			join('product', 'product.id','oew_receiptproduct.product_id')->
			where('oew_receipt.status', '!=', 'voided')->
			where('oew_receiptproduct.product_id', $product->id)->
			whereBetween('oew_receiptlist.created_at',
				[$start .  ' 00:00:00', $stop . ' 23:59:59'])->
			sum('oew_receiptlist.refund_qty');

            if ($refund_amount > 0) {
                $refund_quantity = $refund_qty;
            } else {
                $refund_quantity = 0;
            }

            $net_quantity = $quantity - $refund_quantity;
            $net_branch_sales = $item_amount - $refund_amount_tax;

            Log::info('oew_print_pdf: net_branch_sales=' . $net_branch_sales);

            if ($item_amount > 0) {
                $product->item_amount = $item_amount - round($refund_amount_tax);
                $product->quantity = $quantity - $refund_quantity;
                $product->net_quantity = $net_quantity;
                $product->net_branch_sales = $net_branch_sales;

                Log::debug('oew_print_pdf: product= ' . json_encode($product));

                $product_details[] = $product;
            }
        }

        Log::debug('oew_print_pdf: item_amount=' . $item_amount);
        Log::debug('oew_print_pdf: product_details=' .
            json_encode($product_details));

        $refund = DB::table('oew_receiptlist')->
		join('oew_receipt', 'oew_receipt.id',
			'oew_receiptlist.oew_receipt_id')->
		join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=',
			'oew_receipt.id')->
		join('product', 'product.id', 'oew_receiptproduct.product_id')->
		where('oew_receipt.status', '!=', 'voided')->
		whereNotNull('oew_receiptlist.refund')->
		where('oew_receiptlist.refund', ">", 0)->
		whereBetween('oew_receiptlist.created_at',
			[$start . ' 00:00:00', $stop . ' 23:59:59'])->
		select(
			'product.name',
			'product.systemid',
			'oew_receipt.service_tax as tax',
			'oew_receiptlist.refund as refund_amount',
			'oew_receiptproduct.price as price',
			'oew_receiptlist.refund_qty')->
		get();

        foreach ($refund as $ref) {
            $ref->refund_amount = $ref->refund_amount / (1 + $ref->tax / 100);
        }

        // dd($product_details);
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true])->
		loadView(
            'sales_report.oew_sales_report_pdf',
            compact(
                'product_details',
                'requestValue',
                'location',
                'refund',
                'currency'
            )
        );

        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('OutdooreWalletSalesReport.pdf');
    }

    public function evPrintPDF(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();
        $start = date('Y-m-d', strtotime($request->ev_start_date));
        $stop = date('Y-m-d', strtotime($request->ev_end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $carparklots = DB::table('carparklot')->get();

        $carparklot_details = [];
        foreach ($carparklots as $carparklot) {

            $hours = DB::table('evreceiptdetails')->
			join('evreceipt', 'evreceiptdetails.evreceipt_id', '=',
				'evreceipt.id') ->
			join('evreceiptlist', 'evreceiptlist.evreceipt_id', '=',
				'evreceipt.id') ->
			join('carpark_oper', 'carpark_oper.id', '=',
				'evreceiptlist.carpark_oper_id')->
			where('carpark_oper.carparklot_id', '=', $carparklot->id)->
			where('evreceipt.status', '!=', 'voided')->
			whereBetween('evreceiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('evreceipt.hours');

            $item_amount = DB::table('evreceiptdetails')->
			join('evreceipt', 'evreceiptdetails.evreceipt_id', '=',
				'evreceipt.id')->
			join('evreceiptlist', 'evreceiptlist.evreceipt_id', '=',
				'evreceipt.id')->
			join('carpark_oper', 'carpark_oper.id', '=',
				'evreceiptlist.carpark_oper_id')->
			where('carpark_oper.carparklot_id', '=', $carparklot->id)->
			where('evreceipt.status', '!=', 'voided')->
			whereBetween('evreceiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('evreceiptdetails.item_amount');

            if ($item_amount > 0) {
                $carparklot->lot_no = $carparklot->lot_no;
                $carparklot->lot_id = $carparklot->systemid;
                $carparklot->hours  = $hours;
                $carparklot->net_branch_sales = $item_amount;
                Log::debug('carparklot: ' . json_encode($carparklot));
                $carparklot_details[] = $carparklot;
            }
        }

        Log::debug('Sales: ' . json_encode($carparklot_details));

        $receipts = Evreceipt::query()->
			whereBetween('created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			get();

        $total_hours = 0;
        $total_hour_amount = 0;
        $total_kwh = 0;
        $total_kwh_amount = 0;

        foreach ($receipts as $receipt) {
            $details = $receipt->receiptDetails()->first();

            if ($details != null) {
                if ($receipt['hours'] != 0) {
                    $total_hours = $total_hours + $receipt['hours'];
                    $total_hour_amount = $total_hour_amount +
						$details['item_amount'];
                } else {
                    $total_kwh = $total_kwh + $receipt['kwh'];
                    $total_kwh_amount = $total_kwh_amount +
						$details['item_amount'];
                }
            }
        }

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true,
			'isRemoteEnabled' => true])->
			loadView('sales_report.ev_sales_report_pdf',
                compact(
                    'carparklot_details',
                    'requestValue',
                    'location',
                    'currency',
                    'total_hours',
                    'total_hour_amount',
                    'total_kwh',
                    'total_kwh_amount'
                )
            );

        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ])
        );
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('SalesReport.pdf');
    }


    public function h2PrintPDF(Request $request)
    {
        Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();
		$start = date('Y-m-d', strtotime($request->h2_start_date));
		//strtotime($request->h2_start_date)); $stop = date('Y-m-d',

		//strtotime($request->h2_end_date));

		Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $products = DB::table('product')->where('ptype', 'h2')->get();

        $product_details = [];
        foreach ($products as $product) {
            $item_amount = DB::table('h2receiptdetails')->
			join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
				'h2receiptdetails.receipt_id') ->
			join('h2receipt', 'h2receiptproduct.receipt_id', '=',
				'h2receipt.id') ->
			where('h2receiptproduct.product_id', '=', $product->id)->
			where('h2receipt.status', '!=', 'voided')->
			whereBetween('h2receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('item_amount');

            $quantity = DB::table('h2receiptdetails')->
			join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
				'h2receiptdetails.receipt_id') ->
			join('h2receipt', 'h2receiptproduct.receipt_id', '=',
				'h2receipt.id') ->
			where('h2receiptproduct.product_id', '=', $product->id) ->
			where('h2receipt.status', '!=', 'voided')->
			whereBetween('h2receiptdetails.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('h2receiptproduct.quantity');

            $refund_amount = DB::table('h2receiptlist')->
			join('h2receipt', 'h2receipt.id', 'h2receiptlist.h2receipt_id')->
			join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
				'h2receipt.id')->
			join('product', 'product.id', 'h2receiptproduct.product_id')->
			where('h2receipt.status', '!=', 'voided')->
			where('product.ptype', 'h2')->
			where('h2receiptproduct.product_id', $product->id)->
			whereBetween('h2receiptlist.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('h2receiptlist.refund');

			$refund_amount_tax = DB::table('h2receiptlist')->
			join('h2receipt', 'h2receipt.id', 'h2receiptlist.h2receipt_id')->
			join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
				'h2receipt.id')->
			join('product', 'product.id', 'h2receiptproduct.product_id')->
			where('h2receipt.status', '!=', 'voided')->
			where('product.ptype', 'h2')->
			where('h2receiptproduct.product_id', $product->id)->
			whereBetween('h2receiptlist.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum(DB::raw('h2receiptlist.refund / (1 + h2receipt.service_tax / 100)'));

			$price = DB::table('h2receiptproduct')->
			join('h2receipt', 'h2receipt.id', 'h2receiptproduct.receipt_id')->
			join('product', 'product.id', 'h2receiptproduct.product_id')->
			where('h2receipt.status', '!=', 'voided')->
			where('product.ptype', 'h2')->
			where('h2receiptproduct.product_id', $product->id)->
			whereBetween('h2receiptproduct.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			select('h2receiptproduct.price')->first();

			$refund_qty = DB::table('h2receiptlist')->
			join('h2receipt', 'h2receipt.id', 'h2receiptlist.h2receipt_id')->
			join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
				'h2receipt.id')->
			join('product', 'product.id', 'h2receiptproduct.product_id')->
			where('h2receipt.status', '!=', 'voided')->
			where('product.ptype', 'h2')->
			where('h2receiptproduct.product_id', $product->id)->
			whereBetween('h2receiptlist.created_at',
				[$start . ' 00:00:00', $stop . ' 23:59:59'])->
			sum('h2receiptlist.refund_qty');

			if ($refund_amount > 0) {
                $refund_quantity = $refund_qty;
            } else {
                $refund_quantity = 0;
            }
            $net_quantity = $quantity - $refund_quantity;
            $net_branch_sales = $item_amount - $refund_amount_tax;

            Log::info('h2PrintPDF: net_branch_sales=' . $net_branch_sales);

            if ($item_amount > 0) {
                $product->item_amount = $item_amount - round($refund_amount_tax);
                $product->quantity = $quantity - $refund_quantity;
                $product->net_quantity = $net_quantity;
                $product->net_branch_sales = $net_branch_sales;

                Log::debug('H2 Product: ' . json_encode($product));

                $product_details[] = $product;
            }
		}


        $refund = DB::table('h2receiptlist')->
		join('h2receipt', 'h2receipt.id', 'h2receiptlist.h2receipt_id')->
		join('h2receiptproduct', 'h2receiptproduct.receipt_id', '=',
			'h2receipt.id') ->
		join('product', 'product.id', 'h2receiptproduct.product_id')->
		where('h2receipt.status', '!=', 'voided')->
		where('product.ptype', 'h2')->
		whereNotNull('h2receiptlist.refund')->
		where('h2receiptlist.refund', ">", 0)->
		whereBetween('h2receiptlist.created_at',
			[$start . ' 00:00:00', $stop . ' 23:59:59'])->
		select(
			'product.name',
			'product.systemid',
			'h2receipt.service_tax as tax',
			'h2receiptlist.refund as refund_amount',
			'h2receiptproduct.price as price',
			'h2receiptlist.refund_qty')->
		get();

        foreach ($refund as $ref) {
            $ref->refund_amount = $ref->refund_amount / (1 + $ref->tax / 100);
        }

        // dd($product_details);
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true,
			'isRemoteEnabled' => true])->
			loadView(
                'sales_report.h2_sales_report_pdf',
                compact(
					'product_details',
					'requestValue',
					'location',
					"refund"
				)
            );

        //compact('product_details', 'requestValue', 'location', 'refund', 'currency'));

        $pdf->getDomPDF()->setBasePath(public_path() . '/');
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ])
        );

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('HydrogenSalesReport.pdf');
    }

    public function terminalDate()
    {
        $created_at = DB::table('terminal')->select('created_at')->first();
        return $created_at->created_at;
    }

    public function storeExcel(Request $request)
    {
        $comm_receipts = [];
        Log::info($request->date_excel);
        if ($request->date_excel) {
            $comm_receipts = CommReceipt::with(["location", "user"])->
			whereBetween('created_at', [$request->date_excel .
			" 00:00:00", $request->date_excel . " 23:59:59"])->
			get();

        } else {
            $comm_receipts = CommReceipt::with(["location", "user"])->get();
        }

        $filename = time() . "_fuel_usage.xlsx";
        Excel::store(new FuelUsageExport(
			$comm_receipts), $filename, "excel_disk");

        return "exports/" . $filename;
    }

    // fuel_sales_summary
    public function generate_fuelsales_summary_pdf(Request $request)
    {
        $start = date('Y-m-d H:i:s', strtotime($request->fuel_start_date));
        $stop = date('Y-m-d 23:59:59', strtotime($request->fuel_end_date));

        ExcelExportController::saveExcelFuelReceiptlist($request);
        $file_path = 'fuel/'. $request->fuel_start_date.'-'. $request->fuel_stop_date
            . 'fuel_receipt_list.xlsx';

        Excel::import(new ExcelExtractor($request), storage_path($file_path));

       $parentId = DB::table('fuelsales_summary')->
            where('start', $start)->
            where('end', $stop)->
            first();
        

        if(!empty($parentId)){
            $receipt_sld =  DB::table('fuelsales_summary_detail')->
            where('fuelsales_summary_id',$parentId->id)->
            get();
        }

         $pdf = PDF::loadView('sales_report.fuel_sales_summary_pdf', compact(
            'receipt_sld',
            'start',
            'stop'
        ))->setPaper('A4', 'portrait');
        return  $pdf->download('FuelSales_Summary.pdf');
    }
}
