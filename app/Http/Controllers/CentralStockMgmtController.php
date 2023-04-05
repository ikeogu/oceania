<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Models\Company;
use App\Models\Location;
use App\Models\PrdOpenitem;
use DB;
use Illuminate\Http\Request;
use League\Flysystem\Adapter\NullAdapter;
use Log;
use SebastianBergmann\Type\NullType;
use Yajra\DataTables\DataTables;

class CentralStockMgmtController extends Controller
{
    public function qtyAvailable($prd_id)
    {
        try {
            $product_stock = DB::table('stockreportproduct')->
                where('product_id', $prd_id)->get()->sum('quantity');

            $sales = DB::table('cstore_receipt')->
                select('cstore_receiptproduct.quantity as quantity')->
                join(
                    'cstore_receiptproduct',
                    'cstore_receipt.id',
                    'cstore_receiptproduct.receipt_id'
                )->
                leftJoin(
                    'cstore_receiptdetails',
                    'cstore_receipt.id',
                    'cstore_receiptdetails.receipt_id'
                )->
                orderBy('cstore_receipt.updated_at', "desc")->
                where("cstore_receiptproduct.product_id", $prd_id)->
                whereNotIn('cstore_receipt.status', ['voided'])->get()->sum('quantity');

            return $product_stock - $sales;
        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }

    public function bookValAvailable($prd_id)
    {
        try {

            $product_stock = DB::table('fuelmovement')->join(
                'prd_ogfuel',
                'prd_ogfuel.id',
                'fuelmovement.ogfuel_id'
            )->where('prd_ogfuel.product_id', $prd_id)->first();

            $receipt = DB::table('stockreportproduct')->leftjoin(
                'stockreport',
                'stockreport.id',
                'stockreportproduct.stockreport_id'
            )->where('stockreportproduct.product_id', $prd_id)->get()->sum('quantity');

            return ($product_stock->book ?? 0) + $receipt ?? 0;
        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }
    private function cost_distribution_query($receipt_id, $product_id)
    {
        $query = "
            SELECT
                ocr.locprodcost_id,
                ocr.csreceipt_id,
                ocr.qty_taken,
                oc.cost,
                p.name, p.id as product_id
            FROM
                product p,
                locprodcost_qtydist ocr
            LEFT JOIN
                locationproduct_cost oc ON
                oc.id = ocr.locprodcost_id
            LEFT JOIN locprod_productledger opl ON
                opl.id = oc.locprodprodledger_id
            WHERE
                p.systemid = opl.product_systemid AND
                ocr.csreceipt_id = $receipt_id AND
                p.id =  $product_id   AND
                ocr.qty_taken <> 0
            ;
        ";
        return DB::select(DB::raw($query));
    }

    public function cost_distribution_tbl($receipt_id, $product_id)
    {
        $cost_distributions = [];
        $mappings = $this->cost_distribution_query($receipt_id, $product_id);

        foreach ($mappings as $map) {
            $map->cost = number_format($map->cost / 100, 2);
            array_push($cost_distributions, $map);
        }

        $cost_distributions = array_reverse($cost_distributions);

        return response($cost_distributions, 201);
    }

    public function stockout_cost_dist($stockreport_id)
    {
        $cost_distributions = [];
        $reports = DB::select(DB::raw("
            SELECT
                opcq.id,
                opcq.qty_taken,
                oc.cost
            FROM
                locprodcost_qtydist opcq
            LEFT JOIN
                locationproduct_cost oc ON oc.id = opcq.locprodcost_id
            WHERE
                opcq.stockreport_id = $stockreport_id;
        "));
        foreach ($reports as $map) {
            $map->cost = number_format($map->cost / 100, 2);
            array_push($cost_distributions, $map);
        }

        $cost_distributions = array_reverse($cost_distributions);

        return response($cost_distributions, 201);
    }

    public function add_cost_to_prd_ledger($data, $prd_id)
    {

        $prd_info = DB::table('product')->whereId($prd_id)->first();

        $new_data = collect();

        foreach ($data as $prd) {

            if($prd->doc_type == 'Cash Sales'){
                $cost = DB::table('locprod_productledger')->
                where('product_systemid', $prd_info->systemid)->
                where('qty', $prd->quantity)->
                where('csreceipt_id', $prd->show_receipt_id)->
                orderBy('created_at', 'desc')->
                first();

            }else{
                $cost = DB::table('locprod_productledger')->
                where('product_systemid', $prd_info->systemid)->
                where('qty', $prd->quantity)->
                where('stockreport_id', $prd->id)->
                orderBy('created_at', 'desc')->
                first();

            }
            if (!empty($cost)) {
                $prd->cost = $cost->cost;
            } else {

                $prd->cost = 0;
            }

            $new_data->push($prd);
        }

        return $new_data;
    }

    public function stockUpdate(Request $request)
    {
        Log::debug('****stockUpdate()*****');
        try {
            $user_id = \Auth::user()->id;
            $table_data = $request->get('table_data');
            $stock_type = $request->get('stock_type');
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            foreach ($table_data as $key => $value) {
                Log::debug('***stockUpdate()*** $value=' . json_encode($value));
                //if qty zero
                if ($value['qty'] <= 0) {
                    continue;
                }

                //If SI or SO
                if ($stock_type == "IN") {
                    $curr_qty = $value['qty'];
                    $type = 'stockin';
                } else {
                    $curr_qty = $value['qty'] * -1;
                    $type = 'stockout';
                }

                //Location Product
                $locationproduct = DB::table('locationproduct')->where([
                    'product_id' => $value['product_id'],
                ])->first();

                if ($locationproduct) { // modify existing location product
                    $locationproduct = DB::table('locationproduct')->where([
                        'product_id' => $value['product_id'],
                    ])->increment('quantity', $curr_qty);
                } else {

                    DB::table('locationproduct')->insert([
                        "location_id" => $location->id,
                        "product_id" => $value['product_id'],
                        "quantity" => $curr_qty,
                        "damaged_quantity" => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                //Stock Report
                $stockreport_id = DB::table('stockreport')->insertGetId([
                    'systemid' => $stock_system,
                    'creator_user_id' => $user_id,
                    'type' => $type,
                    'location_id' => $location->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('stockreportproduct')->insert([
                    "stockreport_id" => $stockreport_id,
                    "product_id" => $value['product_id'],
                    "quantity" => $curr_qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $prd = DB::table('product')->
                    whereId($value['product_id'])->
                    first();

                if ($stock_type == "IN") {
                    $latest_cost = DB::table('locprod_productledger')->
                        where('product_systemid', $prd->systemid)->
                        whereIn('type', ['stockin', 'received'])->
                        whereNotNull('cost')->
                        orderBy('created_at', 'desc')->
                        first();

                    $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                    $locprodid = DB::table('locprod_productledger')->
                    insertGetId([
                        "stockreport_id" => $stockreport_id,
                        "product_systemid" => $prd->systemid,
                        "qty" => $curr_qty,
                        "cost" => $cost,
                        "last_update" => date('Y-m-d H:i:s'),
                        "status" => 'active',
                        "type" => $type,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    if ($locprodid) {


                        $last_record = DB::select(DB::raw("
                            SELECT
                                lp. *
                            FROM
                                locprod_productledger lp,
                                locationproduct_cost lc
                            WHERE
                                lp.type = 'cash_sales'  AND
                                lp.product_systemid  = '$prd->systemid' AND
                                lc.locprodprodledger_id = lp.id AND
                                lc.balance < 0
                            LIMIT 1
                            ;
                        "));

                        $last_record = collect($last_record)->first();

                        if ($last_record) {
                            $cost_info = DB::table('locationproduct_cost')
                            ->where('locprodprodledger_id', $last_record->id)
                                ->first();

                            if ($cost_info->qty_out * -1 > $curr_qty) {

                                DB::table('locationproduct_cost')->
                                where('locprodprodledger_id', $last_record->id)->
                                update([
                                    "qty_out" => $cost_info->qty_out + $curr_qty,
                                    "balance" => $cost_info->balance + $curr_qty,
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ]);

                                $cost_id = DB::table('locationproduct_cost')->
                                insertGetId([
                                    "locprodprodledger_id" => $locprodid,
                                    "cost" => $cost,
                                    "qty_in" => $curr_qty,
                                    "qty_out" => $curr_qty * -1,
                                    "balance" =>  $curr_qty - $curr_qty,
                                    "deleted_at" => null,
                                    "created_at" => date('Y-m-d H:i:s'),
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ]);
                                $this->record_locprodcost_csreceipt(
                                    $last_record->csreceipt_id,
                                    $cost_id,
                                    $curr_qty,
                                    null
                                );
                            } elseif ($curr_qty >= $cost_info->qty_out * -1) {

                                DB::table('locationproduct_cost')->
                                    where('locprodprodledger_id', $last_record->id)->
                                    update([
                                        "qty_out" => 0,
                                        "balance" => 0,
                                        "updated_at" => date('Y-m-d H:i:s'),
                                    ]);

                                $cost_id = DB::table('locationproduct_cost')->
                                insertGetId([
                                    "locprodprodledger_id" => $locprodid,
                                    "cost" => $cost,
                                    "qty_in" => $curr_qty,
                                    "qty_out" => $curr_qty - ($curr_qty - ($cost_info->qty_out)),
                                    "balance" => ($curr_qty - ($cost_info->qty_out * -1)),
                                    "deleted_at" => null,
                                    "created_at" => date('Y-m-d H:i:s'),
                                    "updated_at" => date('Y-m-d H:i:s'),
                                ]);

                                $this->record_locprodcost_csreceipt(
                                    $last_record->csreceipt_id,
                                    $cost_id,
                                    $cost_info->qty_out * -1,
                                    null
                                );
                            }
                        } else {
                            $lp_costid = DB::table('locationproduct_cost')->
                            insertGetId([
                                "locprodprodledger_id" => $locprodid,
                                "qty_in" => $curr_qty,
                                "qty_out" => 0,
                                "balance" => $curr_qty,
                                "cost" => $cost,
                                "deleted_at" => null,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                } else if ($stock_type == "OUT") {
                    $earliest_cost = DB::select(DB::raw("
                        SELECT
                            lc.cost
                        FROM
                            locationproduct_cost lc,
                            locprod_productledger lpl
                        WHERE
                            lpl.product_systemid = $prd->systemid AND
                            lc.locprodprodledger_id= lpl.id AND
                            lc.balance > 0
                        ORDER BY  lc.cost ASC
                        LIMIT 1
                        ;
                    "));
                    $cost = empty($earliest_cost) ? 0 : $earliest_cost[0]->cost;

                    $locprod_id = DB::table('locprod_productledger')->insertGetId([
                        "stockreport_id" => $stockreport_id,
                        "product_systemid" => $prd->systemid,
                        "qty" => $curr_qty,
                        "cost" => $cost,
                        "last_update" => date('Y-m-d H:i:s'),
                        "status" => 'active',
                        "type" => $type,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $this->process_locprod_stockout(
                        $prd->systemid,
                        $curr_qty,
                        $stockreport_id,
                        $locprod_id
                    );
                }

                PrdOpenitem::where('product_id', $value['product_id'])->get()->map(function ($f) {
                    $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($f->product_id);
                    $f->update();
                });
            }
            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }

    public function process_locprod_stockout($systemid, $curr_qty, $stockreport_id, $ledger_id)
    {
        try {

            // Get oldest non zero balance -- 1st Level
            $oldest_bal = DB::table('locationproduct_cost')->join(
                'locprod_productledger',
                'locprod_productledger.id',
                'locationproduct_cost.locprodprodledger_id'
            )->select(
                    'locprod_productledger.id as ledger_id',
                    'locprod_productledger.stockreport_id as sr_id',
                    'locprod_productledger.type as doc_type',
                    'locationproduct_cost.cost as cost',
                    'locationproduct_cost.id as id',
                    'locationproduct_cost.qty_in as qty_in',
                    'locationproduct_cost.qty_out as qty_out',
                    'locationproduct_cost.balance as balance',
                    'locationproduct_cost.created_at as created_at',
                    'locationproduct_cost.updated_at as updated_at'
                )->where("locprod_productledger.product_systemid", $systemid)->where('locationproduct_cost.balance', '>', 0)->orderBy('locationproduct_cost.created_at', 'asc')->first();

            if (!empty($oldest_bal)) {
                $compare = $curr_qty;

                if ($oldest_bal->balance >= ($compare * -1)) {

                    DB::table('locationproduct_cost')->whereId($oldest_bal->id)->update([
                            "qty_out" => $curr_qty + $oldest_bal->qty_out,
                            "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('locprod_productledger')->whereId($ledger_id)->update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    #Note: curr_qty + qty_out => surplus
                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    DB::table('locprodcost_qtydist')->insert([
                        "csreceipt_id" => null,
                        "stockreport_id" => $stockreport_id,
                        "locprodcost_id" => $oldest_bal->id,
                        "qty_taken" => $qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('locationproduct_cost')->whereId($oldest_bal->id)->update([
                            "qty_out" => $oldest_bal->qty_in * -1,
                            "balance" => 0,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('locprod_productledger')->whereId($ledger_id)->update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $oldest_bal->balance;

                    DB::table('locprodcost_qtydist')->insert([
                        "csreceipt_id" => null,
                        "stockreport_id" => $stockreport_id,
                        "locprodcost_id" => $oldest_bal->id,
                        "qty_taken" => $qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $this->process_locprod_stockout(
                        $systemid,
                        $carry_over_bal,
                        $stockreport_id,
                        $ledger_id
                    );
                }
            }

            // $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }


    public function process_locprod_returning($systemid, $curr_qty, $stockreport_id, $ledger_id)
    {
        try {

            // Get oldest non zero balance -- 1st Level
            Log::debug("***** process_locprod_returning: START ***** ");
            $oldest_bal = DB::table('locationproduct_cost')->
            join(
                'locprod_productledger',
                'locprod_productledger.id',
                'locationproduct_cost.locprodprodledger_id'
            )->select(
                'locprod_productledger.id as ledger_id',
                'locprod_productledger.stockreport_id as sr_id',
                'locprod_productledger.type as doc_type',
                'locationproduct_cost.cost as cost',
                'locationproduct_cost.id as id',
                'locationproduct_cost.qty_in as qty_in',
                'locationproduct_cost.qty_out as qty_out',
                'locationproduct_cost.balance as balance',
                'locationproduct_cost.created_at as created_at',
                'locationproduct_cost.updated_at as updated_at'
            )->where("locprod_productledger.product_systemid", $systemid)->
            where('locationproduct_cost.balance', '>', 0)->
            orderBy('locationproduct_cost.created_at', 'asc')->first();

            $cost = $oldest_bal->cost;

            Log::debug("process_locprod_returning: cost=".json_encode($cost));

            if (!empty($oldest_bal)) {
                $compare = $curr_qty;
                Log::info("process_locprod_returning: oldest_bal exists");

                if ($oldest_bal->balance >= ($compare * -1)) {

                    DB::table('locationproduct_cost')->
                        whereId($oldest_bal->id)->
                        update([
                        "qty_out" => $curr_qty + $oldest_bal->qty_out,
                        "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    /* DB::table('locprod_productledger')->
                    whereId($ledger_id)->
                    update([
                        "cost" => $oldest_bal->cost,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]); */

                    #Note: curr_qty + qty_out => surplus
                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    DB::table('locprodcost_qtydist')->insert([
                        "csreceipt_id" => null,
                        "stockreport_id" => $stockreport_id,
                        "locprodcost_id" => $oldest_bal->id,
                        "qty_taken" => $qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    Log::debug("process_locprod_returning: oldest bal is less than qty");
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;
                    Log::debug("process_locprod_returning: curr_qty=".$curr_qty);
                    Log::debug("process_locprod_returning: oldest_bal->balance=" . $oldest_bal->balance);
                    Log::debug("process_locprod_returning: carry_over_bal=" . $carry_over_bal);

                    DB::table('locationproduct_cost')->
                        whereId($oldest_bal->id)->
                        update([
                            "qty_out" => $oldest_bal->qty_in * -1,
                            "balance" => 0,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                   /*  DB::table('locprod_productledger')->
                    whereId($ledger_id)->update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]); */

                    $qty = $oldest_bal->balance;

                    DB::table('locprodcost_qtydist')->insert([
                        "csreceipt_id" => null,
                        "stockreport_id" => $stockreport_id,
                        "locprodcost_id" => $oldest_bal->id,
                        "qty_taken" => $qty,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    // $this->process_locprod_returning(
                    //     $systemid,
                    //     $carry_over_bal,
                    //     $stockreport_id,
                    //     $ledger_id
                    // );
                }
            }

            // $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }


    public function process_locprod_stockout_negative_sales(
        $systemid,
        $curr_qty,
        $csreceipt_id,
        $ledger_id
    ) {
        try {

            // Get oldest non zero balance -- 1st Level
            $oldest_bal = DB::table('locationproduct_cost')->join(
                'locprod_productledger',
                'locprod_productledger.id',
                'locationproduct_cost.locprodprodledger_id'
            )->select(
                    'locprod_productledger.id as ledger_id',
                    'locprod_productledger.stockreport_id as sr_id',
                    'locprod_productledger.type as doc_type',
                    'locprod_productledger.cost as lcost',
                    'locationproduct_cost.cost as cost',
                    'locationproduct_cost.id as id',
                    'locationproduct_cost.qty_in as qty_in',
                    'locationproduct_cost.qty_out as qty_out',
                    'locationproduct_cost.balance as balance',
                    'locationproduct_cost.created_at as created_at',
                    'locationproduct_cost.updated_at as updated_at'
                )->where("locprod_productledger.product_systemid", $systemid)->whereRaw('locationproduct_cost.qty_in < locationproduct_cost.qty_out * -1')->orderBy('locationproduct_cost.created_at', 'desc')->first();

            $sec_oldest_bal = DB::table('locationproduct_cost')->join(
                'locprod_productledger',
                'locprod_productledger.id',
                'locationproduct_cost.locprodprodledger_id'
            )->select(
                'locprod_productledger.id as ledger_id',
                'locprod_productledger.stockreport_id as sr_id',
                'locprod_productledger.type as doc_type',
                'locprod_productledger.cost as lcost',
                'locationproduct_cost.cost as cost',
                'locationproduct_cost.id as id',
                'locationproduct_cost.qty_in as qty_in',
                'locationproduct_cost.qty_out as qty_out',
                'locationproduct_cost.balance as balance',
                'locationproduct_cost.created_at as created_at',
                'locationproduct_cost.updated_at as updated_at'
            )->where("locprod_productledger.product_systemid", $systemid)->whereRaw('locationproduct_cost.balance > 0')->orderBy('locationproduct_cost.created_at', 'asc')->first();
            Log::debug("oldest_bal" . json_encode($oldest_bal));
            if (!empty($oldest_bal)) {

                $compare = $curr_qty;
                $data_cost = DB::table('locationproduct_cost')->where('locprodprodledger_id', $oldest_bal->ledger_id)->orderBy('created_at', 'desc')->first();

                if ($data_cost->qty_in < $data_cost->qty_out * -1) {

                    DB::table('locationproduct_cost')->
                    whereId($data_cost->id)->
                    update([
                            "cost" => $oldest_bal->cost,
                            "qty_out" => $curr_qty + $oldest_bal->qty_out,
                            "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('locprod_productledger')->
                        whereId($ledger_id)->
                        update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }
                    $this->record_locprodcost_csreceipt(
                        $csreceipt_id,
                        $oldest_bal->id,
                        $qty,
                    );
                }
            } elseif (!empty($sec_oldest_bal)) {
                Log::debug("Sec_oldest_bal:=" . json_encode($sec_oldest_bal));
                $compare = $curr_qty;
                if ($sec_oldest_bal->balance >= ($compare * -1)) {

                    DB::table('locationproduct_cost')->whereId($sec_oldest_bal->id)->update([
                            "cost" => $sec_oldest_bal->cost,
                            "qty_out" => $curr_qty + $sec_oldest_bal->qty_out,
                            "balance" => $sec_oldest_bal->qty_in +
                                ($curr_qty + $sec_oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('locprod_productledger')->whereId($ledger_id)->update([
                            "cost" => $sec_oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $curr_qty + $sec_oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    $this->record_locprodcost_csreceipt(
                        $csreceipt_id,
                        $sec_oldest_bal->id,
                        $qty,
                    );
                } else {
                    Log::debug("3rd condition--->:sec_oldest_bal=:" . $sec_oldest_bal->balance);
                    $carry_over_bal = $curr_qty + $sec_oldest_bal->balance;

                    DB::table('locationproduct_cost')->whereId($sec_oldest_bal->id)->update([
                            "cost" => $sec_oldest_bal->cost,
                            "qty_out" => $sec_oldest_bal->qty_in * -1,
                            "balance" => 0,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('locprod_productledger')->whereId($ledger_id)->update([
                            "cost" => $sec_oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $sec_oldest_bal->balance;
                    $this->record_locprodcost_csreceipt(
                        $csreceipt_id,
                        $sec_oldest_bal->id,
                        $qty,
                        $sec_oldest_bal->sr_id
                    );
                    $this->process_locprod_stockout_negative_sales(
                        $systemid,
                        $carry_over_bal,
                        $csreceipt_id,
                        $ledger_id
                    );
                }
            } else {

                $latest_cost = DB::select(DB::raw("
                    SELECT
                        lc.cost
                    FROM
                        locationproduct_cost lc,
                        locprod_productledger lpl
                    WHERE
                        lpl.product_systemid = $systemid AND
                        lpl.type = 'stockin'  OR
                        lpl.type = 'received'  AND
                        lc.locprodprodledger_id = lpl.id AND
                        lc.balance > 0
                    ORDER BY  lc.cost DESC
                    LIMIT 1
                    ;
                "));
                Log::debug("latest_cost:=" . json_encode($latest_cost));
                $cost_id = DB::table('locationproduct_cost')->insertGetId([
                    "locprodprodledger_id" => $ledger_id,
                    "cost" => $latest_cost[0]->cost ?? 0,
                    "qty_in" => 0,
                    "qty_out" => $curr_qty,
                    "balance" => $curr_qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);  // end of insert openitem_cost


                DB::table('locprod_productledger')->whereId($ledger_id)->update([
                        "cost" => $latest_cost[0]->cost ?? 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                $this->record_locprodcost_csreceipt(
                    $csreceipt_id,
                    $cost_id,
                    $curr_qty * -1,
                );
            }
            // $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }




    public function autoStockIn($product_id, $qty)
    {

        try {

            Log::debug("Auto Stock In - Product ID: " . $product_id . " - Qty: " . $qty);

            $user_id = \Auth::user()->id;
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            $type = 'stockin';
            //Location Product
            $locationproduct = DB::table('locationproduct')->where([
                'product_id' => $product_id,
            ])->first();

            if ($locationproduct) { // modify existing location product

                $locationproduct = DB::table('locationproduct')->where([
                    'product_id' => $product_id,
                ])->increment('quantity', $qty);
            } else {
                DB::table('locationproduct')->insert([
                    "location_id" => $location->id,
                    "product_id" => $product_id,
                    "quantity" => $qty,
                    "damaged_quantity" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            //Stock Report
            $stockreport_id = DB::table('stockreport')->insertGetId([
                'systemid' => $stock_system,
                'creator_user_id' => $user_id,
                'type' => $type,
                'location_id' => $location->id,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('stockreportproduct')->insert([
                "stockreport_id" => $stockreport_id,
                "product_id" => $product_id,
                "quantity" => $qty,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            PrdOpenitem::where('product_id', $product_id)->get()->map(function ($f) {
                $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($f->product_id);
                $f->update();
            });

            // $this->create_receiptcost();

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
        }
    }

    public function showStockReport()
    {
        $stockreport = DB::table('stockreport')->
            join('stockreportproduct', 'stockreportproduct.stockreport_id', '=',
                 'stockreport.id')->
            join('product', 'product.id', '=', 'stockreportproduct.product_id')->
            where('stockreport.systemid', request()->report_id)->
            get();

        $stockreport_data = DB::table('stockreport')->
        select(
            'users.fullname as staff_name',
            'users.systemid as staff_id',
            'stockreport.systemid as document_no',
            'stockreport.id as stockreport_id',
            'stockreport.type as srtype',
            'stockreport.created_at as last_update',
            'location.name as location',
            'location.id as locationid'
        )->
        leftjoin('location', 'location.id', '=', 'stockreport.location_id')->
        join('users', 'users.id', '=', 'stockreport.creator_user_id')->
        where('stockreport.systemid', request()->report_id)->
        orderBy('stockreport.updated_at', "desc")->
        first();

        $isWarehouse = false;
        return view(
            'inv_stockmgmt.inventorystockreport',
            compact('stockreport', 'stockreport_data', 'isWarehouse')
        );
    }

    public function reflect_autostock_locationproduct_cost()
    {

        $locProd = DB::select(
            DB::raw("
            SELECT
                p.id as product_id,
                p.name,
                p.systemid,
                srp.quantity,
                srp.stockreport_id
            FROM
                product p,
                prd_inventory piv,
                stockreport sr,
                stockreportproduct srp
            WHERE
                p.id = piv.product_id AND
                srp.product_id = p.id AND
                srp.stockreport_id = sr.id
            ;")
        );

        if (!empty($locProd)) {

            foreach ($locProd as $key => $value) {
                # code...
                $stocrep = DB::table('locprod_productledger')->where('stockreport_id', $value->stockreport_id)->where('product_systemid', $value->systemid)->first();
                $product = DB::table('locprod_productledger')->where('product_systemid', $value->systemid)->latest()->first();

                if (!$stocrep) {

                    if ($product) {

                        $cost = DB::select(
                            DB::raw(
                                "
                            SELECT
                                lpl.product_systemid,
                                max(lpc.cost) as locost
                            FROM
                                locprod_productledger lpl,
                                locationproduct_cost lpc
                            WHERE
                                lpc.locprodprodledger_id ='" . $product->id . "'
                            GROUP BY
                                lpl.product_systemid
                                ;"
                            )
                        );

                        $cost = $cost[0]->locost ?? 0;
                        Log::debug("LOCost: " . $cost);
                        if ($value->quantity >= 0) {
                            $lpl_id = DB::table('locprod_productledger')->insertGetID([
                                "stockreport_id" => $value->stockreport_id,
                                "product_systemid" => $value->systemid,
                                "qty" => $value->quantity,
                                "cost" => $cost,
                                "status" => "active",
                                "type" => "stockin",
                                "last_update" => date('Y-m-d H:i:s'),
                                "created_at" => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                            DB::table('locationproduct_cost')->insert([
                                "locprodprodledger_id" => $lpl_id,
                                "cost" => $cost,
                                "qty_in" => $value->quantity,
                                "qty_out" => $value->quantity * -1,
                                "balance" => ($value->quantity * -1) + $value->quantity,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    } else {

                        if ($value->quantity >= 0) {
                            $lpl_id = DB::table('locprod_productledger')->insertGetID([
                                "stockreport_id" => $value->stockreport_id,
                                "product_systemid" => $value->systemid,
                                "qty" => $value->quantity,
                                "cost" => 0,
                                "status" => "active",
                                "type" => "stockin",
                                "last_update" => date('Y-m-d H:i:s'),
                                "created_at" => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                            DB::table('locationproduct_cost')->insert([
                                "locprodprodledger_id" => $lpl_id,
                                "qty_in" => $value->quantity,
                                "qty_out" => $value->quantity * -1,
                                "balance" => ($value->quantity * -1) + $value->quantity,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
        }
        $this->update_locationprod_cost_when_auto_stockin_off();
    }

    public function create_receiptcost()
    {

        $receipt_id = DB::select(
            DB::raw("
            SELECT
                lpc.id as locprodcost_id,
                cr.id as csreceipt_id
            FROM
                locationproduct_cost lpc,
                locprod_productledger lpl,
                stockreport sr,
                stockreportproduct srp,
                cstore_receiptproduct crp,
                cstore_receipt cr
            WHERE
                lpc.locprodprodledger_id = lpl.id AND
                lpl.stockreport_id = srp.stockreport_id AND
                lpl.stockreport_id = sr.id AND
                srp.product_id = crp.product_id AND
                crp.receipt_id = cr.id
            ;")
        );

        if (!empty($receipt_id)) {

            foreach ($receipt_id as $key => $value) {
                # code...
                $stocrep = DB::table('locprodcost_qtydist')->where('csreceipt_id', $value->csreceipt_id)->where('locprodcost_id', $value->locprodcost_id)->first();

                if (!$stocrep) {

                    DB::table('locprodcost_qtydist')->insert([
                        "csreceipt_id" => $value->csreceipt_id,
                        "locprodcost_id" => $value->locprodcost_id,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }

    public function update_locationprod_cost_when_auto_stockin_off()
    {
        $cost_ledger = DB::select("
            SELECT
                lp.id,
                lp.qty,
                lp.product_systemid,
                lp.stockreport_id,
                lp.type,
                lp.cost
            FROM
                locprod_productledger lp
                LEFT JOIN locationproduct_cost lc ON lc.locprodprodledger_id = lp.id
            WHERE
                lc.locprodprodledger_id is null
            ;

            ");
        $cost_ledger = collect($cost_ledger);

        foreach ($cost_ledger as $key => $value) {
            # code...
            if ($value->qty >= 0) {
                DB::table('locationproduct_cost')->insert([
                    "locprodprodledger_id" => $value->id,
                    "qty_in" => $value->qty,
                    "qty_out" => 0,
                    "balance" => $value->qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }

            if ($value->qty < 0) {
                DB::table('locationproduct_cost')->insert([
                    "locprodprodledger_id" => $value->id,
                    "qty_in" => 0,
                    "qty_out" => $value->qty,
                    "balance" => $value->qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }


    public function record_locprodcost_csreceipt(
        $csreceipt_id_raw = null,
        $locprod_id,
        $qty_taken,
        $stockreport_id_raw = null
    ) {
        if (empty($csreceipt_id_raw)) {
            $csreceipt_id = null;
            $stockreport_id = $stockreport_id_raw;
        } else {
            $csreceipt_id = $csreceipt_id_raw;
            $stockreport_id = null;
        }

        DB::table('locprodcost_qtydist')->insert([
            'qty_taken' => $qty_taken,
            'locprodcost_id' => $locprod_id,
            'csreceipt_id' => $csreceipt_id,
            'stockreport_id' => $stockreport_id,
        ]);
    }

    // added optimized function

    public function showProductledger(Request $request)
    {
        $product = DB::table('product')->where("id", $request->product_id)->first();

        $location = Location::first();
        return view(
            "inv_stockmgmt.productledger",
            compact("location", "product")
        );
    }


    public function showproductledger_datatable(Request $request)
    {
        $product = DB::table('product')->
            where("systemid", $request->systemid)->
            first();

        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $location = Location::first();
        $totalRecords_query = "
            SELECT
                SUM(count) as total
            FROM
                (
                    SELECT
                            COUNT(*) as count
                    FROM
                        cstore_receiptproduct crp,
                        cstore_receipt cr,
                        cstore_receiptdetails,
                        product p
                    LEFT JOIN
                        locprod_productledger lpl ON
                        lpl.product_systemid = p.systemid
                    WHERE
                        p.id = crp.product_id  AND
                        cr.id = cstore_receiptdetails.receipt_id AND
                        cr.id = crp.receipt_id AND
                        p.id = $product->id
                    GROUP BY
                        cr.id
                    UNION
                    SELECT
                        COUNT(*) as count
                    FROM
                        stockreportproduct srp,
                        stockreport sr,
                        product p
                    LEFT JOIN
                        locprod_productledger lpl ON
                        lpl.product_systemid = p.systemid
                    WHERE
                        sr.id = srp.stockreport_id AND
                        srp.product_id = p.id AND
                        p.id = $product->id
                    GROUP BY
                        sr.id
                ) as t
            ;
        ";
        $totalRecords = DB::select(DB::raw($totalRecords_query))[0]->total;

        $cstore_stockreport_query = "

                SELECT
                    cr.id as id,
                    cr.status as status,
                    cr.systemid as systemid,
                    (crp.quantity * -1) as quantity,
                    null as stockreport_id,
                    cr.id as show_receipt_id,
                    'Cash Sales' as doc_type,
                    cstore_receiptdetails.id as receiptdetails_id,
                    DATE_FORMAT(cr.updated_at, '%d%b%y %H:%i:%s') as last_update,
                    cr.voided_at as voided_at
                FROM
                    cstore_receiptproduct crp,
                    cstore_receipt cr,
                    cstore_receiptdetails,
                    product p
                WHERE
                    p.id = crp.product_id  AND
                    cr.id = cstore_receiptdetails.receipt_id AND
                    cr.id = crp.receipt_id AND
                    p.id = $product->id

                UNION
                SELECT
                    sr.id as id,
                    sr.status as status,
                    sr.systemid as systemid,
                    srp.quantity as quantity,
                    sr.id as stockreport_id,
                    null as show_receipt_id,
                    sr.type as doc_type,
                    null as receiptdetails_id,
                    DATE_FORMAT(sr.updated_at, '%d%b%y %H:%i:%s') as last_update,
                    null as voided_at
                FROM
                    stockreportproduct srp,
                    stockreport sr,
                    product p
                WHERE
                    sr.id = srp.stockreport_id AND
                    srp.product_id = p.id AND
                    p.id = $product->id

                ORDER BY last_update DESC
                LIMIT $rowperpage
                OFFSET $start

        ";

        $data = collect(DB::select(DB::raw($cstore_stockreport_query)));
        $data = $data->values()->sortBy('last_update');
        // $data = $data)->skip($start)->take($rowperpage)->values();

        $data = $this->add_cost_to_prd_ledger($data, $product->id);

        Log::debug(['**showProductLedger**: $data=' => $data]);

        $result = [];

        foreach ($data as $dt) {
            if ($dt->doc_type == "Cash Sales" && !empty($dt->show_receipt_id)) {
                $receipt_id = $dt->show_receipt_id;
                $mappings = $this->cost_distribution_query($receipt_id, $product->id);

                $cost_distributions = [];
                foreach ($mappings as $map) {
                    array_push($cost_distributions, $map);
                }

                $dt->cost_distribution = $cost_distributions;
                if (sizeof($dt->cost_distribution) > 0) {
                    $costd = $dt->cost_distribution[sizeof($dt->cost_distribution) - 1];
                    $dt->cost = number_format(($costd->cost / 100), 2);
                }
                $dt->product_id = $product->id;
            }

            if ($dt->doc_type == "stockout" || $dt->doc_type == "returned" && !empty($dt->stockreport_id)) {
                $cost_distributions = [];
                $reports = DB::select(DB::raw("
                    SELECT
                        opcq.id,
                        opcq.qty_taken,
                        oc.cost
                    FROM
                        locprodcost_qtydist opcq
                    LEFT JOIN
                        locationproduct_cost oc ON oc.id = opcq.locprodcost_id
                    WHERE
                        opcq.stockreport_id = $dt->stockreport_id;
                "));
                foreach ($reports as $map) {
                    $map->cost = number_format($map->cost / 100, 2);
                    array_push($cost_distributions, $map);
                }

                $dt->cost_distribution = $cost_distributions;
                if (sizeof($dt->cost_distribution) > 0) {
                    $costd = $dt->cost_distribution[sizeof($dt->cost_distribution) - 1];
                    $dt->cost = $costd->cost;
                }
                $dt->product_id = $product->id;
            }
            array_push($result, $dt);
        }
        /* Here is where you store $data in new productledger schema:
        locprod_productledger */
        return Datatables::of($data)->
            setOffset($start)->
            addIndexColumn()->
            addColumn('product_systemid', function ($row) {
                $display = '';
                if ($row->doc_type == "Cash Sales" && !empty($row->show_receipt_id)) {

                    if ($row->status == 'voided') {
                        $display .=  ' <td style="text-align: center; background-color:red;color:white;font-weight:bold;">';
                    } else {
                        $display .= '<td style="text-align: center; background-color:red;color:white;font-weight:bold;">';
                    }
                    $display .= '<a href="#" style="text-decoration: none;" onclick="showReceipt(\'' . $row->show_receipt_id . '\')">' . $row->systemid . ' </a>';
                } elseif ($row->doc_type == "received" ) {

                    $url = route('receiving_list_id', $row->systemid);
                    $display .= '<a href="javascript:window.open(\'' . $url . '\')"
                        style="text-decoration: none;">' . $row->systemid . '</a>';
                } elseif ($row->doc_type == "returned") {

                    $display .= '
                        <a href="/returning_note_report/' . $row->systemid . '"
                        style="text-decoration: none;">' . $row->systemid . '</a></td>';
                } else {

                    $url = route('stocking.stock_report', $row->systemid);
                    $display .= '<td style="text-align: center;">
                        <a href="javascript:window.open(\'' . $url . '\')"
                        style="text-decoration: none;">' . $row->systemid . '</a></td>';
                }

                return  $display;
            })->
            addColumn('type', function ($row) {
                if ($row->doc_type == 'stockin') {
                    $type =   "Stock In";
                } elseif ($row->doc_type == 'stockout') {
                    $type =  "Stock Out";
                } else {
                    $type = ucwords($row->doc_type);
                }
                return $type;
            })->
            addColumn('last_update', function ($row) {
                if ($row->status == 'voided') {
                    $date = date('dMy H:i:s', strtotime($row->voided_at ?? ''));
                } else {
                    $date = $row->last_update;
                }
                return $date;
            })->
            addColumn('location', function () use ($location) {
                return $location->name;
            })->
            addColumn('cost', function ($row) {

            $cost_num = empty($row->cost) ? 0 :
                 number_format((filter_var($row->cost, FILTER_SANITIZE_NUMBER_INT) / 100), 2);
                if (property_exists($row, 'cost_distribution')) {

                    $cost = '';
                    if (sizeof($row->cost_distribution) > 0) {
                        if ($row->doc_type == "Cash Sales") {
                            $cost = ' <a href="#" onclick="show_cost_breakdown(' . $row->show_receipt_id . ', ' . $row->product_id . ')">' . $cost_num .
                              '</a>';
                        } elseif ($row->doc_type == 'stockout' || $row->doc_type == 'returned' && !empty($row->stockreport_id)) {

                            $cost =  '<a href="#" onclick="showStockOutCostDist(\'' . $row->stockreport_id .
                                '\')">' . $cost_num . '</a>';
                        }
                    } else {
                        $cost = $cost_num;
                    }
                } else {
                    $cost = $cost_num;
                }

                return $cost;
            })->
            addColumn('quantity', function ($row) {
                if ($row->status == 'voided') {
                    return 0;
                } else {
                    return $row->quantity;
                }
            })->
            setTotalRecords($totalRecords)->
            escapeColumns([])->
            make(true);
    }
}
