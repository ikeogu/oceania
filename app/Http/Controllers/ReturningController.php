<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;

class ReturningController extends Controller
{
    //
    public function cstore_returning_list()
    {
        return view('returning.cstore_returning_list');
    }

    public function cstore_returning()
    {

        return view('returning.cstore_returning');
    }

    public function display_returing_note(Request $request)
    {

        try {

            $start = $request->get('start');
            $rowPerPage = $request->get('length');

            $open_item_prods = DB::table('product')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                join('openitem_productledger', 'openitem_productledger.product_systemid',
                    'product.systemid')->
                join('openitem_cost', 'openitem_cost.openitemprodledger_id',
                     'openitem_productledger.id')->
                leftjoin('productbarcode', function ($join) {
                    $join->on('productbarcode.product_id', '=', 'product.id')
                        ->where('productbarcode.selected', 1)
                        ->limit(1);
                })->
                whereNotNull('product.name')->
                where('openitem_cost.balance', '>', '0')->
                select("product.*", "openitem_cost.cost",
                    DB::Raw('IFNULL(productbarcode.barcode,product.systemid) as barcode'))->
                get();

            $loc__prods = DB::table('product')->
                join('prd_inventory', 'prd_inventory.product_id', 'product.id')->
                join(
                    'locprod_productledger',
                    'locprod_productledger.product_systemid',
                    'product.systemid'
                )->join(
                    'locationproduct_cost',
                    'locationproduct_cost.locprodprodledger_id',
                    'locprod_productledger.id'
                )->
                 leftJoin('productbarcode', function ($join) {
                    $join->on('productbarcode.product_id', '=', 'product.id')
                        ->where('productbarcode.selected', 1)
                        ->limit(1);
                })->

                whereNotNull([
                    'product.name',
                ])->where('locationproduct_cost.balance', '>', '0')->
                select("product.*", "locationproduct_cost.cost",
                  DB::raw('IFNULL(productbarcode.barcode, null) as barcode')
                )->
                get();

            $product_data_open_item = $open_item_prods->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->
                qtyAvailable($product->id) > 0;
            });

            $product_data_locprod = $loc__prods->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($product->id) > 0;
            });

            $totalRecords = $product_data_locprod->unique('id')->values()->count() + $product_data_open_item->unique('id')->values()->count();

            $data = $product_data_locprod->merge($product_data_open_item)->unique('id')->values()->skip($start)->take($rowPerPage);


            if (!empty($request->container)) {
                foreach ($request->container as $i) {

                    if ($data->contains('id', $i['product_id']) && $data->where('id', $i['product_id'])
                        ->where('qty', '!=', $i['product_id'])->first()
                    ) {
                        $data->where('id', $i['product_id'])->first()->rqty = $i['qty'];
                    }
                }
            }
            if ($request->has('search') && !empty($request->search)) {

                $search = trim($request->search);

                // Log::debug('request->search:' . $request->search);

                 $data = $product_data_locprod->merge($product_data_open_item)->
                    unique('id')->
                    values()->
                    filter(function ($value) use ($search) {

                    // Log::debug('value:' . $value->name);

                    if (
                        preg_match("/$search/i", $value->name)     ||
                        preg_match("/$search/i", $value->barcode)  ||
                        preg_match("/$search/i", $value->systemid)
                    ) {

                        return $value;
                    }
                });

                $totalRecords = count($data);
            }

            return $this->table($data, $start,$totalRecords);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function table($data, $start, $totalRecords)
    {

        return Datatables::of($data)->
            setOffset($start)->
            addIndexColumn()->
            addColumn('barcode', function ($data) {
                return $data->barcode == null ? $data->systemid : $data->barcode;
            })->
            addColumn('name', function ($data) {
                $img_src = '/images/product/' .
                    $data->systemid . '/thumb/' .
                    $data->thumbnail_1;

                if (!empty($data->thumbnail_1) && file_exists(public_path() . $img_src)) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
						style=' width: 25px; height: 25px;
						display: inline-block;margin-right: 8px;
						object-fit:contain;'>";
                } else {
                    $img = '';
                }
                return $img . $data->name;
            })->
            addColumn('cost', function ($data) {
                $product_id = $data->id;
                $cost = number_format($data->cost / 100, 2);
                return <<<EOD
						<span id="cost_$product_id">$cost</span>
EOD;
            })->
            addColumn('qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($product_id);
                return <<<EOD
						<span id="qty_$product_id">$qty</span>
EOD;
            })->
            addColumn('returning_qty', function ($data) {

                $val = $data->id;
                $qty = empty($data->rqty) ? 0 : $data->rqty;
                $incr = '<div class="align-self-center value-button increase" id="increase_' .
                    $val . '" onclick="increaseValue(\'' . $val . '\')" value="Increase Value">
                            <ion-icon class="ion-ios-plus-outline" style="cursor: pointer;font-size: 24px;margin-right:5px;">
                            </ion-icon>
                        </div>';
                //
                $input = '<input type="number" id="number_' . $val .
                    '" oninput="changeValueOnBlur(\'' . $val . '\')" class="number product_qty js-product-qty"
                    value="' . $qty . '" min="0"/>';

                $decr = '<div class="value-button decrease" id="decrease_' . $val .
                    '" onclick="decreaseValue(\'' . $val . '\')" value="Decrease Value">
                            <ion-icon class="ion-ios-minus-outline" style="cursor: pointer;font-size: 24px;margin-left:5px;">
                            </ion-icon>
                        </div>';

                $full_div = '<div class="d-flex align-items-center justify-content-center">' .
                    $incr . $input . $decr . '</div>';
                return $full_div;
            })->rawColumns(['difference'])->
            escapeColumns([])->
            setTotalRecords($totalRecords)->
            make(true);
    }

    public function save_returning_qty(Request $request)
    {

        $container = json_decode(json_encode($request->table_data, true));
        $container = collect($container);

        Log::debug("save_returning_qty: container", [$container]);
        $s = new SystemID('returningnote');

        foreach ($container as $value) {

            if(strpos($value->cost,',' ) !== false){
                $cost = intval(preg_replace('/[^\d.]/', '', $value->cost)) * 100;
            }else{
                $cost = $value->cost * 100;
            }

            Log::debug("save_returning_qty: cost", [$cost]);
            if ($value->qty > 0) {
                $data = array(
                    'product_id' => $value->product_id,
                    'qty' => $value->qty,
                    'cost' => $cost,
                    'systemid' => $s->__toString(),
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time()),
                );
                Log::debug("save_returning_qty: container", [$data]);

                DB::table('returningnote')->insert($data);
            }
        }

        $data2 = array(
            'returningnote_systemid' => $s->__toString(),
            'created_at' => date('Y-m-d H:i:s', time()),
        );

        DB::table('returningnote_list')->insert($data2);
        $this->do_stockout_returning_product($request->table_data, $s->__toString());
    }

    public function display_returning_list(Request $request)
    {
        try {
            $start = $request->get('start');
            $rowPerPage = $request->get('length');
            $totalRecords = DB::table('returningnote_list')->count();

            $data = DB::table('returningnote_list')->
            latest()->skip($start)->take($rowPerPage)->get();

            if ($request->has('search') && !empty($request->search)) {

                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);
                $data = DB::table('returningnote_list')->latest()->
                get()->filter(function ($value) use ($search) {

                        Log::debug('value:' . $value->returningnote_systemid);

                        if (
                            preg_match("/$search/i", $value->returningnote_systemid)
                        ) {

                            return $value;
                        }
                    });

                $totalRecords = count($data);
            }

            return Datatables::of($data)->setOffset($start)->
                addIndexColumn()->
                addColumn('returningnote_systemid', function ($data) {
                    return $data->returningnote_systemid;
                })->
                addColumn('created_at', function ($data) {
                    return date('dMy H:i:s', strtotime($data->created_at));
                })->
                setTotalRecords($totalRecords)->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function returning_note_report($id)
    {
        $main = DB::table('returningnote_list')->
            where('returningnote_systemid', $id)->
            first();

        $list = DB::table('returningnote')->
            where('returningnote.systemid', '=', $id)->
            leftjoin('product', 'product.id', '=', 'returningnote.product_id')->
            select(
                'returningnote.*',
                'product.name as name',
                'product.thumbnail_1 as thumbnail_1',
                'product.systemid as psystemid'
            )->
            get();

        $result = [];
        foreach ($list as $lt) {

            $barcode = DB::table('productbarcode')->
                where('product_id', $lt->id)->
                where('selected', 1)->
                first();

            if (is_null($barcode)) {
                $lt->barcode = $lt->psystemid;
            } else {
                $lt->barcode = $barcode->barcode;
            }

            array_push($result, $lt);
        }

        $list = $result;

        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s', strtotime($main->created_at));
        $docId = $id;

        $invoice_data = DB::table('returningnote')->
            where('systemid', $id)->
            first();
        $invoice_no = 0;

        return view('returning.cstore_returning_note_confirmed',
            compact('list', 'location', 'user', 'invoice_no', 'docId', 'time'));
    }

    public function do_stockout_returning_product($data, $ret_systemid)
    {

        $user_id = \Auth::user()->id;
        $stock_system = $ret_systemid;

        $company = Company::first();
        $location = Location::first();
        $type = 'returned';

        foreach ($data as $key => $value) {
            Log::debug('***do_returning stockout()*** $value=' . json_encode($value));

            //Stock Report
            $stockreport_id = DB::table('stockreport')->
            insertGetId([
                'systemid' => $stock_system,
                'creator_user_id' => $user_id,
                'type' => $type,
                'location_id' => $location->id,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('stockreportproduct')->
            insert([
                "stockreport_id" => $stockreport_id,
                "product_id" => $value['product_id'],
                "quantity" => str_replace(',', '', $value['qty']) * -1,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $prd = DB::table('product')->
                whereId($value['product_id'])->
                first();

            if ($prd->ptype == 'openitem') {

                $latest_cost = collect(DB::select(DB::raw("
                    SELECT
                        MIN(op.cost) as cost
                    FROM
                        openitem_cost op,
                        openitem_productledger opl
                    WHERE
                        opl.product_systemid = '$prd->systemid' AND
                        op.openitemprodledger_id = opl.id AND
                        op.balance > 0
                    ;
                ")))->first();

                $cost = $latest_cost->cost;

                $openitemprodid = DB::table('openitem_productledger')->
                insertGetId([
                    "stockreport_id" => $stockreport_id,
                    "product_systemid" => $prd->systemid,
                    "qty" => str_replace(',', '', $value['qty']) * -1,
                    "cost" => $cost,
                    "last_update" => date('Y-m-d H:i:s'),
                    "status" => 'active',
                    "type" => 'returned',
                    "deleted_at" => null,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
                // process stockout
                app("App\Http\Controllers\OpenitemController")->
                    process_openitem_returning($prd->systemid, $value['qty'] * -1, $stockreport_id, $openitemprodid);
            }

            if ($prd->ptype == 'inventory') {

                $latest_cost = collect(DB::select(DB::raw("
                    SELECT
                        MIN(lc.cost) as cost
                    FROM
                        locationproduct_cost lc,
                        locprod_productledger lpl
                    WHERE
                        lpl.product_systemid = '$prd->systemid' AND
                        lc.locprodprodledger_id= lpl.id AND
                        lc.balance > 0
                    ;
                ")))->first();


                $cost = $latest_cost->cost;

                $locprodid = DB::table('locprod_productledger')->
                    insertGetId([
                        "stockreport_id" => $stockreport_id,
                        "product_systemid" => $prd->systemid,
                        "qty" => str_replace(',', '', $value['qty']),
                        "cost" => $cost,
                        "last_update" => date('Y-m-d H:i:s'),
                        "status" => 'active',
                        "type" => $type,
                        "deleted_at" => null,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                // process stockout locationproduct
                app("App\Http\Controllers\CentralStockMgmtController")->
                    process_locprod_returning($prd->systemid, $value['qty'] * -1,
                    $stockreport_id, $locprodid);
            }
        }
    }
}
