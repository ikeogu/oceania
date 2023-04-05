<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use ZipArchive;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    //
    public function getReport()
    {
        $company = Company::first();
        $approvedDate = $company->approved_at;
        return view('report.report', ['approved_at' => $approvedDate]);
    }

    public function cstorePLPDF(Request $request)
    {
        Log::info('***** cstorePLPDF: START *****');
        Log::info('Request: ' . json_encode($request->all()));

        $company = Company::first();
        $currency = $company->currency->code ?? 'MYR';

        //Change date Format
        $requestValue = $request->all();

        if (!$request->ev_start_date) {
            $request->ev_start_date = date('Y-m-d');
        }
        if (!$request->ev_end_date) {
            $request->ev_end_date = date('Y-m-d');
        }

        $start = date('Y-m-d', strtotime($request->ev_start_date));
        $stop = date('Y-m-d', strtotime($request->ev_end_date));

        $start = $start . ' 00:00:00';

        $stop = $stop . ' 23:59:59';

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $location = Location::first();

        $query = "
            SELECT
                r.id,
                p.systemid,
                rp.name,
                p.ptype as ptype,
                rp.price as price,
                rp.product_id as product_id,
                opl.cost as cost,
                @qty := CASE WHEN rp.quantity < 0 THEN (rp.quantity * -1) ELSE rp.quantity END as qty,
                ((CAST(rp.price AS SIGNED) - CAST(opl.cost AS SIGNED)) * @qty) as profit_loss,
                opl.type,
                IFNULL(b.barcode,NULL) as  barcode,
                r.created_at as created_at
            FROM
                cstore_receipt r,
                cstore_receiptproduct rp,
                openitem_productledger opl,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            WHERE
                r.id = rp.receipt_id AND
                rp.product_id = p.id AND
                opl.csreceipt_id = r.id AND
                r.created_at BETWEEN '$start' AND '$stop' AND
                r.created_at is not null AND
                opl.product_systemid =p.systemid AND
                opl.type = 'cash_sales'
            ORDER BY
                rp.name ASC

            ;
        ";
        Log::debug('Query: ' . $query);
        $report_details = collect(DB::select(DB::raw($query)));
        $report_details = $this->collection_transformer($report_details);

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView(
                'report.cstore_profit_loss_pdf',
                compact(
                    'currency',
                    'report_details',
                    'requestValue',
                    'location'
                )
            );

        $pdf->setPaper('A4', 'portrait');
        ini_set('memory_limit', '-1');
        $ret = $pdf->download('C-StoreProfitLoss.pdf');

        Log::info('***** cstorePLPDF: END *****');

        return $ret;
    }

    public function getProductBarcode($id, $systemid)
    {
        $barcode = DB::table('productbarcode')
            ->where('product_id', $id)
            ->where('selected', 1)
            ->first();

        if (!is_null($barcode)) {
            return $barcode->barcode;
        } else {
            return $systemid;
        }
    }


    public function cost_value_reportPDF(Request $request)
    {
        Log::info('***** cost_value_reportPDF: START *****');
        Log::info('Request: ' . json_encode($request->all()));
        $company = Company::first();
        $location = DB::table('location')->first();
        $currency = $company->currency->code ?? 'MYR';

        //Change date Format
        is_null($request->year) ? $start_d = date("Y-m-01") :
            $start_d = $request->year . '-' . $request->month . '-01';

        $requestValue = [
            'start_date' => date('Y-m-01', strtotime($start_d)),
            'end_date' => date('Y-m-t', strtotime($start_d)),
        ];

        $start = date('Y-m-01', strtotime($start_d));
        $end = date('Y-m-t', strtotime($start_d));
        $end_D = date('1970-Jan-01');

        Log::info("cost_value_reportPDF: start inventory_product");

        $query = "
            SELECT
                p.id,
                p.systemid,
                p.name,
                opc.cost as Icost,
                p_o.costvalue as Icostvalue,
                p_o.price as Iprice,
                opc.balance as Iqty,
                b.barcode as barcode
            FROM
                prd_openitem p_o
            LEFT JOIN
                product p ON p.id = p_o.product_id
            LEFT JOIN productbarcode b ON b.product_id = p_o.product_id AND
                b.deleted_at IS NULL AND
                b.selected = 1
            LEFT JOIN
                openitem_productledger opl ON opl.product_systemid =p.systemid
            LEFT JOIN
                openitem_cost opc ON opc.openitemprodledger_id = opl.id
            WHERE opc.updated_at is not NULL  AND
                (opl.type ='stockin' OR  opl.type='received' OR opl.type='returned') AND
                opc.cost <> 0 AND
                opc.updated_at BETWEEN '" . $end_D . "  00:00:00' AND '" . $end . " 23:59:59'
            ORDER By
                id, Icost
            ;
        ";

        $inv_and_open_products = collect(DB::select(DB::raw($query)));

        Log::info("cost_value_reportPDF: inv_and_open_products=" .
            count($inv_and_open_products));

        $inv_and_open_products = $this->collectionReceiver($inv_and_open_products);


        $allproducts = $this->getAllProducts();

        Log::info("cost_value_reportPDF: allproducts=" . count($allproducts));

        $filtered_collection = $allproducts->filter(function ($item)
			use ($inv_and_open_products) {
            $ids = $inv_and_open_products->pluck('id')->toArray();
            return !in_array($item->id, $ids);
        })->values();

        Log::info("cost_value_reportPDF: end filtered_array");

        $openitem_prod = $filtered_collection->merge($inv_and_open_products);

        Log::info("cost_value_reportPDF: end merged allfiltered_collection");
        Log::info("cost_value_reportPDF: openitem_prod=" . count($openitem_prod));

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('report.cost_value_rpt_pdf', compact(
                'openitem_prod',
                'requestValue',
                'location',
                'currency'
            ));
        $pdf->setPaper('A4', 'portrait');
        ini_set('memory_limit', '-1');
        $ret = $pdf->download('CostValueReport.pdf');

        Log::info('***** cost_value_reportPDF: END *****');

        return $ret;
    }


    public function getAllProducts()
    {
        Log::info('***** getAllProducts: START *****');

        $data = collect(DB::select(DB::raw("
            SELECT
                p.id,
                p.systemid,
                p.name,
                ptype as ptype,
                null as Icostvalue,
                null as Iprice,
                null as Icost,
                null as Iqty,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                product p,
                prd_openitem p_o
            LEFT JOIN
                productbarcode b ON b.product_id = p_o.product_id AND
                b.selected = 1 AND
                b.deleted_at is null

            WHERE
                p_o.product_id = p.id
            ;
        ")));

        Log::info('getAllProducts: END ');

        return $data;
    }

    public function collectionReceiver($collect)
    {
        Log::info("***** collectionReceiver: START *****");

        $new_arr = collect();
        foreach ($collect->values()->all() as $key => $value) {
            # code...
            if (
                $new_arr->where('id', $value->id)->where('Icost', $value->Icost)->count() > 0
            ) {
                # code...

                $h = $new_arr->where('id', $value->id)->where('Icost', $value->Icost)->first();

                $h->Iqty += $value->Iqty;
                continue;
            }
            $new_arr->push($value);
        }
        Log::info("collectionReceiver: new_arr=" . count($new_arr));

        Log::info("***** collectionReceiver: END *****");

        return $new_arr;
    }

    public function collection_transformer($collect)
    {
        $new_arr = collect();
        foreach ($collect->values()->all() as $key => $value) {
            # code...
            if (
                $new_arr->where('product_id', $value->product_id)->where('price', $value->price)->
                where('cost', $value->cost)->count() > 0
            ) {
                # code...
                $h = $new_arr->where('product_id', $value->product_id)->where('price', $value->price)->
                    where('cost', $value->cost)->first();
                if ($h) {
                    $h->qty += $value->qty;
                    $h->profit_loss += $value->profit_loss;
                }

                continue;
            }
            $new_arr->push($value);
        }
        return $new_arr;
    }


    public function get_barcode($id)
    {

        $productbarcode = DB::select(
            // DB::raw("
            //     select
            //         JSON_ARRAYAGG(b.barcode) as barcode
            //     from
            //         productbarcode b,
            //         product p
            //     where
            //         p.id = b.product_id  and
            //         p.id ='" . $id . "' and
            //         b.selected = 1 and
            //         b.deleted_at is null;
            // ")
            DB::raw("
            select b.barcode as barcode from  productbarcode b, product p where p.id = b.product_id  and p.id ='" . $id . "' and b.selected = 1 and b.deleted_at is null;
            ")
        );

        if (sizeof($productbarcode) > 0) {
            return $productbarcode[0]->barcode;
        } else {
            return null;
        }
    }

    public function products()
    {
        $openitem_details = DB::table('prd_openitem')->
			leftjoin('product', 'prd_openitem.product_id', '=', 'product.id')->
			leftjoin('openitem_productledger',
			'openitem_productledger.product_systemid', '=', 'product.systemid')->
			leftjoin('openitem_cost', 'openitem_cost.openitemprodledger_id', '=',
			'openitem_productledger.id')->
			groupBy(
				'product.id',
				'product.systemid',
				'product.name',
				'prd_openitem.price',
				'openitem_cost.qty_out',
				'openitem_cost.cost')->
			select(
                'product.id as id',
                'product.systemid as systemid',
                'product.name  as name',
                DB::raw('null as price'),
                DB::raw('null as cost'),
                DB::raw('null as qty'),
                DB::raw('null as profit_loss')
            )->get();

        return $openitem_details;
    }
}
