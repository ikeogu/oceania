<?php

namespace App\Http\Controllers;

use App\Classes\FuelUsageExport;
use App\Exports\InvoicesExport;
use App\Models\CommReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PDF;
use App\Models\Company;

class CStoreSalesReportController extends Controller
{
    //
    public function printPDF(Request $request)
    {

        Log::info('***** printPDF: START *****');

        Log::info('Request: ' . json_encode($request->all()));

        $company = Company::first();
        $location = DB::table('location')->first();
        $currency = $company->currency->code ?? 'MYR';
        //Change date Format
        $requestValue = $request->all();

        $start = date('Y-m-d', strtotime($request->start_date));
        $stop = date('Y-m-d', strtotime($request->end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $product_details = collect(DB::select(DB::raw("

             SELECT
                p.name,
                p.systemid,
                p.ptype as ptype,
                SUM(cid.amount) as item_amount,
                SUM(crp.quantity) as quantity,
                SUM(cr.service_tax) as tax,
                cr.status
            FROM
                prd_openitem po
            LEFT JOIN
                product p ON p.id = po.product_id
            LEFT JOIN
                cstore_receiptproduct crp ON crp.product_id = po.product_id AND
                crp.created_at BETWEEN  '" . $start . " 00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_receipt cr
                ON crp.receipt_id = cr.id AND
                cr.created_at BETWEEN  '" . $start . "  00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_itemdetails cid
                ON cid.receiptproduct_id = crp.id AND
                cid.created_at BETWEEN  '" . $start . "  00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_receiptdetails crd
                ON crd.receipt_id = cr.id
            GROUP BY
                name,systemid,ptype,status ASC
            UNION
            SELECT
                p.name,
                p.systemid,
                p.ptype as ptype,
                SUM(cid.amount) as item_amount,
                SUM(crp.quantity) as quantity,
                SUM(cr.service_tax) as tax,
                cr.status
            FROM
                prd_inventory pi
            LEFT JOIN
                product p ON p.id = pi.product_id
            LEFT JOIN
                cstore_receiptproduct crp ON crp.product_id = pi.product_id AND
                crp.quantity > 0 AND
                crp.created_at BETWEEN  '" . $start . " 00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_receipt cr
                ON crp.receipt_id = cr.id AND
                cr.created_at BETWEEN  '" . $start . " 00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_itemdetails cid
                ON cid.receiptproduct_id = crp.id AND
                cid.created_at BETWEEN  '" . $start . " 00:00:00'  AND '" . $stop . " 23:59:59'
            LEFT JOIN cstore_receiptdetails crd
                ON crd.receipt_id = cr.id
            GROUP BY
                name,systemid,ptype,status ASC
            ORDER BY
                name ASC
            ;
        ")));
        $product_details = $product_details->
             where('quantity', '>',0)->values();

        Log::debug('product_details count=' . count($product_details));


        $receipt_refund = DB::table('cstore_receipt')->
            join('cstore_receiptrefund', 'cstore_receiptrefund.cstore_receipt_id', '=',
                 'cstore_receipt.id')->
            where('cstore_receipt.status', '!=', 'voided')
            ->whereBetween('cstore_receipt.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])
            ->select('cstore_receipt.systemid', 'cstore_receiptrefund.refund_amount')
            ->get();

        Log::debug('receipt_refund count=' . json_encode($receipt_refund));

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('sales_report.sales_report_pdf', compact(
                'product_details',
                'requestValue',
                'location',
                'receipt_refund',
                'currency'
            ));

        Log::info('printPDF: AFTER PDF::setOptions');

        $pdf->setPaper('A4', 'portrait');

        Log::info('printPDF: AFTER $pdf->setPaper');

        ini_set('memory_limit', '-1');

        $ret = $pdf->download('SalesReport.pdf');

        Log::info('***** printPDF: END *****');

        return $ret;
    }
}
