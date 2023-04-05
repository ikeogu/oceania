<?php

namespace App\Exports;

use DB;
use Log;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockLedgerExport extends DefaultValueBinder implements FromView, WithEvents, WithColumnFormatting, WithCustomValueBinder, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    protected $tableHeaderLength = 6;
    /**
     * @return \Illuminate\Support\view
     */
    public function view(): View
    {
        Log::info('***** SLR view: START *****');

        if (is_null($this->request)) {
            $start = date("Y-m-01");
            $stop = date("Y-m-t");
        } else {
            $start = date('Y-m-d', strtotime($this->request->sl_start_date));
            $stop = date('Y-m-d', strtotime($this->request->sl_end_date));
        }

        $stockledger_query = "
            SELECT
                sr.id,
 	            p.id as product_id,
                p.systemid as product_systemid,
                p.ptype,
                sr.systemid,
                sr.type,
                s.updated_at as last_update,
                s.quantity as qty,
                s.created_at
            FROM
                locprod_productledger l,
                locationproduct_cost c,
                stockreportproduct s,
                stockreport sr,
                product p
            WHERE
                s.stockreport_id = sr.id  AND
                s.product_id = p.id AND
                l.product_systemid = p.systemid AND
                l.id = c.locprodprodledger_id  AND
                sr.created_at BETWEEN '" . $start .
            " 00:00:00' AND '" . $stop . " 23:59:59'
            UNION
            SELECT
                sr.id,
                p.id as product_id,
                p.systemid  as product_systemid,
                p.ptype,
                sr.systemid,
                sr.type,
                s.updated_at as last_update,
                s.quantity as qty,
                s.created_at
            FROM.
                openitem_productledger o,
                openitem_cost c,
                stockreportproduct s,
                stockreport sr,
                product p
            WHERE
                sr.id = s.stockreport_id  AND
                s.product_id = p.id AND
                o.product_systemid = p.systemid AND
                o.id = c.openitemprodledger_id  AND
                o.product_systemid = p.systemid AND
                sr.created_at BETWEEN '" . $start .
            " 00:00:00' AND '" . $stop . " 23:59:59'
            ;
        ";

        $cstore_query = "
            SELECT
                cr.id as id,
                crp.quantity as qty,
                cr.systemid,
                'cash_sales' as type,
                p.id as product_id,
                crd.id as receiptdetails_id,
                p.systemid as product_systemid,
                p.ptype,
                cr.updated_at as last_update,
                cr.created_at as created_at
            FROM
                cstore_receiptproduct crp,
                cstore_receipt cr,
                cstore_receiptdetails crd,
                product p,
                openitemcost_qtydist ocr,
                openitem_cost oc
            WHERE
                crp.receipt_id = cr.id  AND
                crd.receipt_id = cr.id  AND
                crp.product_id = p.id   AND
                ocr.openitemcost_id =oc.id AND
                cr.created_at BETWEEN '" . $start .
            " 00:00:00' AND '" . $stop . " 23:59:59'
            UNION
            SELECT
                cr.id as id,
                crp.quantity as qty,
                cr.systemid,
                'cash_sales' as type,
                p.id as product_id,
                crd.id as receiptdetails_id,
                p.systemid as product_systemid,
                p.ptype,
                cr.updated_at as last_update,
                cr.created_at as created_at
            FROM
                cstore_receiptproduct crp,
                cstore_receipt cr,
                cstore_receiptdetails crd,
                locprodcost_qtydist lcr,
                locationproduct_cost c,
                product p
            WHERE
                crp.receipt_id = cr.id  AND
                crd.receipt_id = cr.id  AND
                crp.product_id = p.id   AND
                lcr.locprodcost_id = c.id  AND
                cr.created_at BETWEEN '" . $start .
            " 00:00:00' AND '" . $stop . " 23:59:59'
            GROUP BY
                product_systemid,
                systemid,
                type,
                ptype,
                receiptdetails_id,
                qty,
                last_update,
                cr.id,
                p.id,
                created_at

            ;
        ";
        $all_products_query = "
            SELECT
                p.name,
                p.systemid
            FROM
                product p,
                prd_inventory pi
            WHERE
                p.id = pi.product_id
            UNION
            SELECT
                p.name,
                p.systemid
            FROM
                product p,
                prd_openitem op
            WHERE
                p.id = op.product_id
            ;

        ";
        Log::info("SLR view: BEFORE running query");

        $stockledger_query = collect(DB::select(DB::raw($stockledger_query)));

        Log::info("SLR view: finished stockledger_query=" . count($stockledger_query));

        $cstore_query = collect(DB::select(DB::raw($cstore_query)));

        Log::info("SLR view: finished cstore_query=" . count($cstore_query));

        $all_products_query = collect(DB::select(DB::raw($all_products_query)));
        Log::info("SLR view: finished all_products_query=" . count($all_products_query));

        // Log::info('SLR view: prod_o='.count($prod_o));

        // $products = array_merge($prod_o, $prod_i);

        $products = $all_products_query->sortBy('name')->values();
        $stockledger = $stockledger_query->merge($cstore_query);

        /*  Log::info('SLR view: BEFORE distinct_value stockledger_query and cstore_query');
        $stockledger = $this->distinct_values($stockledger)->values();
        Log::info('SLR view: AFTER distinct_value stockledger_query and cstore_query'. count($stockledger));
 */
        $result = [];
        Log::info("SLR view: BEFORE FOR LOOP");
        foreach ($stockledger as $key => $stk) {
            $query = "
                SELECT
                    oqd.*,
                    oc.*
                FROM
                    openitemcost_qtydist oqd
                LEFT JOIN
                    openitem_cost oc ON oc.id = oqd.openitemcost_id
                WHERE
                    oqd.csreceipt_id = $stk->id AND
                    oc.qty_in <> 0 AND
                    oqd.qty_taken <> 0
                ;
            ";
            $query2 = "
                SELECT
                    opl.*
                FROM
                    openitem_productledger opl
                WHERE
                    opl.stockreport_id = $stk->id
                LIMIT 1
                ;
            ";
            $query3 = "
                SELECT
                    lqd.*,
                    lc.*
                FROM
                    locprodcost_qtydist lqd
                LEFT JOIN
                    locationproduct_cost lc ON lc.id = lqd.locprodcost_id
                WHERE
                    lqd.csreceipt_id = $stk->id AND
                    lc.qty_in <> 0 AND
                    lqd.qty_taken <> 0
                ;
            ";
            $query4 = "
                SELECT
                    lpl.*
                FROM
                    locprod_productledger lpl
                WHERE
                    lpl.stockreport_id = $stk->id
                LIMIT 1
                ;
            ";
            $query5 = "
                SELECT
                    oqd.*,
                    oc.*
                FROM
                    openitemcost_qtydist oqd
                LEFT JOIN
                    openitem_cost oc ON oc.id = oqd.openitemcost_id
                WHERE
                    oqd.stockreport_id = $stk->id
                ;
            ";
            $query6 = "
                SELECT
                    lqd.*,
                    lc.*
                FROM
                    locprodcost_qtydist lqd
                LEFT JOIN
                     locationproduct_cost lc ON lc.id = lqd.locprodcost_id
                WHERE
                    lqd.stockreport_id = $stk->id
                ;
            ";

            if ($stk->ptype == "openitem") {
                if ($stk->type == "cash_sales") {

                    /* $cost_dist = DB::table('openitemcost_qtydist')
                        ->leftjoin('openitem_cost', 'openitem_cost.id', 'openitemcost_id')
                        ->whereRaw('openitem_cost.qty_in <> 0')
                        ->where('csreceipt_id', $stk->id)
                        ->where('qty_taken','<>',0)
                        ->get(); */
                    $cost_dist = collect(DB::select(DB::raw("$query")));

                    $p_costs = $this->merge__costs($cost_dist, $stk);
                    if (sizeof($p_costs) > 0) {
                        $result = array_merge($result, $p_costs);
                    }
                    continue;
                } elseif ($stk->type == "stockin" || $stk->type == "received") {

                    $cost = collect(DB::select(DB::raw($query2)))->first();

                    /* $cost = DB::table('openitem_productledger')
                        ->where('stockreport_id', $stk->id)
                        ->first(); */

                    $qty = !empty($cost) ? $cost->qty : 0;
                    $cst = !empty($cost) ? $cost->cost / 100 : 0;

                    array_push($result, (object) [
                        "id" => $stk->id,
                        "systemid" => $stk->systemid,
                        "type" => $stk->type,
                        "product_id" => $stk->product_id,
                        "product_systemid" => $stk->product_systemid,
                        "ptype" => $stk->ptype,
                        "last_update" => $stk->last_update,
                        "qty" => $qty,
                        "cost" => $cst,
                    ]);
                    continue;
                } else {
                    /*  $cost_dist = DB::table('openitemcost_qtydist')
                        ->leftjoin('openitem_cost', 'openitem_cost.id', 'openitemcost_id')
                        ->where('stockreport_id', $stk->id)
                        ->get(); */
                    $cost_dist = collect(DB::select(DB::raw($query5)));
                    $p_costs = $this->merge__costs($cost_dist, $stk);
                    if (sizeof($p_costs) > 0) {
                        $result = array_merge($result, $p_costs);
                    }
                    continue;
                }
            } elseif ($stk->ptype == "inventory") {
                if ($stk->type == "cash_sales") {
                    /* $cost_dist = DB::table('locprodcost_qtydist')
                        ->leftjoin('locationproduct_cost', 'locationproduct_cost.id', 'locprodcost_id')
                        ->whereRaw('locationproduct_cost.qty_in <> 0')
                        ->where('csreceipt_id', $stk->id)
                        ->where('qty_taken', '<>', 0)
                        ->get(); */
                    $cost_dist = collect(DB::select(DB::raw($query3)));
                    $p_costs = $this->merge__costs($cost_dist, $stk);
                    if (sizeof($p_costs) > 0) {
                        $result = array_merge($result, $p_costs);
                    }
                } elseif ($stk->type == "stockin" || $stk->type == "received") {
                    /* $cost = DB::table('locprod_productledger')
                        ->where('stockreport_id', $stk->id)
                        ->first(); */
                    $cost = collect(DB::select(DB::raw($query4)))->first();;
                    $qty = !empty($cost) ? $cost->qty : 0;
                    $cst = !empty($cost) ? $cost->cost / 100 : 0;

                    array_push($result, (object) [
                        "id" => $stk->id,
                        "systemid" => $stk->systemid,
                        "type" => $stk->type,
                        "product_id" => $stk->product_id,
                        "product_systemid" => $stk->product_systemid,
                        "ptype" => $stk->ptype,
                        "last_update" => $stk->last_update,
                        "qty" => $qty,
                        "cost" => $cst,
                    ]);
                    continue;
                } else {

                    /*  $cost_dist = DB::table('locprodcost_qtydist')
                        ->leftjoin('locationproduct_cost', 'locationproduct_cost.id', 'locprodcost_id')
                        ->where('stockreport_id', $stk->id)
                        ->get(); */
                    $cost_dist = collect(DB::select(DB::raw($query6)));

                    $p_costs = $this->merge__costs($cost_dist, $stk);
                    if (sizeof($p_costs) > 0) {
                        $result = array_merge($result, $p_costs);
                    }
                    continue;
                }
            }
        }

        Log::info('SLR view: AFTER FOR LOOP');

        $stockledger = (sizeof($result) > 0) ? $result : $stockledger;

        $collection = collect($stockledger);

        $stockledger = $collection->sortBy('last_update');
        $stockledger = $stockledger->reverse();


        $ret = view('excel_export.stock_ledger_excel', [
            'stock_ledgers' => $stockledger,
            'products' => $products,
            'start_date' => $start,
            'stop_date' => $stop,
        ]);

        Log::info('***** SLR view: END *****');

        return $ret;
    }


    public function sort_date($a, $b)
    {
        #2022-07-07 22:54:38
        return \DateTime::createFromFormat('Y-m-d H:i:s', $a) <=> \DateTime::createFromFormat('Y-m-d H:i:s', $b);
    }

    public function merge__costs($values, $parent): array
    {

        $result = [];
        foreach ($values as $cost) {
            $el = (object) [
                "id" => $parent->id,
                "systemid" => $parent->systemid,
                "type" => $parent->type,
                "product_id" => $parent->product_id,
                "receiptdetails_id" => $parent->receiptdetails_id ?? 0,
                "product_systemid" => $parent->product_systemid,
                "ptype" => $parent->ptype,
                "last_update" => $parent->last_update,
                "qty" => ($cost->qty_taken * -1),
                "cost" => $cost->cost / 100,
            ];
            array_push($result, $el);
        }
        return $result;
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {

        if (is_numeric($value) && strlen($value) > 10) {

            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {

        return [

            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(18);

                $event->sheet->getDelegate()->getStyle(static::columnLetter($this->tableHeaderLength))
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('000000');

                $event->sheet->getStyle(static::columnLetter(
                    $this->tableHeaderLength
                ))->getFont()->setBold(true)->getColor()->setRGB('ffffff');
            },

        ];
    }

    public static function columnLetter($c)
    {

        $c = intval($c);
        if ($c <= 0) {
            return '';
        }

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A3:' . $letter . '3';
    }

    public function cellColor($cells, $color)
    {
        global $objPHPExcel;

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startcolor' => array(
                    'rgb' => $color,
                ),
            ));
    }

    public function distinct_values($collect)
    {
        Log::info("***** distinct_values: START *****");

        $new_collection = collect();
        foreach ($collect->values()->all() as $value) {

            if ($new_collection->where('id', $value->id)->where('type', $value->type)->count() > 0
            ) {

                continue;
            } else {

                $new_collection->push($value);
            }
        }
        Log::info("***** distinct_values: END *****");
        return $new_collection;
    }
}
