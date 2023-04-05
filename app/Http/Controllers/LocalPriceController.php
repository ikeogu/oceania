<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Location;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Log;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Yajra\DataTables\DataTables;

class LocalPriceController extends Controller
{
    public function local_price()
    {
        try {

            $data = DB::table('product')->whereNotIn('ptype', ['oilgas'])->get()->count();

            $is_active_all = DB::table('localprice')->where([
                "active" => 1,
            ])->get();

            $is_deactive_all = DB::table('localprice')->where([
                "active" => 0,
            ])->get();

            $is_all_active = 0;
            if ($is_active_all->count() == $data) {
                $is_all_active = 1;
            }

            if ($is_deactive_all->count() == $data) {
                $is_all_active = 0;
            }

            return view(
                'local_price.landing_screend',
                compact('is_all_active')
            );
        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function product_barcode($systemid)
    {
        try {
            $product = DB::table('product')->wheresystemid($systemid)->first();

            return view(
                'local_price.barcode',
                compact('product')
            );
        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function product_barcode_datatable($systemid)
    {
        try {
            Log::debug('product_barcode_datatable(' . $systemid . ')');

            $product = DB::table('product')->
                wheresystemid($systemid)->
                first();

            Log::debug('product_barcode_datatable: product_id=' .
                $product->id . ', systemid=' .
                $product->systemid);

            $barcode = [];

            $default = collect();

            $default['systemid'] = $product->systemid;

            Log::debug('product_barcode_datatable: default=' .
                json_encode($default));

            $barcode[] = $default;

            Log::debug('product_barcode_datatable: 1. barcode=' .
                json_encode($barcode));

            $productbarcode = DB::table('productbarcode')->
                where('product_id', $product->id)->
                orderBy('id', 'desc')->
                get();

            Log::debug('product_barcode_datatable: BEFORE productbarcode=' .
                json_encode($productbarcode));

            // Prepend a new record at the start of the collection
            $o = new \stdClass();
            $o->id = $product->id;
            $o->product_id = $product->id;
            $o->barcode = $product->systemid;
            $productbarcode->prepend($o);

            Log::debug('product_barcode_datatable:  AFTER productbarcode=' .
                json_encode($productbarcode));

            $productbarcode->map(function ($f) use ($barcode) {
                if (!empty($barcode[0][0])) {

                    Log::debug('product_barcode_datatable map(): 2. barcode=' .
                        json_encode($barcode));

                    $product_barcode = collect();

                    Log::debug('product_barcode_datatable map(): product_barcode=' .
                        json_encode($product_barcode));

                    $product_barcode['systemid'] = $f->barcode;

                    $notes = $product_barcode->notes;
                    $notes .= "Start Date: <b>";
                    $notes .= date("dMy", strtotime($f->startdate));
                    $notes .= "</b><br>";
                    $notes .= "Expiry Date: <b>";
                    $notes .= date("dMy", strtotime($f->expirydate));
                    $notes .= "</b>";
                    $product_barcode['notes'] = $notes;

                    $barcode[] = $barcode;
                }
            });

            Log::debug('product_barcode_datatable: 3. barcode=' .
                json_encode($barcode));


            return Datatables::of($productbarcode)->
                addIndexColumn()->
                addColumn('product_barcode', function ($memberList) {

                Log::debug('product_barcode_datatable: memberList=' .
                    json_encode($memberList));

                $code = new DNS1D();
                $code = $code->getBarcodePNG(trim($memberList->barcode), "C128");
                $bc = $memberList->barcode;

                return <<<EOD
                    <img src="data:image/png;base64,$code" style="display:block;"
                            alt="barcode" class="mx-auto" width="200px" height="70px "/>
                    $bc
EOD;
            })->
            addColumn('product_qr', function ($memberList) {
                $code = new DNS2D();
                $code = $code->getBarcodePNG($memberList->barcode, "QRCODE");
                return <<<EOD
					<img src="data:image/png;base64,$code" style="display:block;"
						 alt="barcode" class="mx-auto" height='70px' width='70px'/>
EOD;
            })->
            addColumn('product_color', function ($memberList) {
            })->
            addColumn('product_matrix', function ($memberList) {
            })->
            addColumn('product_notes', function ($memberList) {
                return !empty($memberList->notes) ?
                    $memberList['notes'] : '';
            })->
            addColumn('product_qty', function ($memberList) {
            })->
            addColumn('product_print', function ($memberList) {
            })->
            addColumn('action', function ($memberList) {
                    return '<a href="">CLick</a>';
            })->
            escapeColumns([])->make(true);
        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function get_product_barcode($products)
    {
        Log::debug("get_product_barcode: products" . json_encode($products));
        $systemids = [];
        foreach ($products as $prd) {
            array_push($systemids, $prd->systemid);
        }

        $product_ids = DB::table('product')
            ->whereIn('systemid', $systemids)
            ->get();

        $prod = [];
        for ($i = 0; $i < sizeof($products); $i++) {
            $prd = $products[$i];
            $p = $product_ids[$i];

            $barcode = DB::table('productbarcode')
                ->where('product_id', $p->id)
                ->where('selected', 1)
                ->first();

            if (is_null($barcode)) {
                $prd->barcode = $prd->systemid;
            } else {
                $prd->barcode = $barcode->barcode;
            }

            array_push($prod, $prd);
        }

        return $prod;
    }

    public function local_price_datatable(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");
            $totalRecords = DB::table('product')->
                leftjoin(
                    'prd_inventory',
                    'prd_inventory.product_id',
                    'product.id'
                )->
                whereIn('product.ptype', ['inventory'])->
                select(
                    "product.*",
                    "product.id as z_product_id",
                    'prd_inventory.loyalty'
                )->
                count();

            $data = DB::table('product')->
                leftjoin(
                    'prd_inventory',
                    'prd_inventory.product_id',
                    'product.id'
                )->
                whereIn('product.ptype', ['inventory'])->
                select(
                    "product.*",
                    "product.id as z_product_id",
                    'prd_inventory.loyalty'
                )->
                offset($start)->
                limit($rowperpage)->
                get();

            $oridata = $data;

            Log::debug('local_price_datatable: data=' . json_encode($data));

            $ndata = $data->filter(function ($product) {
                $rec = DB::table('localprice')->
                    where('product_id', $product->z_product_id)->
                    orderBy('localprice.created_at', 'desc')->
                    first();

                $imp = Log::debug('local_price_datatable: z_product_id=' .
                    $product->z_product_id);

                Log::debug('local_price_datatable: rec=' . json_encode($rec));

                if (!empty($rec)) {
                    foreach ($rec as $key => $value) {
                        $product->$key = $value;
                    }
                    return $product;
                }
            });

            $ndata = $this->get_product_barcode($ndata);

            /* Log::debug('local_price_datatable: AFTER filter data=' .
                json_encode($ndata)); */

            return Datatables::of($ndata)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('product_systemid', function ($memberList) {
                    //return $memberList->systemid;
                    $url = route("franchise.location_price.barcode", $memberList->systemid);
                    return <<<EOD
                            <span class="os-linkcolor" style="cursor:pointer" onclick="window.open('$url')">$memberList->systemid</span>
EOD;
                })->
                addColumn('product_name', function ($memberList) {

                    $img_src = '/images/product/' .
                        $memberList->systemid . '/thumb/' .
                        $memberList->thumbnail_1;

                    if (
                        !empty($memberList->thumbnail_1) &&
                        file_exists(public_path() . $img_src)
                    ) {

                        $img = "<img src='$img_src' data-field='inven_pro_name'
                            style=' width: 30px; height: 30px;display: inline-block;
                            border-radius:5px;margin-right: 8px;
                            object-fit:contain;'>";
                    } else {
                        $img = '';
                    }

                    return $img . $memberList->name;
                })->
                addColumn('product_lower', function ($memberList) {
                    return number_format(($memberList->lower_price ?? 0) / 100, 2);
                })->
                addColumn('product_price', function ($data) {
                    //    return number_format(($memberList->recommended_price ?? 0) / 100, 2);

                    $price = number_format(($data->recommended_price ?? 0) / 100, 2) ?? "0.00";
                    $price_inp = $data->recommended_price ?? "";

                    $ptype = $data->ptype;

                    $validation = $ptype != 'inventory' ? 'bypass' : 'strict';

                        /*  Log::debug('local_price_datatable: addColumn product_price:' .
                    json_encode($data)) */;

                    return <<<EOD
					<span class="os-linkcolor" style="cursor:pointer"
						onclick="updatePrice('$price_inp','$data->z_product_id','$data->lower_price', '$data->upper_price','$validation')">$price
					</span>
EOD;
                })->
                addColumn('product_upper', function ($memberList) {
                    return number_format(($memberList->upper_price ?? 0) / 100, 2);
                })->
                addColumn('product_loyalty', function ($memberList) {
                    return $memberList->loyalty ?? 0;
                })->
                addColumn('product_stock', function ($memberList) {
                    $qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($memberList->z_product_id);
                    //$memberList->cost_value = $qty * ($memberList->cost/100);
                    $link = route("stocking.showproductledger", $memberList->z_product_id);
                    return <<<EOD
					<a href="javascript:window.open('$link')"
						style="text-decoration:none;">$qty
					</a>
EOD;
                })->
                addColumn('product_cost', function ($memberList) {

                    if (!empty($memberList->product_id)) {

                        $prd_info = DB::table('product')->
                            whereId($memberList->product_id)->
                            whereNull('deleted_at')->
                            first();;

                        $lprec = DB::table('locprod_productledger')->
                            where('product_systemid', $prd_info->systemid)->
                            whereNotNull('cost')->
                            orderBy('created_at', 'desc')->
                            first();

                        $prd_cost = empty($lprec->cost) ? 0 : $lprec->cost;

                    } else {
                        $prd_cost = 0;
                    }

                    $formatted = number_format(($prd_cost / 100), 2);
                    return <<<EOD
					<a href="local_price/locprod_cost/$memberList->systemid"
                    id="cost_$memberList->systemid" style="text-decoration: none;" target="_blank"
                    >
                        $formatted
					</a>
EOD;
                })->
                addColumn('product_cost_value', function ($memberList) {

                    $cost_value = DB::select(
                        DB::raw("
                    SELECT
                        sum(CAST(lp.cost AS SIGNED) * CAST(lp.balance AS SIGNED)) as total
                    FROM
                        locationproduct_cost lp,
                        product p,
                        locprod_productledger lpl
                    WHERE
                        p.systemid = lpl.product_systemid AND
                        lpl.id = lp.locprodprodledger_id AND
                        p.id = '" . $memberList->product_id . "'
                    ")
                    );

                    $formatted = null;
                    if (!empty($cost_value)) {
                        DB::table('locationproduct')->where(
                                'id',
                                $memberList->product_id
                            )->update([
                                'costvalue' => $cost_value[0]->total,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        $formatted = is_null($cost_value[0]->total) ? number_format(0, 2) :
                            number_format($cost_value[0]->total / 100, 2);
                    }

                    return <<<EOD
					<a id="cv_$memberList->systemid" style="text-decoration: none;">
                        $formatted
					</a>
EOD;
                })->
                addColumn('product_value', function ($data) {
                    if (!empty($data->recommended_price)) {
                        $price = $data->recommended_price;
                        $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                            qtyAvailable($data->z_product_id);

                        DB::table('locationproduct')->
                            where('product_id',$data->z_product_id)->
                            update([
                                'value' => $price * $qty,
                                'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $price = 0;
                        $qty = 0;
                    }

                    return number_format($price / 100 * $qty, 2);
                })->
                addColumn('product_royalty', function ($memberList) {
                    return $memberList->royalty ?? 0;
                })->
                addColumn('active', function ($memberList) {
                    $active = $memberList->active == 1 ? "active_button_activated" : '';
                    return <<<EOD
                        <button
                            class="prawn btn active_button $active"
                            onclick="activate_func($memberList->z_product_id , this)"
                            style="min-width:75px;font-size:14px">Display
                        </button>
EOD;
                })->escapeColumns([])->
                setTotalRecords($totalRecords)->
                make(true);
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function locprod_qty_distribution(Request $request)
    {
        try {
            $op_id = $request->op_id;
            $qty_population = DB::select(DB::raw("
                SELECT
                    opc.csreceipt_id,
                    opc.stockreport_id,
                    opc.qty_taken,
                    cr.systemid
                FROM
                    locationproduct_cost oc
                JOIN locprodcost_qtydist opc ON opc.locprodcost_id = oc.id
                LEFT JOIN cstore_receipt cr ON cr.id = opc.csreceipt_id
                JOIN locprod_productledger opl ON opl.id = oc.locprodprodledger_id
                LEFT JOIN stockreport sr ON sr.id = opl.stockreport_id
                WHERE
                    oc.id = $op_id AND
                    opc.qty_taken <> 0
                ;
            "));

            $c_qty_pop = [];
            foreach ($qty_population as $qp) {
                if (isset($qp->stockreport_id)) {
                    $sreport = DB::select(DB::raw("
                        SELECT
                            *
                        FROM
                            stockreport
                        WHERE id = $qp->stockreport_id
                    "));
                    $qp->sr_systemid = (sizeof($sreport) > 0) ? $sreport[0]->systemid : null;

                    $qp->is_returning = false;

                    if (isset($qp->sr_systemid)) {
                        $is_returningnote = DB::table('returningnote')
                            ->where('systemid', $qp->sr_systemid)
                            ->first();
                        $qp->is_returning = (!is_null($is_returningnote)) ? true : false;
                    }
                }

                if (isset($qp->csreceipt_id)) {
                    $crreport = DB::select(DB::raw("
                        SELECT
                            *
                        FROM
                            cstore_receipt
                        WHERE id = $qp->csreceipt_id
                    "));
                    $qp->cr_systemid = (sizeof($crreport) > 0) ? $crreport[0]->systemid : null;
                }

                array_push($c_qty_pop, $qp);
            }
            $qty_population = (sizeof($c_qty_pop) > 0) ? $c_qty_pop : $qty_population;

            $qty_population = array_reverse($qty_population);

            $res = [
                'records' => sizeof($qty_population),
                'data' => $qty_population,
            ];

            return response($res, 200);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function locationPriceUpdate(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "field" => "required",
                "data" => "required",
                "product_id" => "required",
            ]);

            if ($validation->fails()) {
                new \Exception("Invalid data");
            }

            $is_exist = DB::table('localprice')->where([
                "product_id" => $request->product_id,
            ])->orderBy('created_at', 'desc')->first();

            $update_array = [];

            Log::debug('locationPriceUpdate: request=' .
                json_encode($request->all()));

            switch ($request->field) {
                case "price":
                    if (!empty($is_exist)) {
                        if ($is_exist->recommended_price == $request->data) {
                            abort(404);
                        }
                    }
                    $update_array['recommended_price'] = (float) $request->data;
                    $msg = ucfirst($request->field) . " updated";
                    break;
                case "active":
                    $update_array['active'] = empty($is_exist->active) ?
                        1 : !$is_exist->active;

                    if ($update_array['active'] == true) {
                        $msg = "Location price has been activated";
                    } else {
                        $msg = "Location price has been deactivated";
                    }
                    break;
            }

            $update_array["updated_at"] = date("Y-m-d H:i:s");

            if (!empty($is_exist)) {
                DB::table('localprice')->where('id', $is_exist->id)->update($update_array);
            } else {

                $update_array["product_id"] = $request->product_id;
                $update_array["created_at"] = date("Y-m-d H:i:s");

                DB::table('localprice')->insert($update_array);
            }

            $response = ["success" => true, "msg" => $msg];

            return response()->json($response);
        } catch (Exception $e) {
            Log::error([
                "error" => $e->getmessage(),
                "file" => $e->getfile(),
                "line" => $e->getline(),
            ]);
            abort(404);
        }
    }

    public function save_prd_cost(Request $request)
    {
        try {
            $product = DB::table('product')
                ->where('systemid', $request->product_id)
                ->select('id')->first();

            if (!empty($product)) {
                DB::table('locationproduct')->where(
                    'product_id',
                    $product->id
                )->update([
                    'cost' => $request->cost_amount,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                Log::error([
                    'Message' => "Error: Product cost update failed. Product Not found",
                    'ProductId' => $request->product_id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
        }
    }

    public function priceToggleAll(Request $request)
    {
        try {

            $franchiseid = $request->franchiseid;
            $locationid = $request->locationid;
            $all_btn_state = $request->all_btn_state;
            $date = $request->date;

            $data = DB::table('product')->whereNotIn('ptype', ['oilgas'])->get();

            $data->map(function ($z) use ($all_btn_state) {

                $condition = [
                    "product_id" => $z->id,
                ];

                $locationproductprice_data = DB::table('localprice')->where($condition)->first();

                if (!empty($locationproductprice_data)) {
                    DB::table('localprice')->where($condition)->update(['active' => !$all_btn_state]);
                } else {
                    $condition['created_at'] = date('Y-m-d H:i:s');
                    $condition['updated_at'] = date('Y-m-d H:i:s');
                    $condition['active'] = !$all_btn_state;
                    DB::table('localprice')->insert($condition);
                }
            });

            if (!$all_btn_state == true) {
                $msg = "All location price has been activated";
            } else {
                $msg = "All location price has been deactivated";
            }

            $response = ["success" => true, "msg" => $msg];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::info([
                "error" => $e->getmessage(),
                "file" => $e->getfile(),
                "line" => $e->getline(),
            ]);
            abort(404);
        }
    }

    //-----------------------------------------------------------------
    public function StockIn()
    {
        $company = Company::first();
        $location = Location::first();
        return view("inv_stockmgmt.stockin", compact('location', 'company'));
    }

    public function StockOut()
    {
        $company = Company::first();
        $location = Location::first();
        return view("inv_stockmgmt.stockout", compact('location', 'company'));
    }

    public function stockInDatatable(Request $request)
    {

        $start = $request->start;
        $length = $request->length;

        $data = Product::query()->
            where('ptype', "inventory")->
            skip($start)->
            take($length)->
            get();

        if ($request->type == 'out') {
            $data = $data->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($product->id) > 0;
            });

           
            $totalRecords =  $data->count();
        } else {
            $totalRecords = Product::query()->where('ptype', "inventory")->count();
        }
        return Datatables::of($data)->
            setOffset($start)->
            addIndexColumn()->
            addColumn('product_name', function ($data) {

                $img_src = '/images/product/' .
                    $data->systemid . '/thumb/' .
                    $data->thumbnail_1;

                if (
                    !empty($data->thumbnail_1) &&
                    file_exists(public_path() . $img_src)
                ) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
					style=' width: 30px; height: 30px;display: inline-block;
					border-radius:5px;margin-right: 8px;
					object-fit:contain;'>";
                } else {
                    $img = '';
                }

                return $img . $data->name;
            })->
            addColumn('inven_existing_qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($product_id);
                //$qty = number_format($qty, 2);
                return <<<EOD
			<span id="qty_$product_id">$qty</span>
EOD;
            })->
            addColumn('inven_qty', function ($data) {
                $product_id = $data->id;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->
            rawColumns(['inven_existing_qty', 'inven_qty', 'product_name'])->
            setTotalRecords($totalRecords)->
            make(true);
    }

    public function prodloc_cost()
    {
        return view('local_price.prodloc_cost');
    }

    public function locationproduct_cost(Request $request)
    {
        $prd_sysid = $request->systemid;
        $prd_info = DB::table('product')->
            where('systemid', $prd_sysid)->
            first();

        return view('local_price.prodloc_cost', compact('prd_sysid', 'prd_info'));
    }

    public function locprod_cost_datatable(Request $request)
    {
        try {
            $prd = DB::table('product')->
                where('systemid', $request->systemid)->
                first();

            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $totalRecords = DB::table('locprod_productledger')->
                join('product', 'product.systemid', 'locprod_productledger.product_systemid')->join(
                    'locationproduct_cost',
                    'locationproduct_cost.locprodprodledger_id',
                    'locprod_productledger.id'
                )->
                select(
                    'locprod_productledger.id as record_id',
                    'locprod_productledger.stockreport_id as sr_id',
                    'locprod_productledger.csreceipt_id  as cr_id',
                    'locprod_productledger.type as doc_type',
                    'locationproduct_cost.id as op_id',
                    'locationproduct_cost.cost as cost',
                    'locationproduct_cost.qty_in as stockin',
                    'locationproduct_cost.qty_out as stockout',
                    'locationproduct_cost.balance as balance',
                    'locationproduct_cost.created_at as created_at',
                    'locationproduct_cost.updated_at as updated_at',
                    'product.id as product_id'
                )->
                where('locprod_productledger.product_systemid', $request->systemid)->
                orderBy('locprod_productledger.created_at', 'DESC')->
                count();

            $cost_data = DB::table('locprod_productledger')->
                join('product', 'product.systemid', 'locprod_productledger.product_systemid')->
                join(
                    'locationproduct_cost',
                    'locationproduct_cost.locprodprodledger_id',
                    'locprod_productledger.id'
                )->
                select(
                    'locprod_productledger.id as record_id',
                    'locprod_productledger.stockreport_id as sr_id',
                    'locprod_productledger.csreceipt_id  as cr_id',
                    'locprod_productledger.type as doc_type',
                    'locationproduct_cost.id as op_id',
                    'locationproduct_cost.cost as cost',
                    'locationproduct_cost.qty_in as stockin',
                    'locationproduct_cost.qty_out as stockout',
                    'locationproduct_cost.balance as balance',
                    'locationproduct_cost.created_at as created_at',
                    'locationproduct_cost.updated_at as updated_at',
                    'product.id as product_id'
                )->
                where('locprod_productledger.product_systemid', $request->systemid)->
                orderBy('locprod_productledger.created_at', 'DESC')->
                offset($start)->
                limit($rowperpage)->
                get();

            $updated_data = collect();

            foreach ($cost_data as $data) {
                if ($data->doc_type == 'stockin') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->
                        first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock In';

                } elseif ($data->doc_type == 'received') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->
                        first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Received';
                } elseif ($data->doc_type == 'stockout') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->
                        first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock Out';
                } elseif ($data->doc_type == 'returned') {
                    $stockreportdata = DB::table('stockreport')->
                        whereId($data->sr_id)->
                        first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Returned';
                } elseif ($data->doc_type == 'cash_sales') {

                    $stockreportdata = DB::table('cstore_receipt')->
                        whereId($data->cr_id)->
                        first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Cash Sales';
                }
                $updated_data->push($data);
            }

            return Datatables::of($updated_data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('doc_no', function ($data) {
                    $prd_cost = empty($data->cost) ? 0 : $data->cost;

                    $doc_no = $data->doc_no ?? '';

                    if ($data->doc_type == 'Received') {
                        $doc_no = '<a  href="javascript:window.open(\'' .
                            route('receiving_list_id', $data->doc_no) . '\')"
                     style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                    } elseif ($data->doc_type == 'Stock In') {
                        $doc_no = '<a  href="javascript:window.open(\'' . route('stocking.stock_report', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                    } elseif ($data->doc_type == 'Stock Out') {
                        $doc_no = '<a  href="javascript:window.open(\'' . route('stocking.stock_report', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                    } elseif ($data->doc_type == 'Returned') {
                        $doc_no = '<a  href="javascript:window.open(\'' . route('returning.stock_report', $data->doc_no) . '\')" style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                    } elseif ($data->doc_type == 'Cash Sales') {
                        $doc_no = '<a   href="javascript:void(0)"
									onclick="showReceipt(' . $data->cr_id . ')"
                    style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                    }
                    return $doc_no;
                })->
                addColumn('type', function ($data) {
                    return $data->doc_type;
                })->
                addColumn('cost_date', function ($data) {
                    $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                    return $created_at;
                })->
                addColumn('cost', function ($data) {
                    $prd_cost = empty($data->cost) ? 0 : $data->cost;

                    if (empty($data->stockout) || $data->stockout == 0 || $data->doc_no == 'cash_sales') {
                        $cost = '<a  href="javascript:void(0)"  onclick="open_update_cost_modal(' .
                            $data->product_id . ',' . $prd_cost . ',' . $data->record_id . ',' .
                            $data->record_id . ')" data-prod="' . $data->product_id .
                            '" style="text-decoration:none;" class="">' .
                            number_format($data->cost / 100, 2) . ' </a>';
                    } else {
                        $cost = '<a style="text-decoration:none;" class="">' .
                            number_format($data->cost / 100, 2) . ' </a>';
                    }

                    return $cost;
                })->
                addColumn('stockin', function ($data) {
                    return $data->stockin;
                })->
                addColumn('stockout', function ($data) {

                    $so_qty = empty($data->stockout) ? 0 : $data->stockout * -1;

                    if (empty($so_qty) || $so_qty == 0) {
                        return $so_qty;
                    } else {
                        return '<a href="#" onclick="show_doc_qty_modal(' .
                            $data->stockout . ', ' . $data->op_id . ')"
                        style="text-decoration:none;" class="">' .
                            $so_qty . ' </a>';
                    }
                })->
                addColumn('balance', function ($data) {
                    return $data->balance;
                })->
                escapeColumns([])->
                setTotalRecords($totalRecords)->
                make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function locprod_update_cost(Request $request)
    {
        try {
            DB::table('locprod_productledger')->
                whereId($request->record_id)->
                update([
                    'cost' => $request->new_cost,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            DB::table('locationproduct_cost')->
                where('locprodprodledger_id', $request->record_id)->
                update([
                    'cost' => $request->new_cost,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return response()->json(["status" => true]);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }
}
