<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Jenssegers\Agent\Agent;
use Log;
use Yajra\DataTables\DataTables;

class CStoreAuditedRptController extends Controller
{
    //
    public function getAuditedNotes()
    {
        return view('cstore_audited_rpt.cstore_audited_report_list');
    }

    public function getAuditedList()
    {
        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');
        $agent = new Agent();

        return view(
            'cstore_audited_rpt.cstore_audited_report',
            [
                'time' => $time,
                'location' => $location,
                'user' => $user,
                'agent' => $agent,

            ]
        );
    }

    public function updateAuditReport(Request $request)
    {
        $allproduct = $this->getProducts();
        $container = json_decode(json_encode($request->container, true));
        $container = collect($container);

        $allproduct->each(function ($item, $key) use ($container) {

            if ($container->contains('id', $item->id)) {

                $item->audited_qty = $container->where('id', $item->id)->first()->audited_qty;
                $item->diff = $container->where('id', $item->id)->first()->diff;
                $item->qty = $container->where('id', $item->id)->first()->qty;
            }
        });

        $s = new SystemID('auditedreport');
        foreach ($allproduct as $key => $value) {

            $data = array(
                'product_id' => $value->id,
                'qty' => ($value->qty < 0) ? 0 : $value->qty,
                'audited_qty' => $value->audited_qty,
                'difference' => $value->diff,
                'systemid' => $s->__toString(),
                'created_at' => date('Y-m-d H:i:s', time()),
            );
            DB::table('auditedreport')->insert($data);
        }

        $data2 = array(
            'auditedreport_systemid' => $s->__toString(),
            'created_at' => date('Y-m-d H:i:s', time()),
        );

        DB::table('auditedreport_list')->insert($data2);
    }

    public function view_audited_report($audited_note_list_id)
    {
        $main = DB::table('auditedreport_list')->where('auditedreport_systemid', $audited_note_list_id)->first();

        $list = DB::table('auditedreport')->where('auditedreport.systemid', $main->auditedreport_systemid)->leftjoin('product', 'product.id', '=', 'auditedreport.product_id')->select(
                'auditedreport.*',
                'product.id as p_id',
                'product.name as name',
                'product.thumbnail_1 as thumbnail_1',
                'product.systemid as psystemid'
            )->get();

        $result = [];
        foreach ($list as $lt) {

            $barcode = DB::table('productbarcode')
            ->where('product_id', $lt->p_id)
                ->where('selected', 1)
                ->first();

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
        $docId = $audited_note_list_id;

        return view(
            'cstore_audited_rpt.cstore_audited_report_confirmed',
            compact('list', 'location', 'user', 'docId', 'time')
        );
    }

    public function confirmed_datatable(Request $request){

        try {
            //code...

            $start = $request->start;
            $length = $request->length;
            $totalRecords = DB::table('auditedreport')->
                where('auditedreport.systemid', $request->data)->
                leftjoin('product', 'product.id', '=', 'auditedreport.product_id')->
                select(
                    'auditedreport.*',
                    'product.id as p_id',
                    'product.name as name',
                    'product.thumbnail_1 as thumbnail_1',
                    'product.systemid as psystemid'
                )->count();

            $list = DB::table('auditedreport')->
                where('auditedreport.systemid', $request->data)->
                leftjoin('product', 'product.id', '=', 'auditedreport.product_id')->
                select(
                    'auditedreport.*',
                    'product.id as p_id',
                    'product.name as name',
                    'product.thumbnail_1 as thumbnail_1',
                    'product.systemid as psystemid'
                )->
                skip($start)->take($length)->
                get();

            $result = [];
            foreach ($list as $lt) {

                $barcode = DB::table('productbarcode')
                ->where('product_id', $lt->p_id)
                ->where('selected',
                    1
                )
                ->first();

                if (is_null($barcode)) {
                    $lt->barcode = $lt->psystemid;
                } else {
                    $lt->barcode = $barcode->barcode;
                }

                array_push($result, $lt);
            }

            $list = collect($result);

            $this->displayTableConfirmed($list,$start, $totalRecords);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function mobileView()
    {
        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');
        return view(
            'cstore_audited_rpt.mob_audited_report',
            ['time' => $time, 'location' => $location, 'user' => $user]
        );
    }

    public function get_product_id(Request $request)
    {

        try {
            $search_string = $request->barcode;

            $is_matrix = false;
            $barcode = DB::table('productbarcode')->
                where('barcode', $search_string)->
                whereNull('deleted_at')->
                first();

            if (empty($barcode)) {
                $barcode = DB::table('productbmatrixbarcode')->
                    where('bmatrixbarcode', $search_string)->
                    whereNull('deleted_at')->
                    first();
                $is_matrix = true;
            }

            $product_id = $barcode->product_id ?? null;

            // If product is from prd_inventory
            $product = DB::table('product')->
                select('product.*', 'locationproduct.quantity as qty')->
                join('locationproduct', 'locationproduct.product_id', 'product.id')->
                where('product.id', $product_id)->
                whereNull('product.deleted_at')->
                first();

            // If product is from prd_openitem
            if (!empty($product)) {
                $product = DB::table('product')->
                select('product.*', 'prd_openitem.qty as qty')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                where('product.id', $product_id)->
                whereNull('product.deleted_at')->
                first();
            }
            if (!empty($product)) {
                $found = [];
                $all_product = $this->getProducts();
                foreach ($all_product as $p) {
                    # code...
                    if ($p->id == $product->id) {
                        array_push($found, $p);
                    }
                }
                $data = collect($found);

                return $this->mobile_display_table($data);
            }
        } catch (Exception $e) {

            return response()->json([
                "message"    =>    "Barcode not found",
                "error"    =>    true
            ]);

            //abort(404);
        }
    }

    public function listPrdAuditedRpt(Request $request)
    {
        try {

            $start = $request->get("start");
            $rowPerPage = $request->get("limit");
            $totalRecords = DB::table('auditedreport_list')->count();

            $data = DB::table('auditedreport_list')->
                    latest()->get()->
                    skip($start)->take($rowPerPage);

            if ($request->has('search') && !empty($request->search)) {

                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);
                $data = $data = DB::table('auditedreport_list')->latest()->
                    get()->
                    filter(function ($value) use ($search) {

                    Log::debug('value:' . $value->auditedreport_systemid);

                    if (
                        preg_match("/$search/i", $value->auditedreport_systemid)
                    ) {

                        return $value;
                    }
                });

                $totalRecords = count($data);
            }

            return Datatables::of($data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('created_at', function ($data) {
                    return date('dMy H:i:s', strtotime($data->created_at));
                })->
                setTotalRecords($totalRecords)->make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function listAuditedRpt(Request $request)
    {
        $start = $request->get("start");
        $rowPerPage = $request->get("length");
        // $this->getProducts();

        $query2 = "
            SELECT
                COUNT(*) as total
            FROM
            (
                SELECT
                    p.id as id,
                    p.systemid as systemid,
                    p.name,
                    p.thumbnail_1,
                    ptype as type,
                    SUM(lpl.qty) as qty,
                    SUM(lpl.qty) as audited_qty,
                    null as diff,
                    IFNULL(b.barcode,NULL) as  barcode
                FROM
                    prd_inventory pr_i,
                    product p
                LEFT JOIN
                    productbarcode b ON b.product_id = p.id AND
                    b.selected = 1 AND
                    b.deleted_at is null
                LEFT JOIN
                    locprod_productledger lpl ON lpl.product_systemid = p.systemid
                LEFT JOIN
                    locationproduct_cost lpc ON lpc.locprodprodledger_id = lpl.id
                WHERE
                    pr_i.product_id = p.id
                GROUP BY
                    p.id,
                    p.systemid,
                    p.name,
                    p.thumbnail_1,
                    ptype,
                    b.barcode
                UNION
                SELECT
                    p.id as id,
                    p.systemid as systemid,
                    p.name,
                    p.thumbnail_1,
                    ptype as type,
                    SUM(opl.qty) as qty,
                    SUM(opl.qty) as audited_qty,
                    null as diff,
                    IFNULL(b.barcode,NULL) as  barcode
                FROM
                    prd_openitem p_o,
                    product p
                LEFT JOIN
                    productbarcode b ON b.product_id = p.id AND
                    b.selected = 1 AND
                    b.deleted_at is null
                LEFT JOIN
                    openitem_productledger opl ON opl.product_systemid = p.systemid
                LEFT JOIN
                    openitem_cost o_c ON o_c.openitemprodledger_id = opl.id
                WHERE
                    p.id =  p_o.product_id
                GROUP BY
                    p.id,
                    p.systemid,
                    p.name,
                    p.thumbnail_1,
                    ptype,
                    b.barcode
            ) as t
        ";

        $query = "
            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(lpl.qty) as qty,
                SUM(lpl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_inventory pr_i,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                locprod_productledger lpl ON lpl.product_systemid = p.systemid
            LEFT JOIN
                locationproduct_cost lpc ON lpc.locprodprodledger_id = lpl.id
            WHERE
                pr_i.product_id = p.id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            UNION
            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(opl.qty) as qty,
                SUM(opl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_openitem p_o,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                openitem_productledger opl ON opl.product_systemid = p.systemid
            LEFT JOIN
                openitem_cost o_c ON o_c.openitemprodledger_id = opl.id
            WHERE
                p.id =  p_o.product_id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            ORDER BY
                systemid DESC
            LIMIT $rowPerPage
            OFFSET $start
            ;
        ";

        $query3 = "
            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(lpl.qty) as qty,
                SUM(lpl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_inventory pr_i,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                locprod_productledger lpl ON lpl.product_systemid = p.systemid
            LEFT JOIN
                locationproduct_cost lpc ON lpc.locprodprodledger_id = lpl.id
            WHERE
                pr_i.product_id = p.id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            UNION
            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(opl.qty) as qty,
                SUM(opl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_openitem p_o,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                openitem_productledger opl ON opl.product_systemid = p.systemid
            LEFT JOIN
                openitem_cost o_c ON o_c.openitemprodledger_id = opl.id
            WHERE
                p.id =  p_o.product_id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            ORDER BY
                systemid DESC
            ;
        ";

        $products =collect(DB::select(DB::raw($query)));

        if ($request->has('container')) {

            foreach ($request->container as $i) {

                if ($products->contains('id', $i['id'])) {

                    $product = $products->where('id', $i['id'])->
                    first();
                    $product->audited_qty =  $i['audited_qty'];
                    $product->diff = $i['diff'];
                }
            }
        }
        $totalRecords = DB::select(DB::raw($query2))[0]->total;

        if ($request->has('search') && !empty($request->search)) {

            $search = trim($request->search);

            Log::debug('request->search:' . $request->search);
            $products = collect(DB::select(DB::raw($query3)))->
                filter(function ($value) use ($search) {

                    Log::debug('value:' . $value->name);

                if (
                    preg_match("/$search/i", $value->name)     ||
                    preg_match("/$search/i", $value->barcode)  ||
                    preg_match("/$search/i", $value->systemid)
                ) {

                    return $value;
                }
                });

            $totalRecords = count($products);
        }


        // $products = $this->get_product_barcode($products);
        return $this->updated_display($products, $start, $totalRecords);
    }

    public function get_product_barcode($products)
    {
        $prod = [];
        for ($i = 0; $i < sizeof($products); $i++) {
            $p = $products[$i];

            $barcode = DB::table('productbarcode')
                ->where('product_id', $p->id)
                ->where('selected', 1)
                ->first();

            if (is_null($barcode)) {
                $p->barcode = $p->systemid;
            } else {
                $p->barcode = $barcode->barcode;
            }

            array_push($prod, $p);
        }

        return $prod;
    }

    public function getProducts()
    {

        $location = DB::table('location')->first();
        $stck = DB::select(
            DB::raw("
            SELECT
                pi.product_id,
                p.name,
                p.systemid,
                sum(srp.quantity) as quantity
            FROM
                stockreportproduct srp,
                prd_inventory pi,
                product p
            WHERE
                p.id = pi.product_id and
                pi.product_id = srp.product_id
            GROUP BY
                pi.product_id,
                p.name,
                p.systemid;
            ")
        );

        foreach ($stck as $s) {

            DB::table('locationproduct')->updateOrInsert(
                ['product_id' => $s->product_id],
                [
                    'quantity' => $s->quantity,
                    'location_id' => $location->id,
                    'damaged_quantity' => 0
                ],
            );
        }

        $query = "

            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(lpl.qty) as qty,
                SUM(lpl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_inventory pr_i,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                locprod_productledger lpl ON lpl.product_systemid = p.systemid
            LEFT JOIN
                locationproduct_cost lpc ON lpc.locprodprodledger_id = lpl.id
            WHERE
                pr_i.product_id = p.id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            UNION
            SELECT
                p.id as id,
                p.systemid as systemid,
                p.name,
                p.thumbnail_1,
                ptype as type,
                SUM(opl.qty) as qty,
                SUM(opl.qty) as audited_qty,
                null as diff,
                IFNULL(b.barcode,NULL) as  barcode
            FROM
                prd_openitem p_o,
                product p
            LEFT JOIN
                productbarcode b ON b.product_id = p.id AND
                b.selected = 1 AND
                b.deleted_at is null
            LEFT JOIN
                openitem_productledger opl ON opl.product_systemid = p.systemid
            LEFT JOIN
                openitem_cost o_c ON o_c.openitemprodledger_id = opl.id
            WHERE
                p.id =  p_o.product_id
            GROUP BY
                p.id,
                p.systemid,
                p.name,
                p.thumbnail_1,
                ptype,
                b.barcode
            ORDER BY
                systemid DESC
        ";
        return collect(DB::select(DB::raw($query)));
    }

    public function displayTableConfirmed($data,$start,$totalRecords)
    {
        try {
            return Datatables::of($data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                    return $data->psystemid;
                })->
                addColumn('barcode', function ($data) {
                    return $data->barcode;
                })->
               addColumn('product_name', function ($data) {
                    $img_src = '/images/product/' .
                        $data->psystemid . '/thumb/' .
                        $data->thumbnail_1;

                    $pub_path = public_path($img_src);

                    if (!empty($data->thumbnail_1) && file_exists($pub_path)) {
                        $img = "<img src=" . asset($img_src) .
                            " data-field='inven_pro_name' style=' width: 25px;
                        height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>";
                    } else {
                        $img_src = '';
                        $img = '';
                    }

                    return $img . $data->name;
                })->
                addColumn('qty', function ($data) {

                    $qty = number_format($data->qty);
                    return $qty;
                })->
                addColumn('audited_qty', function ($data) {
                    return number_format($data->audited_qty);
                })->
                addColumn('difference', function ($data) {
                    return number_format($data->audited_qty - $data->qty);
                })->
                addColumn('stockin', function ($data) {
                    if(($data->audited_qty - $data->qty) >= 0){
                        return number_format($data->audited_qty - $data->qty);
                    }else{
                        return 0;
                    }
                })->
                addColumn('stockout', function ($data) {
                    if (($data->audited_qty - $data->qty) <= 0) {
                        return number_format($data->audited_qty - $data->qty);
                    } else {
                        return 0;
                    }
                })->
                escapeColumns([])->
                setTotalRecords($totalRecords)->
                make(true);

        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function updated_display($data, $start, $totalRecords)
    {
        try {

            return Datatables::of($data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                    return $data->systemid;
                })->
                addColumn('barcode', function ($data) {
                    return $data->barcode;
                })->
                addColumn('product_name', function ($data) {
                    $img_src = '/images/product/' .
                        $data->systemid . '/thumb/' .
                        $data->thumbnail_1;

                    $pub_path = public_path($img_src);

                    if (!empty($data->thumbnail_1) && file_exists($pub_path)) {
                        $img = "<img src=" . asset($img_src) .
                            " data-field='inven_pro_name' style=' width: 25px;
                        height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>";
                    } else {
                        $img_src = '';
                        $img = '';
                    }

                    return $img . $data->name;
                })->
                addColumn('product_qty', function ($data) {
                    $qty = !empty($data->qty) ? $data->qty : 0;

                    return <<<EOD
						<span id="qty_$data->id"  data-field="$data->type">$qty</span>
EOD;
                })->
                addColumn('audited_qty', function ($data) {
                    $product_id = $data->id;
                    // return view('fuel_stockmgmt.inven_qty', compact('product_id'));
                    $val = $data->id;

                    $qty = !empty($data->audited_qty) ? $data->audited_qty : 0;

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
                })->
                addColumn('difference', function ($data) {
                    $product_id = $data->id;

                    $diff = $data->audited_qty - $data->qty;
                    return <<<EOD
                                 <div id="diff_$product_id">$diff </div>
EOD;
                })->rawColumns(['difference'])->
                escapeColumns([])->
                setTotalRecords($totalRecords)->
                make(true);
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    function search_barcode(Request $request)
    {

        try {
            $search_string = $request->barcode;
            // dd($search_string);
            $barcode = DB::table('productbarcode')->
                where('barcode', $search_string)->
                whereNull('deleted_at')->
                first();

            if (empty($barcode)) {
                $barcode = DB::table('productbmatrixbarcode')->
                    where('bmatrixbarcode', $search_string)->
                    whereNull('deleted_at')->
                    first();
            }

            if (empty($barcode)) {
                return response()->json(['message' => 'Product not found', 'error' => true]);
            }

            $product_id = $barcode->product_id;

            // If product is from prd_inventory
            $product = DB::table('product')->
                select('product.*', 'locationproduct.quantity as qty')->
                join('locationproduct', 'locationproduct.product_id', 'product.id')->
                where('product.id', $product_id)->
                whereNull('product.deleted_at')->
                first();

            // If product is from prd_openitem
            if (!empty($product)) {
                $product = DB::table('product')->
                select('product.*', 'prd_openitem.qty as qty')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                where('product.id', $product_id)->whereNull('product.deleted_at')->
                first();
            }

            $prod_sorted = app("App\Http\Controllers\ReceivingNoteController")->get_product();

            foreach ($prod_sorted as $value) {
                # code...
                if ($value->id == $product->id) {

                    $value->Iqty = (int)$request->current_qty + 1;
                }
            }
            if (!empty($request->changed_product)) {
                foreach ($prod_sorted as $p) {
                    # code...
                    foreach ($request->changed_products as $c) {
                        # code...
                        if ($p->product_id == $c['product_id']) {
                            $p->Icost = $c['new_cost'];
                        }
                    }
                }
            }

            return $this->mobile_display_table($prod_sorted);
        } catch (Exception $e) {
            Log::error([
                "Error: "    =>    $e->getMessage(),
                "Line: "    =>    $e->getLine(),
                "File: "    =>    $e->getFile()
            ]);

            return response()->json([
                "message"    =>    "Barcode not found",
                "error"    =>    true
            ]);

            //abort(404);
        }
    }

    public function mobile_display_table($data)
    {
        try {
            return Datatables::of($data)->addIndexColumn()->addColumn('name', function ($data) {
                $img_src = '/images/product/' .
                    $data->systemid . '/thumb/' .
                    $data->thumbnail_1;
                $pub_path = public_path($img_src);

                if (!empty($data->thumbnail_1) && file_exists($pub_path)) {
                    $img = "<img src=" . asset($img_src) .
                        " data-field='inven_pro_name' style=' width: 25px;
                                height: 25px;display: inline-block;margin-right: 8px;
                                object-fit:contain;'>";
                } else {
                    $img_src = '';
                    $img = '';
                }

                return $img . $data->name;
            })->addColumn('qty', function ($data) {
                $qty = !empty($data->qty) ? $data->qty : 0;
                return <<<EOD
                                <h5 class="text-bold text-center qty"
						        onclick="increase('$data->id')"
						        id="qty_$data->id">$qty	</h5>
EOD;
            })->addColumn('button', function ($data) {
                $val = $data->id;
                $btn = '<button class="mr-0 text-center"
                                    style="height:60px; width:25px; border-radius:10px;
                                    background:#add8e6;color:#fff; outline:none !important;
                                    border:1px solid #add8e6;"
                                    onclick="decrease(\'' . $val . '\')"
                                    id="btn_' . $val . '"> -
                                    </button>';
                return $btn;
            })->escapeColumns([])->make(true);
        } catch (Exception $e) {
            Log::error([
                "Error: "    =>    $e->getMessage(),
                "Line: "    =>    $e->getLine(),
                "File: "    =>    $e->getFile()
            ]);

            return response()->json([
                "message"    =>    "Barcode not found",
                "error"    =>    true
            ]);

            //abort(404);
        }
    }

    public function datatable()
    {
        return response()->json([
            "message"    =>    "Barcode not found",
            "error"    =>    true
        ]);
    }
}
