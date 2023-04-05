<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Http\Controllers\SyncSalesController;
use App\Models\Company;
use App\Models\Location;
use App\Models\MerchantPrdCategory;
use App\Models\MerchantProduct;
use App\Models\PrdOpenitem;
use App\Models\PrdPrdCategory;
use App\Models\PrdSubCategory;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Milon\Barcode\DNS1D;
use Yajra\DataTables\DataTables;

class OpenitemController extends Controller
{
    public static $IMG_PRODUCT_LINK = "images/product/";
    public function openitem()
    {
        try {

            $test = null;

            return view(
                'openitem.openitem_landing',
                compact('test')
            );
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function save()
    {
        try {
            $data = array();
            // WARNING: Hardcoding location_id=1
            $systemid = SystemID::openitem_system_id(1);
            $merchant = DB::table('company')->first();
            $product = Product::create([
                "systemid" => $systemid,
                "name" => null,
                'ptype' => 'openitem',
            ]);

            $prdOpenitem = PrdOpenitem::create([
                "product_id" => $product->id,
                "price" => 0.00,
                "qty" => 0,
                "loyalty" => $merchant->loyalty_pgm,
            ]);

            if (Auth::user() != null) {
                $merchant_pdr = MerchantProduct::create([
                    "product_id" => $product->id,
                    "merchant_id" => $merchant->id,
                ]);
                $data['merchant_pdr'] = DB::table('merchantproduct')->whereId($merchant_pdr->id)->first();
            }

            //gather open item data
            $data['prd_openitem'] = DB::table('prd_openitem')->whereId($prdOpenitem->id)->first();

            $data['product'] = DB::table('product')->whereId($product->id)->first();

            //send the data to OCOSYSTEM
            SyncSalesController::curlRequest(
                env('MOTHERSHIP_URL') . '/sync-openitem',
                json_encode($data)
            );

            return [
                "data" => $prdOpenitem,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function get_latest_cost_openitem_landing_tbl($data)
    {

        $updated_data = collect();

        foreach ($data as $prd) {
            $prd_info = DB::table('product')->whereId($prd->product_id)->whereNUll('deleted_at')->first();

            $prd->cost = 0;

            if (!empty($prd_info)) {
                $latest_cost = DB::table('openitem_productledger')->where('product_systemid', $prd_info->systemid)->whereNotNull('cost')->orderBy('created_at', 'desc')->first();

                $prd->cost = empty($latest_cost) ? 0 : $latest_cost->cost;
            }
            $updated_data->push($prd);
        }
        return $updated_data;
    }

    public function listPrdOpenitem(Request $request)
    {
        try {


            Log::info("***** listPrdOpenitem: START *****");
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");
            Log::info("listPrdOpenitem:updated quantity: start");

            PrdOpenitem::latest()->get()->map(function ($f) {
                $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->
                qtyAvailable($f->product_id);
                $f->update();
            });
            Log::info("listPrdOpenitem:updated quantity: end");

            $query1 = "
                SELECT
                   COUNT(*) AS total
                FROM
                    prd_openitem p
                LEFT JOIN product pr ON pr.id = p.product_id
                WHERE
                    pr.deleted_at IS NULL
                ;
            ";
            $query2 = "
                SELECT
                    p.id,
                    pr.systemid,
                    p.product_id as product_id,
                    pr.name,
                    pr.thumbnail_1,
                    p.loyalty,
                    p.royalty,
                    p.price,
                    p.qty,
                    p.profitloss,
                    b.barcode AS barcode,
                    IFNULL(MAX(opl.cost),0) as cost,
                    (IFNULL(MAX(opl.cost),0) * p.qty) as cost_value,
                    null AS can_delete
                FROM
                    prd_openitem p
                LEFT JOIN product pr ON pr.id = p.product_id
                LEFT JOIN openitem_productledger opl ON opl.product_systemid = pr.systemid
                LEFT JOIN productbarcode b ON b.product_id = p.product_id AND
                    b.deleted_at IS NULL AND
                    b.selected = 1
                WHERE
                    pr.deleted_at IS NULL
                GROUP BY
                    p.id,
                    pr.systemid,
                    p.product_id,
                    pr.name,
                    p.loyalty,
                    p.royalty,
                    p.price,
                    p.qty,
                    p.profitloss,
                    pr.thumbnail_1,
                    b.barcode
                ORDER BY
                    p.created_at DESC
                LIMIT $rowperpage
                OFFSET $start
                ;
            ";

            $query3 = "
                SELECT
                    p.id,
                    pr.systemid,
                    p.product_id as product_id,
                    pr.name,
                    pr.thumbnail_1,
                    p.loyalty,
                    p.royalty,
                    p.price,
                    p.qty,
                    p.profitloss,
                    IFNULL(MAX(opl.cost),0) as cost,
                    (IFNULL(MAX(opl.cost),0) * p.qty) as cost_value,
                    null AS can_delete,
                    b.barcode as barcode
                FROM
                    prd_openitem p
                LEFT JOIN product pr ON pr.id = p.product_id
                LEFT JOIN openitem_productledger opl ON opl.product_systemid = pr.systemid
                LEFT JOIN productbarcode b ON b.product_id = p.product_id AND
                    b.deleted_at IS NULL AND
                    b.selected = 1
                WHERE
                    pr.deleted_at IS NULL
                GROUP BY
                    p.id,
                    pr.systemid,
                    p.product_id,
                    pr.name,
                    p.loyalty,
                    p.royalty,
                    p.price,
                    p.qty,
                    p.profitloss,
                    pr.thumbnail_1,
                    b.barcode
                ORDER BY
                    p.created_at DESC
                ;
            ";


            $totalRecords =  collect(DB::select(DB::raw($query1)))->first()->total;
            $data = collect(DB::select(DB::raw($query2)));

            if ($request->has('search') && !empty($request->search)) {
				$data = collect(DB::select(DB::raw($query3)));

                $search = trim($request->search);

				Log::debug('request->search:'. $request->search);

                $data= $data->filter(function ($value) use ($search) {

					Log::debug('value:' . $value->name);

					if (preg_match("/$search/i", $value->name)     ||
						preg_match("/$search/i", $value->barcode)  ||
						preg_match("/$search/i", $value->systemid) ) {

                            return $value;
					}
                });

				$totalRecords = count($data);
            }


            Log::info("listPrdOpenitem:display view: start");

            $view = Datatables::of($data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('systemid', function ($data) {
                return <<<EOD
                    <a href="/openitem/products/barcode/$data->systemid" target="_blank"
                     id="prd_$data->systemid" style="text-decoration: none;"> $data->systemid</a>
EOD;
            })->addColumn('product_name', function ($data) {
                 $url = url('/' . static::$IMG_PRODUCT_LINK . $data->systemid . "/thumb/" .
                    $data->thumbnail_1);
                $name = $data->name == null ? "Product Name" : $data->name;

                $exist =  <<<EOD

                            <a href="javascript:void(0)" style="text-decoration: none;padding-top: 15px;" onclick="detailProduct('$data->product_id')">
                            <img width="25px" height="25px" style="margin: 0px 0px 0px 0px;object-fit:contain"
                                src="$url" alt=""> $name</a>
EOD;
                $not_ex =  <<<EOD
                            <a href="javascript:void(0)" style="text-decoration: none;padding-top: 15px;" onclick="detailProduct('$data->product_id')">
                            <img src='' alt='' width='0px' height='25px'>$name</a>

EOD;
                return $data->name == null ? $not_ex : $exist;
            })->addColumn('barcode', function ($data) {
                return $data->barcode;
            })->editColumn('cost', function ($data) {
                $cost = number_format($data->cost == null ? 0 : $data->cost / 100, 2);
                return <<<EOD
                <a href="openitem/openitem_cost/$data->systemid" id="cost_$data->systemid" style="text-decoration: none;" target="_blank"> $cost</a>
EOD;
            })->editColumn('qty', function ($data) {
                $qty = $data->qty;
                return <<< EOD
                     <a target="_blank" id="qty_data->systemid" href="/openitem/prdledger/$data->systemid" style="text-decoration: none;"> $qty </a>

EOD;
            })->editColumn('cost_value', function ($data) {
                $cost_value = DB::select(
                    DB::raw("
                            select
                                sum( CAST(op.cost AS SIGNED) * CAST(op.balance AS SIGNED)) as total
                            from
                                openitem_cost op,
                                product p,
                                openitem_productledger opl
                            where
                                p.systemid = opl.product_systemid and
                                opl.id = op.openitemprodledger_id and
                                p.id =  $data->product_id
                        ")
                );
                $total = !empty($cost_value) ? $cost_value[0]->total : 0;

                DB::table('prd_openitem')->where(
                    'id',
                    $data->id
                )->update([
                    'costvalue' => $total,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return is_null($total) ? number_format(0, 2) :
                    number_format($total / 100, 2);
            })->editColumn('loyalty', function ($data) {

                return number_format($data->loyalty, 2);
            })->editColumn('royalty', function ($data) {

                return number_format($data->royalty, 2);
            })->editColumn('price', function ($data) {

                $price = number_format($data->price == null ? 0 : $data->price / 100, 2);
                return <<<EOD
                    <a href='javascript:void(0)' style='text-decoration: none;'
                    onclick="prdOpenItemPrice('$data->id','$data->price' ,'$data->product_id')">
                    $price</a>
EOD;
            })->addColumn('action', function ($row) {
                $transaction = DB::table('cstore_receiptproduct')->where('product_id', $row->product_id)->first();

                $stock = DB::table('stockreportproduct')->where('product_id', $row->product_id)->first();

                if (empty($transaction) && empty($stock)) {
                    $row->can_delete = true;
                } else {
                    $row->can_delete = false;
                }
                if ($row->can_delete == true) {
                    $btn = '<a  href="javascript:void(0)" onclick="deleteMe(' .
                        $row->id . ',' . $row->product_id . ')" data-row="' .
                        $row->id . '" class="delete"> <img width="25px" src="images/redcrab_50x50.png" alt=""> </a>';
                    return $btn;
                }
                $btn = '<a  style="text-decoration: none;  filter: grayscale(100) brightness(1.5); pointer-events: none;cursor: default;" class="delete"> <img width="25px" src="images/redcrab_50x50.png" alt=""> </a>';
                return $btn;
            })->rawColumns(['action'])->escapeColumns([])->setTotalRecords($totalRecords)->make(true);

            Log::info("***** listPrdOpenitem: END *****");

            return $view;
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    public function detailProduct(Request $request)
    {
        try {
            $product_details = Product::whereId($request->id)->first();
            /*
            $product_category = PrdCategory::all();
            $product_brand = PrdBrand::all();
            $product_subcategory = PrdSubCategory::all();
            $product_product = PrdPrdCategory::all();
             */
            return view(
                "openitem.product_details",
                compact("product_details")
            );
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function updateCustom(Request $request)
    {
        try {
            $data = [
                "name" =>
                $request->product_name == null ?
                    null : $request->product_name,
            ];

            if (Auth::user() != null) {
                $merchant = DB::table('company')->first();
                MerchantPrdCategory::create([
                    "category_id" => 0,
                    "merchant_id" => $merchant->id,
                ]);
            }

            $prd = Product::where(
                "systemid",
                $request->systemid
            )->update($data);

            return [
                "data" => $prd,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function get_dropDown($OPTION, $KEY)
    {
        $data = [];
        if ($OPTION == "subcat") {
            $data = PrdSubCategory::where("category_id", $KEY)->get();
        } else {
            $data = PrdPrdCategory::where("subcategory_id", $KEY)->get();
        }

        return $data;
    }

    public function delPicture(Request $request)
    {
        try {
            $data = [
                "thumbnail_1" => null,
                "photo_1" => null,
            ];

            $prd = Product::where(
                "systemid",
                $request->systemid
            )->update($data);

            return [
                "data" => $prd,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function savePicture(Request $request)
    {
        try {

            if ($request->file != null) {
                $filename = $this->generatePhotoName(
                    $request->file->getClientOriginalExtension()
                );

                $request->file->move(public_path(self::$IMG_PRODUCT_LINK .
                    $request->product_id . "/"), $filename);

                $path = public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/") . "thumb/";
                if (!file_exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $thumb_path = public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/") .
                    "thumb/" . "thumb_" . $filename;

                File::copy(
                    public_path(self::$IMG_PRODUCT_LINK . $request->product_id . "/" . $filename),
                    $thumb_path
                );

                $img = Image::make($thumb_path);
                $img->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumb_path);

                $data["photo_1"] = $filename;
                $data["thumbnail_1"] = "thumb_" . $filename;
            }

            Product::where("systemid", $request->product_id)->update($data);

            $prd = Product::where("systemid", $request->product_id)->first();

            return [
                "name" => $prd->name,
                "src" => self::$IMG_PRODUCT_LINK .
                    $request->product_id . "/" . $filename,
                "error" => false,
            ];
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function updateOpen(Request $request)
    {
        $data = [
            $request->key => $request->value,
        ];

        $prdOpen = PrdOpenitem::where("id", $request->element)->update($data);

        return [
            "data" => $prdOpen,
            "error" => false,
        ];
    }

    public function save_prd_cost(Request $request)
    {
        try {
            $product = DB::table('product')
                ->where('systemid', $request->product_id)
                ->select('id')->first();

            if (!empty($product)) {
                DB::table('prd_openitem')->where(
                    'product_id',
                    $product->id
                )->update([
                    'cost' => $request->cost_amount,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                Log::error([
                    'Message' => "Product Cost Update failed. Product Not found",
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

    public function deleteOpen(Request $request)
    {
        $prdOpen = PrdOpenitem::where("id", $request->id)->first();
        if (!empty($prdOpen)) {
            Product::find($prdOpen->product_id)->delete();
            PrdOpenitem::find($request->id)->delete();
            $ret = ["data" => $prdOpen, "error" => false];
        } else {
            $ret = ["data" => [], "error" => true];
        }
        return $ret;
    }

    public function generatePhotoName($ext)
    {
        return "p" . time() . "-m" . $this->generateRandomString(14) . "." . $ext;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function cost_distribution_query($receipt_id, $product_id)
    {

        $query = "
             SELECT
                ocr.openitemcost_id,
                ocr.csreceipt_id,
                ocr.qty_taken,
                oc.cost,
                p.name,
                p.id as product_id
            FROM
                product p,
                openitemcost_qtydist ocr
            LEFT JOIN
                openitem_cost oc ON
                oc.id = ocr.openitemcost_id
            LEFT JOIN openitem_productledger opl ON
                opl.id = oc.openitemprodledger_id
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


    public function prdLedger($systemid)
    {
        try {
            $product = Product::where("systemid", $systemid)->first();

            $location = Location::first();
            return view(
                'openitem.openitem_productledger',
                compact('location', 'product')
            );
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }
    public function prdLedger_datatable(Request $request)
    {
        try {

            $start = $request->get("start");
            $rowperpage = $request->get("length");
            $location = Location::first();
            $product = Product::where("systemid", $request->systemid)->first();
            $data = collect();

            $totalRecords = collect(DB::select(DB::raw(
                "
                SELECT
                    SUM(total) as total
                FROM
                (
                    SELECT
                        COUNT(*) as total
                    FROM
                        cstore_receiptproduct crp
                    LEFT JOIN cstore_receipt cr ON cr.id = crp.receipt_id
                    LEFT JOIN cstore_receiptdetails ON
                        cr.id = cstore_receiptdetails.receipt_id
                    WHERE
                        cr.id = crp.receipt_id AND
                        crp.product_id = $product->id
                    UNION
                    SELECT
                        COUNT(*) as total
                    FROM
                        stockreportproduct srp
                    LEFT JOIN  stockreport sp
                    ON  sp.id = srp.stockreport_id
                    WHERE
                        srp.product_id = $product->id
                ) as t
               ;"
            )))->first()->total;

            $query = "
                SELECT
                    cr.id as id,
                    cr.systemid as systemid,
                    cr.status as status,
                    (crp.quantity * -1) as quantity,
                    opl.cost as cost,
                    cr.id as show_receipt_id,
                    null as stockreport_id,
                    crd.id as receiptdetails_id,
                    'Cash Sales' as doc_type,
                    cr.updated_at as last_update,
                    cr.voided_at as voided_at
                FROM
                    cstore_receiptproduct crp
                LEFT JOIN cstore_receipt cr ON cr.id = crp.receipt_id
                LEFT JOIN cstore_receiptdetails crd ON crd.receipt_id = crp.receipt_id
                LEFT JOIN  product p ON
                    p.id = crp.product_id
                LEFT JOIN openitem_productledger opl ON
                    opl.product_systemid = p.systemid AND
                    opl.qty = crp.quantity AND
                    opl.csreceipt_id = crp.receipt_id
                WHERE
                    cr.id = crd.receipt_id AND
                    crp.product_id = $product->id
                UNION
                SELECT
                    sp.id as id,
                    sp.systemid as systemid,
                    sp.status as status,
                    srp.quantity as quantity,
                    opl.cost as cost,
                    null as show_receipt_id,
                    sp.id as stockreport_id,
                    null as receiptdetails_id,
                    sp.type as doc_type,
                    srp.updated_at as last_update,
                    null as voided_at
                FROM
                    stockreportproduct srp
                LEFT JOIN  stockreport sp ON
                    sp.id = srp.stockreport_id
                LEFT JOIN  product p ON
                    p.id = srp.product_id
                LEFT JOIN openitem_productledger opl ON  opl.product_systemid = p.systemid AND
                    srp.quantity = opl.qty AND
                    srp.stockreport_id = opl.stockreport_id
                WHERE
                    sp.id = srp.stockreport_id AND
                    srp.product_id = $product->id
                ORDER BY
                    last_update DESC
                LIMIT $rowperpage
                OFFSET $start

                ;
            ";

            $query3 = "
                SELECT
                    cr.id as id,
                    cr.systemid as systemid,
                    cr.status as status,
                    (crp.quantity * -1) as quantity,
                    opl.cost as cost,
                    cr.id as show_receipt_id,
                    null as stockreport_id,
                    crd.id as receiptdetails_id,
                    'Cash Sales' as doc_type,
                    cr.updated_at as last_update,
                    cr.voided_at as voided_at
                FROM
                    cstore_receiptproduct crp
                LEFT JOIN cstore_receipt cr ON cr.id = crp.receipt_id
                LEFT JOIN cstore_receiptdetails crd ON crd.receipt_id = crp.receipt_id
                LEFT JOIN  product p ON
                    p.id = crp.product_id
                LEFT JOIN openitem_productledger opl ON
                    opl.product_systemid = p.systemid AND
                    opl.qty = crp.quantity AND
                    opl.csreceipt_id = crp.receipt_id
                WHERE
                    cr.id = crd.receipt_id AND
                    crp.product_id = $product->id
                UNION
                SELECT
                    sp.id as id,
                    sp.systemid as systemid,
                    sp.status as status,
                    srp.quantity as quantity,
                    opl.cost as cost,
                    null as show_receipt_id,
                    sp.id as stockreport_id,
                    null as receiptdetails_id,
                    sp.type as doc_type,
                    srp.updated_at as last_update,
                    null as voided_at
                FROM
                    stockreportproduct srp
                LEFT JOIN  stockreport sp ON
                    sp.id = srp.stockreport_id
                LEFT JOIN  product p ON
                    p.id = srp.product_id
                LEFT JOIN openitem_productledger opl ON  opl.product_systemid = p.systemid AND
                    srp.quantity = opl.qty AND
                    srp.stockreport_id = opl.stockreport_id
                WHERE
                    sp.id = srp.stockreport_id AND
                    srp.product_id = $product->id
                ORDER BY
                    last_update DESC
                ;
            ";

            $data =  collect(DB::select(DB::raw($query)));

            if ($request->has('search') && !empty($request->search)) {
                $data = collect(DB::select(DB::raw($query3)));

                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);

                $data = $data->filter(function ($value) use ($search) {

                    Log::debug('value:' . $value->doc_type);

                    if (
                        preg_match("/$search/i", $value->doc_type)  ||
                        preg_match("/$search/i", $value->systemid)
                    ) {

                        return $value;
                    }
                });

                $totalRecords = count($data);
            }

            $result = [];

            foreach ($data as $dt) {
                if ($dt->doc_type == "Cash Sales" && !empty($dt->receiptdetails_id)) {
                    $receipt_id = $dt->show_receipt_id;

                    $mappings = $this->cost_distribution_query($receipt_id, $product->id);

                    $cost_distributions = [];
                    foreach ($mappings as $map) {
                        array_push($cost_distributions, $map);
                    }

                    $dt->cost_distribution = $cost_distributions;
                    $dt->product_id = $product->id;

                    if (sizeof($dt->cost_distribution) > 0) {
                        $costd = $dt->cost_distribution[sizeof($dt->cost_distribution) - 1];
                        $dt->cost = $costd->cost;
                    }
                }

                if (
                    $dt->doc_type == "stockout" || $dt->doc_type == "returned"
                    && !empty($dt->stockreport_id)
                ) {
                    $cost_distributions = [];
                    $reports = DB::select(DB::raw("
                        SELECT
                            opcq.id,
                            opcq.qty_taken,
                            oc.cost
                        FROM
                            openitemcost_qtydist opcq
                        LEFT JOIN
                            openitem_cost oc ON oc.id = opcq.openitemcost_id
                        WHERE
                            opcq.stockreport_id = $dt->stockreport_id ;
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
                }

                array_push($result, $dt);
            }

            // $data =

            return Datatables::of($data)->setOffset($start)->addIndexColumn()->addColumn('product_systemid', function ($row) {
                $display = '';
                if ($row->doc_type == "Cash Sales") {

                    if ($row->status == 'voided') {
                        $display .=  ' <td style="text-align: center; background-color:red;color:white;font-weight:bold;">';
                    } else {
                        $display .= '<td style="text-align: center; background-color:red;color:white;font-weight:bold;">';
                    }
                    $display .= '<a href="#" style="text-decoration: none;" onclick="showReceipt(\'' . $row->show_receipt_id . '\')">' . $row->systemid . ' </a>';
                } elseif ($row->doc_type == "received") {
                    $url = route('receiving_list_id', $row->systemid);

                    $display .= '<a href="javascript:window.open(\'' . $url . '\')"
                        style="text-decoration: none;">' . $row->systemid . '</a>';
                } elseif ($row->doc_type == "returned") {
                    $url = route('returning.stock_report', $row->systemid);
                    $display .= '<td style="text-align: center;">
                            <a href="javascript:window.open(\'' . $url . '\')"
                        style="text-decoration: none;">' . $row->systemid . '</a></td>';
                } else {
                    $url = route('stocking.stock_report', $row->systemid);
                    $display .= '<td style="text-align: center;">
                        <a href="javascript:window.open(\'' . $url . '\')"
                        style="text-decoration: none;">' . $row->systemid . '</a></td>';
                }

                return  $display;
            })->addColumn('type', function ($row) {
                if ($row->doc_type == 'stockin') {
                    $type =   "Stock In";
                } elseif ($row->doc_type == 'stockout') {
                    $type =  "Stock Out";
                } else {
                    $type = ucwords($row->doc_type);
                }
                return $type;
            })->addColumn('last_update', function ($row) {
                if ($row->status == 'voided') {
                    $date = date('dMy H:i:s', strtotime($row->voided_at ?? ''));
                } else {
                    $date = date('dMy H:i:s', strtotime($row->last_update ?? ''));
                }
                return $date;
            })->addColumn('location', function () use ($location) {
                return $location->name;
            })->addColumn('cost', function ($row) {
                if (property_exists($row, 'cost_distribution')) {
                    $cost = '';
                    if (sizeof($row->cost_distribution) > 0) {
                        if ($row->doc_type == "Cash Sales") {

                            $cost = ' <a href="#" onclick="showCostDist(' . $row->show_receipt_id . ', ' . $row->product_id . ')">' . number_format(($row->cost / 100), 2) . '</a>';
                        } elseif ($row->doc_type == 'stockout' || $row->doc_type == 'returned') {

                            $cost =  '<a href="#" onclick="showStockOutCostDist(\'' . $row->stockreport_id .
                                '\')">' . $row->cost . '</a>';
                        }
                    } else {
                        $cost =  number_format(($row->cost / 100), 2);
                    }
                } else {
                    $cost = number_format(($row->cost / 100), 2);
                }

                return $cost;
            })->addColumn('quantity', function ($row) {
                if ($row->status == 'voided') {
                    return 0;
                } else {
                    return $row->quantity;
                }
            })->escapeColumns([])->setTotalRecords($totalRecords)->make(true);
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }
    public function add_cost_to_prd_ledger($data, $prd_id)
    {

        $prd_info = DB::table('product')->whereId($prd_id)->first();

        $new_data = collect();

        foreach ($data as $prd) {

            if ($prd->doc_type == "Cash Sales") {
                $cost = DB::table('openitem_productledger')->where('product_systemid', $prd_info->systemid)->where('qty', $prd->quantity)->where('csreceipt_id', $prd->show_receipt_id)->orderBy('created_at', 'desc')->first();
            } else {
                $cost = DB::table('openitem_productledger')->where('product_systemid', $prd_info->systemid)->where('qty', $prd->quantity)->where('stockreport_id', $prd->id)->orderBy('created_at', 'desc')->first();
            }

            if (!empty($cost)) {
                $prd->cost = $cost->cost;
            } else {
                $prd->cost = 0;
            }

            $new_data->push($prd);
            // $this->reflect_autostock_openitemproduct_cost();
        }

        return $new_data;
    }

    public function openitemStockout()
    {
        try {

            $location = DB::table('location')->first();
            return view('openitem.openitem_stockout', compact('location'));
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function openitemStockin()
    {
        try {

            $location = DB::table('location')->first();
            return view('openitem.openitem_stockin', compact('location'));
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }
    }
    public function openitem_stock_update(Request $request)
    {
        Log::debug('****OpenItem Stock Update()*****');
        try {
            $user_id = \Auth::user()->id;
            $table_data = $request->get('table_data');
            $stock_type = $request->get('stock_type');
            $stock_system = new SystemID("stockreport");

            $company = Company::first();
            $location = Location::first();

            foreach ($table_data as $key => $value) {

                //if qty zero
                if ($value['qty'] == 0) {
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

                // Openitem Product
                $openitemproduct = DB::table('prd_openitem')->where(['product_id' => $value['product_id']])
                    ->first();

                if ($openitemproduct) { // modify existing openitem product

                    $openitemproduct = DB::table('prd_openitem')->where(['product_id' => $value['product_id']])->increment('qty', $curr_qty);
                } else {
                    DB::table('prd_openitem')->insert([
                            "product_id" => $value['product_id'],
                            "qty" => $curr_qty,
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

                $prd = DB::table('product')->whereId($value['product_id'])->first();

                if ($stock_type == "IN") {

                    $latest_cost = DB::table('openitem_productledger')->where('product_systemid', $prd->systemid)->whereIn('type', ['stockin', 'received'])->whereNotNull('cost')->orderBy('created_at', 'desc')->first();

                    $cost = empty($latest_cost) ? 0 : $latest_cost->cost;

                    $openitemprodid = DB::table('openitem_productledger')->insertGetId([
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
                    // this function updates csreceipt for negative sales
                    // $this->process_negative_sales($openitemprodid, $curr_qty, $prd, $cost);
                    if ($openitemprodid) {

                        $last_record = DB::select(DB::raw("
                            SELECT
                                ol. *
                            FROM
                                openitem_productledger ol,
                                openitem_cost oc
                            WHERE
                                ol.type = 'cash_sales'  AND
                                ol.product_systemid  = '$prd->systemid' AND
                                oc.openitemprodledger_id = ol.id AND
                                oc.balance < 0
                            LIMIT 1
                            ;
                        "));

                        $last_cashsales_record = collect($last_record)->first();

                        if ($last_cashsales_record) {

                            $cost_info = DB::table('openitem_cost')->where('openitemprodledger_id', $last_cashsales_record->id)
                                ->first();

                            if (!empty($cost_info)) {

                                if ($cost_info->qty_out * -1 > $curr_qty) {

                                    DB::table('openitem_cost')->where('openitemprodledger_id', $last_cashsales_record->id)->update([
                                            "qty_out" => $cost_info->qty_out + $curr_qty,
                                            "balance" => $cost_info->balance + $curr_qty,
                                            "updated_at" => date('Y-m-d H:i:s'),
                                        ]);

                                    $cost_id = DB::table('openitem_cost')->insertGetId([
                                            "openitemprodledger_id" => $openitemprodid,
                                            "cost" => $cost,
                                            "qty_in" => $curr_qty,
                                            "qty_out" => $curr_qty * -1,
                                            "balance" =>  $curr_qty - $curr_qty,
                                            "deleted_at" => null,
                                            "created_at" => date('Y-m-d H:i:s'),
                                            "updated_at" => date('Y-m-d H:i:s'),
                                        ]);

                                    $this->record_openitemcost_csreceipt(
                                        $last_cashsales_record->csreceipt_id,
                                        $cost_id,
                                        $curr_qty,
                                        null
                                    );
                                } elseif ($curr_qty >= $cost_info->qty_out * -1) {

                                    DB::table('openitem_cost')->where('openitemprodledger_id', $last_cashsales_record->id)->update([
                                            "qty_out" => 0,
                                            "balance" => 0,
                                            "updated_at" => date('Y-m-d H:i:s'),
                                        ]);

                                    $cost_id = DB::table('openitem_cost')->insertGetId([
                                            "openitemprodledger_id" => $openitemprodid,
                                            "cost" => $cost,
                                            "qty_in" => $curr_qty,
                                            "qty_out" => $curr_qty - ($curr_qty - ($cost_info->qty_out)),
                                            "balance" => ($curr_qty - ($cost_info->qty_out * -1)),
                                            "deleted_at" => null,
                                            "created_at" => date('Y-m-d H:i:s'),
                                            "updated_at" => date('Y-m-d H:i:s'),
                                        ]);

                                    $this->record_openitemcost_csreceipt(
                                        $last_cashsales_record->csreceipt_id,
                                        $cost_id,
                                        $cost_info->qty_out * -1,
                                        null
                                    );
                                } else {
                                    $cost_id = DB::table('openitem_cost')->insertGetId([
                                        "openitemprodledger_id" => $openitemprodid,
                                        "cost" => $cost,
                                        "qty_in" => $curr_qty,
                                        "qty_out" => 0,
                                        "balance" =>  $curr_qty,
                                        "deleted_at" => null,
                                        "created_at" => date('Y-m-d H:i:s'),
                                        "updated_at" => date('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        } else {

                            $cost_id = DB::table('openitem_cost')->insertGetId([
                                "openitemprodledger_id" => $openitemprodid,
                                "cost" => $cost,
                                "qty_in" => $curr_qty,
                                "qty_out" => 0,
                                "balance" =>  $curr_qty,
                                "deleted_at" => null,
                                "created_at" => date('Y-m-d H:i:s'),
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                } else if ($stock_type == "OUT") {

                    $earliest_cost = DB::select(DB::raw("
                            SELECT
                                op.cost
                            FROM
                                openitem_cost op,
                                openitem_productledger opl
                            WHERE
                                opl.product_systemid = '$prd->systemid' AND
                                op.openitemprodledger_id = opl.id AND
                                op.balance > 0
                            ORDER BY  op.cost ASC
                            LIMIT 1
                            ;
                     "));

                    $cost = empty($earliest_cost) ? 0 : $earliest_cost[0]->cost;

                    $openitemprodid = DB::table('openitem_productledger')->insertGetId([
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

                    $this->process_openitem_stockout(
                        $prd->systemid,
                        $curr_qty,
                        $stockreport_id,
                        $openitemprodid
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


    public function process_openitem_stockout(
        $systemid,
        $curr_qty,
        $stockreport_id,
        $newLedgerId
    ) {

        try {
            // Get oldest non zero balance
            $oldest_bal = DB::table('openitem_cost')->join(
                'openitem_productledger',
                'openitem_productledger.id',
                'openitem_cost.openitemprodledger_id'
            )->select(
                'openitem_productledger.id as ledger_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.type as doc_type',
                'openitem_productledger.cost as lcost',
                'openitem_cost.cost as cost',
                'openitem_cost.id as id',
                'openitem_cost.qty_in as qty_in',
                'openitem_cost.qty_out as qty_out',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at'
            )->where("openitem_productledger.product_systemid", $systemid)->where('openitem_cost.balance', '>', 0)->orderBy('openitem_cost.created_at', 'asc')->first();

            if (!empty($oldest_bal)) {
                $compare = $curr_qty;
                if ($oldest_bal->balance >= ($compare * -1)) {

                    DB::table('openitem_cost')->whereId($oldest_bal->id)->update([
                            "qty_out" => $curr_qty + $oldest_bal->qty_out,
                            "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);
                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    DB::table('openitem_productledger')->whereId($newLedgerId)->update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $this->record_openitemcost_csreceipt(
                        null,
                        $oldest_bal->id,
                        $qty,
                        $stockreport_id
                    );
                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('openitem_cost')->whereId($oldest_bal->id)->update([
                            "qty_out" => $oldest_bal->qty_in * -1,
                            "balance" => 0,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('openitem_productledger')->whereId($newLedgerId)->update([
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $oldest_bal->balance;
                    $this->record_openitemcost_csreceipt(
                        null,
                        $oldest_bal->id,
                        $qty,
                        $stockreport_id
                    );

                    $this->process_openitem_stockout(
                        $systemid,
                        $carry_over_bal,
                        $stockreport_id,
                        $newLedgerId
                    );
                }
            } else {
                DB::table('openitem_cost')->insert([
                        "openitemprodledger_id" => $oldest_bal->ledger_id,
                        "cost" => $oldest_bal->lcost,
                        "qty_in" => 0,
                        "qty_out" => $curr_qty * -1,
                        "balance" => ($curr_qty * -1) + $curr_qty->quantity,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                DB::table('openitem_productledger')->whereId($newLedgerId)->update([
                        "cost" => $oldest_bal->cost,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                $this->record_openitemcost_csreceipt(
                    null,
                    $oldest_bal->id,
                    $curr_qty * -1,
                    $stockreport_id
                );
            }

            // $this->create_receiptcost();
        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            //abort(500);
        }
    }

    // Stockout for negative sales --when you perform a cash sales --for negavite sales

    public function process_openitem_stockout_negative_sales(
        $systemid,
        $curr_qty,
        $csreceipt_id,
        $ledger_id
    ) {

        Log::debug("***** process_openitem_stockout_negative_sales: START *****");
        try {
            // Get oldest non zero balance
            $oldest_bal = DB::table('openitem_cost')->join(
                    'openitem_productledger',
                    'openitem_productledger.id',
                    'openitem_cost.openitemprodledger_id'
                )->select(
                    'openitem_productledger.id as ledger_id',
                    'openitem_productledger.stockreport_id as sr_id',
                    'openitem_productledger.type as doc_type',
                    'openitem_productledger.cost as lcost',
                    'openitem_cost.cost as cost',
                    'openitem_cost.id as id',
                    'openitem_cost.qty_in as qty_in',
                    'openitem_cost.qty_out as qty_out',
                    'openitem_cost.balance as balance',
                    'openitem_cost.created_at as created_at',
                    'openitem_cost.updated_at as updated_at'
                )->where("openitem_productledger.product_systemid", $systemid)->
                // this condition is an issue.
                whereRaw('openitem_cost.qty_in < openitem_cost.qty_out * -1')->orderBy('openitem_cost.created_at', 'desc')->first();

            $sec_oldest_bal = DB::table('openitem_cost')->join(
                'openitem_productledger',
                'openitem_productledger.id',
                'openitem_cost.openitemprodledger_id'
            )->select(
                'openitem_productledger.id as ledger_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.type as doc_type',
                'openitem_productledger.cost as lcost',
                'openitem_cost.cost as cost',
                'openitem_cost.id as id',
                'openitem_cost.qty_in as qty_in',
                'openitem_cost.qty_out as qty_out',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at'
            )->where("openitem_productledger.product_systemid", $systemid)->
                // this condition is an issue.
                whereRaw('openitem_cost.balance > 0')->orderBy('openitem_cost.created_at', 'asc')->first();


            if (!empty($oldest_bal)) {
                Log::debug("process_openitem_stockout_negative_sales: oldest=" .
                    json_encode($oldest_bal));
                $compare = $curr_qty;

                $data_cost = DB::table('openitem_cost')->where('openitemprodledger_id', $oldest_bal->ledger_id)->orderBy('created_at', 'desc')->first();

                if ($data_cost->qty_in < $data_cost->qty_out * -1) {

                    Log::info("process_openitem_stockout_negative_sales: datacost=" .
                        json_encode($data_cost));

                    DB::table('openitem_cost')->whereId($data_cost->id)->update([
                            "cost" => $oldest_bal->cost,
                            "qty_out" => $curr_qty + $oldest_bal->qty_out,
                            "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('openitem_productledger')->whereId($ledger_id)->update([
                            // "qty" => $curr_qty + $oldest_bal->qty_out,
                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }
                    $this->record_openitemcost_csreceipt(
                        $csreceipt_id,
                        $oldest_bal->id,
                        $qty,
                        null
                    );
                }
            } elseif (!empty($sec_oldest_bal)) {

                Log::debug("process_openitem_stockout_negative_sales: oldest=" .
                    json_encode($sec_oldest_bal));

                $compare = $curr_qty;
                if ($sec_oldest_bal->balance >= ($compare * -1)) {

                    DB::table('openitem_cost')->whereId($sec_oldest_bal->id)->update([
                        "cost" => $sec_oldest_bal->cost,
                        "qty_out" => $curr_qty + $sec_oldest_bal->qty_out,
                        "balance" => $sec_oldest_bal->qty_in +
                            ($curr_qty + $sec_oldest_bal->qty_out),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    DB::table('openitem_productledger')->whereId($ledger_id)->update([
                        // "qty" => $curr_qty + $sec_oldest_bal->qty_out,
                        "cost" => $sec_oldest_bal->cost,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $qty = $curr_qty + $sec_oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    $this->record_openitemcost_csreceipt(
                        $csreceipt_id,
                        $sec_oldest_bal->id,
                        $qty,
                        null
                    );
                } else {

                    $carry_over_bal = $curr_qty + $sec_oldest_bal->balance;

                    DB::table('openitem_cost')->whereId($sec_oldest_bal->id)->update([
                        "cost" => $sec_oldest_bal->cost,
                        "qty_out" => $sec_oldest_bal->qty_in * -1,
                        "balance" => 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    DB::table('openitem_productledger')->whereId($ledger_id)->update([
                        "cost" => $sec_oldest_bal->cost,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $qty = $sec_oldest_bal->balance;

                    $this->record_openitemcost_csreceipt(
                        $csreceipt_id,
                        $sec_oldest_bal->id,
                        $qty,
                        null
                    );

                    $this->process_openitem_stockout_negative_sales(
                        $systemid,
                        $carry_over_bal,
                        $csreceipt_id,
                        $ledger_id
                    );
                }
            } else {


                $latest_cost = DB::select(DB::raw("
                    SELECT
                        op.cost
                    FROM
                        openitem_cost op,
                        openitem_productledger opl
                    WHERE
                        opl.product_systemid = '$systemid' AND
                        op.openitemprodledger_id = opl.id AND
                        op.balance > 0
                    ORDER BY  op.cost DESC
                    LIMIT 1
                    ;
                "));

                $cost_id = DB::table('openitem_cost')->insertGetId([
                    "openitemprodledger_id" => $ledger_id,
                    "cost" => $latest_cost[0]->cost ?? 0,
                    "qty_in" => 0,
                    "qty_out" => $curr_qty,
                    "balance" => $curr_qty,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);  // end of insert openitem_cost

                DB::table('openitem_productledger')->whereId($ledger_id)->update([
                        "cost" => $latest_cost[0]->cost ?? 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                $this->record_openitemcost_csreceipt(
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
            //abort(50
        }
    }



    public function process_openitem_returning(
        $systemid,
        $curr_qty,
        $stockreport_id,
        $current_ledger_id
    ) {

        try {
            // Get oldest non zero balance
            $oldest_bal = DB::table('openitem_cost')->join(
                    'openitem_productledger',
                    'openitem_productledger.id',
                    'openitem_cost.openitemprodledger_id'
                )->select(
                    'openitem_productledger.id as ledger_id',
                    'openitem_productledger.stockreport_id as sr_id',
                    'openitem_productledger.type as doc_type',
                    'openitem_cost.cost as cost',
                    'openitem_cost.id as id',
                    'openitem_cost.qty_in as qty_in',
                    'openitem_cost.qty_out as qty_out',
                    'openitem_cost.balance as balance',
                    'openitem_cost.created_at as created_at',
                    'openitem_cost.updated_at as updated_at'
                )->where("openitem_productledger.product_systemid", $systemid)->where('openitem_cost.balance', '>', 0)->orderBy('openitem_cost.created_at', 'asc')->first();

            $cost = empty($oldest_bal) ? 0 : $oldest_bal->cost;

            if (!empty($oldest_bal)) {
                $compare = $curr_qty;
                if ($oldest_bal->balance >= ($compare * -1)) {
                    DB::table('openitem_cost')->whereId($oldest_bal->id)->update([
                            "qty_out" => $curr_qty + $oldest_bal->qty_out,
                            "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);

                    DB::table('openitem_productledger')->whereId($current_ledger_id)->update([

                            "cost" => $oldest_bal->cost,
                            "updated_at" => date('Y-m-d H:i:s'),
                        ]);
                    #Note: curr_qty + qty_out => surplus
                    $qty = $curr_qty + $oldest_bal->qty_out;

                    if ($qty < 0) {
                        $qty = $curr_qty * -1;
                    }

                    $this->record_openitemcost_csreceipt(
                        null,
                        $oldest_bal->id,
                        $qty,
                        $stockreport_id
                    );
                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('openitem_cost')->whereId($oldest_bal->id)->update([
                        "qty_out" => $oldest_bal->qty_in * -1,
                        "balance" => 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    DB::table('openitem_productledger')->whereId($current_ledger_id)->update([
                        "cost" => $oldest_bal->cost,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    $qty = $oldest_bal->balance;
                    $this->record_openitemcost_csreceipt(
                        null,
                        $oldest_bal->id,
                        $qty,
                        $stockreport_id
                    );

                    $this->process_openitem_returning(
                        $systemid,
                        $carry_over_bal,
                        $stockreport_id,
                        $current_ledger_id
                    );
                }
            } else {
                DB::table('openitem_cost')->insert([
                    "openitemprodledger_id" => $current_ledger_id,
                    "cost" => $cost,
                    "qty_in" => 0,
                    "qty_out" => $curr_qty * -1,
                    "balance" => ($curr_qty * -1) + $curr_qty->quantity,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
            // $this->create_receiptcost();



        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            //abort(500);
        }
    }


    // new
    // new
    public function record_openitemcost_csreceipt(
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

        DB::table('openitemcost_qtydist')->insert([
            'qty_taken' => $qty_taken,
            'openitemcost_id' => $locprod_id,
            'csreceipt_id' => $csreceipt_id,
            'stockreport_id' => $stockreport_id,
        ]);
    }

    public function cost_distribution_stockout($stockreport_id, Request $request)
    {
        $cost_distributions = [];
        $reports = DB::select(DB::raw("
            SELECT
                opcq.id,
                opcq.qty_taken,
                oc.cost
            FROM
                openitemcost_qtydist opcq
            LEFT JOIN
                openitem_cost oc ON oc.id = opcq.openitemcost_id
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

    public function stockOutList(Request $request)
    {
        try {

            $start = $request->start;
            $rowperpage = $request->get("length");
            $totalRecords = DB::table('product')->join('prd_openitem', 'prd_openitem.product_id', 'product.id')->whereNotNull('product.name')->where('prd_openitem.qty', '>', '0')->select("product.*", "prd_openitem.price as recommended_price")->count();

            $product_data_open_item = DB::table('product')->join('prd_openitem', 'prd_openitem.product_id', 'product.id')->whereNotNull('product.name')->where('prd_openitem.qty', '>', '0')->select("product.*", "prd_openitem.price as recommended_price")->orderBy('prd_openitem.created_at', 'desc')->skip($start)->take($rowperpage)->get();

            $product_data_open_item = $product_data_open_item->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product->id) > 0;
            });

            if ($request->has('search') && !empty($request->search)) {
                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);

                $product_data_open_item = DB::table('product')->join('prd_openitem', 'prd_openitem.product_id', 'product.id')->whereNotNull('product.name')->select(
                        "product.*",
                        "prd_openitem.price as recommended_price"
                    )->orderBy('prd_openitem.created_at', 'desc')->
                    get()->filter(function ($value) use ($search) {

                        Log::debug('value:' . $value->name);

                        if (
                            preg_match("/$search/i", $value->name)     ||
                            preg_match("/$search/i", $value->systemid)
                        ) {

                            return $value;
                        }
                    });

                $product_data_open_item = $product_data_open_item->filter(function ($product) {
                    return app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product->id) > 0;
                });

                $totalRecords = count($product_data_open_item);
            }


            return Datatables::of($product_data_open_item)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;
            })->addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                    $data->systemid . '/thumb/' .
                    $data->thumbnail_1;

                if (
                    !empty($data->thumbnail_1) &&
                    file_exists(public_path() . $img_src)
                ) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
						style=' width: 25px; height: 25px;
						display: inline-block;margin-right: 8px;
						object-fit:contain;'>";
                } else {
                    $img = '';
                }

                return $img . $data->name;
            })->addColumn('product_qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product_id);
                return <<<EOD
						<span id="qty_$product_id">$qty</span>
EOD;
            })->addColumn('action', function ($data) {
                $product_id = $data->id;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->rawColumns(['action'])->escapeColumns([])->setTotalRecords($totalRecords)->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function stockInList(Request $request)
    {
        try {

            $start = $request->get("start");
            $rowperpage = $request->get("length");
            $totalRecords =  DB::table('product')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                whereNotNull([
                'product.name',
                //'product.thumbnail_1'
            ])->select("product.*", "prd_openitem.price as recommended_price")->
            count();


            $product_data_open_item = DB::table('product')->
                join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                whereNotNull('product.name')->select("product.*",
                     "prd_openitem.price as recommended_price")->
                orderBy('prd_openitem.created_at', 'desc')->
                skip($start)->take($rowperpage)->get();

            if ($request->has('search') && !empty($request->search)) {
                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);

                $product_data_open_item = DB::table('product')->
                    join('prd_openitem', 'prd_openitem.product_id', 'product.id')->
                    whereNotNull('product.name')->
                    select(
                        "product.*",
                        "prd_openitem.price as recommended_price"
                    )->
                    orderBy('prd_openitem.created_at', 'desc')->
                    get()->filter(function ($value) use ($search) {

                    Log::debug('value:' . $value->name);

                    if (
                        preg_match("/$search/i", $value->name)     ||
                        preg_match("/$search/i", $value->systemid)
                    ) {

                        return $value;
                    }
                });

                $totalRecords = count($product_data_open_item);
            }


            return Datatables::of($product_data_open_item)->
                setOffset($start)->addIndexColumn()->
                addColumn('product_systemid', function ($data) {
                return $data->systemid;
            })->addColumn('product_name', function ($data) {
                $img_src = '/images/product/' .
                    $data->systemid . '/thumb/' .
                    $data->thumbnail_1;

                if (
                    !empty($data->thumbnail_1) &&
                    file_exists(public_path() . $img_src)
                ) {

                    $img = "<img src='$img_src' data-field='inven_pro_name'
                            style=' width: 25px; height: 25px;
                            display: inline-block;margin-right: 8px;
                            object-fit:contain;'>";
                } else {
                    $img = '';
                }

                return $img . $data->name;
            })->addColumn('product_qty', function ($data) {
                $product_id = $data->id;
                $qty = app("App\Http\Controllers\CentralStockMgmtController")->qtyAvailable($product_id);
                return <<<EOD
                            <span id="qty_$product_id">$qty</span>
    EOD;
            })->addColumn('action', function ($data) {
                $product_id = $data->id;
                return view('fuel_stockmgmt.inven_qty', compact('product_id'));
            })->rawColumns(['action'])->escapeColumns([])->setTotalRecords($totalRecords)->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function product_has_qty(Request $request)
    {
        try {
            $product = DB::table('prd_openitem')->where('product_id', $request->product_id)->first();

            if (!empty($product) && $product->qty > 0) {
                return response()->json([
                    'has_qty' => true,
                ]);
            }
            return response()->json([
                'has_qty' => false,
            ]);
        } catch (\Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
        }
    }

    public function product_barcode(Request $request)
    {
        $system_id = $request->route('systemid');

        $product = DB::table('product')->join('prd_openitem', 'prd_openitem.product_id', 'product.id')->where('product.systemid', $system_id)->first();

        $product_id = $product->id;
        return view(
            'openitem.openitem_barcode',
            compact('system_id', 'product', 'product_id')
        );
    }

    public function delete_product_barcode(Request $request)
    {

        $prd_barcode = DB::table('productbarcode')->where("barcode", $request->barcode)->whereNull('deleted_at')->first();

        if (!empty($prd_barcode)) {
            DB::table('productbarcode')->where('barcode', $request->barcode)->whereNull('deleted_at')->update([
                'selected' => 0,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            $ret = ["data" => $prd_barcode, "error" => false];
        } else {
            $ret = ["data" => [], "error" => true];
        }
        return $ret;
    }

    public function show_barcode_table(Request $request)
    {
        try {
            $product = DB::table('prd_openitem')->join('product', 'prd_openitem.product_id', 'product.id')->where('prd_openitem.id', $request->prd_id)->first();

            $barcode = [];

            $default = collect();

            $default['systemid'] = $product->systemid;

            $barcode[] = $default;

            $productbarcode = DB::table('productbarcode')->where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'desc')->get();

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

            return Datatables::of($productbarcode)->addIndexColumn()->addColumn('product_barcode', function ($memberList) {

                Log::debug('product_barcode_datatable: memberList=' .
                    json_encode($memberList));

                /*
                Log::debug('product_barcode_datatable: systemid='.
                $memberList->systemid.', barcode='.
                $memberList->barcode);
                 */

                $code = new DNS1D();
                $code = $code->getBarcodePNG(trim($memberList->barcode), "C128");
                $bc = $memberList->barcode;

                return <<<EOD
                    <div style="display:flex;justify-content: flex-start;">
                        <div style="display:flex;flex-direction: column;justify-content:
                            center;align-items: center;">
                            <img src="data:image/png;base64,$code" style="display:block;"
                                    alt="barcode" class="" width="200px" height="70px "/>
                            $bc
                        </div>
                    </div>
      EOD;
            })->addColumn('select', function ($row) {
                // This is is for the "Display" column
                if (isset($row->merchantproduct_id)) {
                    if ($row->selected) {
                        return '
                    <label class="containerx" style="margin-left:20px;padding-bottom:10px;">
                        <input type="checkbox" checked="checked" onchange="select_barcode(' . $row->id . ', ' . $row->product_id . ')">
                        <span class="checkmark"></span>
                    </label>
                    ';
                    } else {
                        return '
                    <label class="containerx" style="margin-left:20px;padding-bottom:10px;">
                        <input type="checkbox" onchange="select_barcode(' . $row->id . ', ' . $row->product_id . ')">
                        <span class="checkmark"></span>
                    </label>
                    ';
                    }
                }
            })->addColumn('action', function ($row) {
                $transaction = DB::table('cstore_receiptproduct')->where('product_id', $row->product_id)->first();

                if (empty($transaction)) {
                    $row->can_delete = true;
                } else {
                    $row->can_delete = false;
                }

                $prd_sysid = DB::table('product')->whereId($row->product_id)->first();

                if ($row->barcode == $prd_sysid->systemid) {
                    $btn = '<a  style="text-decoration: none;
                    filter: grayscale(100) brightness(1.5);
                    pointer-events: none;cursor: default;" ' .
                        ' class="delete"> ' .
                        '</a>';
                    return $btn;
                } else if ($row->can_delete == true) {
                    $btn = '<a  href="javascript:void(0)" id="bc_' .
                        $row->barcode . '"' .
                        ' onclick="delete_barcode(this.id)" data-barcode="' .
                        $row->barcode . '" class="delete"> ' .
                        ' <img width="25px" src="/images/redcrab_50x50.png" alt=""> ' .
                        '</a>';
                    return $btn;
                } else {
                    $btn = '<a  style="text-decoration: none;
                        filter: grayscale(100) brightness(1.5); pointer-events:
                        none;cursor: default;" ' .
                        ' class="delete"> ' .
                        '<img width="25px" src="/images/redcrab_50x50.png" alt=""> ' .
                        '</a>';
                    return $btn;
                }
            })->rawColumns(['action'])->escapeColumns([])->make(true);
        } catch (Exception $e) {
            Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line No" => $e->getLine(),
            ]);
            abort(404);
        }
    }

    public function select_barcode_record(Request $request)
    {
        $product_id = $request->product_id;
        $barcode_id = $request->barcode_id;

        DB::table('productbarcode')
            ->where('selected', 1)
            ->where('product_id', $product_id)
            ->update(['selected' => 0]);

        DB::table('productbarcode')
            ->where('id', $barcode_id)
            ->where('product_id', $product_id)
            ->update(['selected' => 1]);

        return response(['status' => 1], 200);
    }

    public function save_barcode(Request $request)
    {
        $barcodes = trim($request->barcodes);
        $barcodes = str_replace("\n", ";", $barcodes);
        $barcodes = str_replace(",", ";", $barcodes);
        $parts = explode(';', $barcodes);

        Log::debug('parts=' . json_encode($parts));

        $company = Company::first();
        $merchant_id = $company->id;
        $duplicate_barcodes = "";

        $prd = DB::table('prd_openitem')->whereId($request->id)->first();

        $merchant_product = DB::table('merchantproduct')->select('id')->where('merchant_id', '=', $merchant_id)->where('product_id', '=', $prd->product_id)->first();

        Log::debug('merchant_product=' . json_encode($merchant_product));

        $is_duplicate = false;
        foreach ($parts as $part) {
            $part = trim($part);

            Log::debug('merchant_id=' . $merchant_id);
            Log::debug('product_id =' . $request->id);
            Log::debug('barcode    =' . $part);

            $count = DB::table('merchantproduct as mp')->join(
                'productbarcode as pb',
                'mp.id',
                '=',
                'pb.merchantproduct_id'
            )->select('pb.barcode')->where('mp.merchant_id', '=', $merchant_id)->
                // where('mp.product_id', '=', $request->id)->
                where('pb.barcode', '=', $part)->whereNull('pb.deleted_at')->count();

            Log::debug('count=' . json_encode($count));

            if (empty($count)) {
                DB::table('productbarcode')
                    ->where('product_id', $prd->product_id)
                    ->where('selected', 1)
                    ->update(['selected' => 0]);
                DB::table('productbarcode')->insert([
                    "merchantproduct_id" => $merchant_product->id,
                    "product_id" => $prd->product_id,
                    "selected" => 1,
                    "barcode" => $part,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $is_duplicate = true;
                $duplicate_barcodes .= $part . "<br>";
            }
        }

        if ($is_duplicate) {
            $msg = "Duplicated barcodes found:<br>" .
                $duplicate_barcodes;

            // $html = view('openitem.dialog', compact('msg'))->render();
            return $msg;
        } else {
            return 0;
        }
    }

    /**
     * Create Barcode from user input range
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create_barcode_from_input_range(Request $request)
    {
        $this->barcodeGeneratorValidator($request);

        $barcode_from = (int) $request->get('barcode_from');
        $barcode_to = (int) $request->get('barcode_to');
        $product_id = $request->get('product_id');
        $barcode_notes = $request->get('barcode_notes');
        $merchant_id = (new UserData())->company_id();

        $merchant_product = DB::table('merchantproduct')->select('id')->where('merchant_id', '=', $merchant_id)->where('product_id', '=', $product_id)->first();

        if ($barcodes = $this->checkIfBarcodesExistWithRange(
            $product_id,
            $merchant_product->id,
            $barcode_from,
            $barcode_to
        )) {

            $unique_barcodes = array_unique($barcodes);
            sort($unique_barcodes);
            if (count($unique_barcodes) > 10) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'System detected clashing barcodes already in existence:
                     <div class="text-left"><br/>'
                        . implode('<br/>', array_slice($unique_barcodes, 0, 10)) .
                        '<br>Another ' . (count($unique_barcodes) - 10) .
                        ' barcodes existed.</div>',
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'System detected clashing barcodes already in existence:
                     <div class="text-left"><br/>'
                        . implode('<br/>', array_slice($unique_barcodes, 0, 10)) .
                        '</div>',
                ]);
            }
        }

        $this->createMultipleBarcodesWithRanges($barcode_from, $barcode_to, [
            'barcode_type' => 'C128',
            'product_id' => $product_id,
            'merchantproduct_id' => $merchant_product->id,
            'notes' => $barcode_notes,
        ]);

        $this->update_barcode_oceania($product_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Barcode generated successfully',
        ]);
    }

    public function generate_bar_code(Request $request)
    {
        try {
            $bm_id = $request->bm_id;
            $changed = false;
            //bmatrixbarcode
            $bmatrix = DB::table('bmatrix')->where('id', $bm_id)->whereNull('deleted_at')->first();

            if (empty($bmatrix)) {
                throw new \Exception("Invalid barcode matrix");
            }

            $attributes = DB::table('bmatrixattrib')->where('bmatrix_id', $bm_id)->whereNull('deleted_at')->whereNotNull('name')->get();

            $bmatrixcolor = DB::table('bmatrixcolor')->where('bmatrix_id', $bm_id)->where([['color_id', '!=', 0]])->whereNull('deleted_at')->get();

            $attrib_items = DB::table('bmatrixattribitem')->whereIn('bmatrixattrib_id', $attributes->pluck('id'))->whereNull('deleted_at')->whereNotNull('name')->get();

            $color_items = DB::table('color')->whereIn('id', $bmatrixcolor->pluck('color_id'))->
                //    whereNotIn('hex_code', ["#000","#000000"])->
                whereNull('deleted_at')->pluck('id');
            $array = [];

            foreach ($attributes as $a) {
                $array[] = DB::table('bmatrixattribitem')->where('bmatrixattrib_id', $a->id)->whereNull('deleted_at')->pluck('id');
            }
            $combined_attr = $this->combos($array);
            $z_array = [];

            if (count($color_items) > 0) {
                foreach ($color_items as $c) {
                    if (count($combined_attr) > 0) {
                        foreach ($combined_attr as $a) {
                            $a['color'] = $c;
                            $z_array[] = $a;
                        }
                    } else {
                        $z_array = array(['color' => $c]);
                    }
                }
            } else {

                foreach ($combined_attr as $a) {
                    $a['color'] = 0;
                    $z_array[] = $a;
                }
            }

            $is_exist = DB::table('bmatrixbarcode')
                ->where('bmatrix_id', $bmatrix->id)
                ->whereNull('deleted_at')->get();

            if ($is_exist->count() > 0) {

                DB::table('bmatrixbarcode')
                    ->where('bmatrix_id', $bmatrix->id)
                    ->delete();
            }

            foreach ($z_array as $barcode) {
                $bar = [];
                foreach ($barcode as $key => $id) {
                    if ($key === 'color') {
                        if ($id != 0) {
                            $bmatrixcolor_id = DB::table('bmatrixcolor')->where('color_id', $id)->first()->id;

                            $bar[] = ["color_id" => $bmatrixcolor_id];
                        } else {
                            $bar[] = ["color_id" => 0];
                        }
                    } else {
                        $bmatrixattribitem = DB::table('bmatrixattribitem')->where('id', $id)->first();

                        $bmatrixattrib = DB::table('bmatrixattrib')->where('id', $bmatrixattribitem->bmatrixattrib_id)->first();

                        $bar[] = [$bmatrixattrib->id => $bmatrixattribitem->id];
                    }
                }

                ///        $bar[] = ["bmatrix_id" => $bmatrix->id];
                $string = json_encode($bar);

                DB::table('bmatrixbarcode')->insert([
                    "bmatrix_id" => $bmatrix->id,
                    "pbarcode" => $string,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date("Y-m-d H:i:s"),
                ]);
            }

            if ($is_exist->count() > 0) {
                $ischanged = DB::table('bmatrixbarcode')->where('bmatrix_id', $bmatrix->id)->whereNotIn('pbarcode', $is_exist->pluck('pbarcode')->toArray())->whereNull('deleted_at')->get();

                if ($ischanged->count() > 0) {
                    $this->remove_exisiting_switchs($bmatrix->id);
                }
            }
            $msg = "Definition updated successfully";
            return view('layouts.dialog', compact('msg'))->render();
        } catch (\Exception $e) {
            Log::info($e);
            abort(404);
        }
    }

    public function openitem_cost(Request $request)
    {
        $company = Company::first();
        $prd_sysid = $request->systemid;
        $prd_info = DB::table('product')->where('systemid', $prd_sysid)->first();

        if ($company->auto_stockin == 1) {
            $this->reflect_autostock_openitemproduct_cost();
        }

        return view('openitem.openitem_cost', compact('prd_sysid', 'prd_info'));
    }

    public function openitem_cost_datatable(Request $request)
    {
        try {
            $prd = DB::table('product')->where('systemid', $request->systemid)->first();

            $start = $request->get("start");
            $rowperpage = $request->get("length");
            $totalRecords = DB::table('openitem_productledger')->join('product', 'product.systemid', 'openitem_productledger.product_systemid')->join(
                'openitem_cost',
                'openitem_cost.openitemprodledger_id',
                'openitem_productledger.id'
            )->select(
                'openitem_productledger.id as record_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.csreceipt_id as cr_id',
                'openitem_productledger.type as doc_type',
                'openitem_cost.id as op_id',
                'openitem_cost.cost as cost',
                'openitem_cost.qty_in as stockin',
                'openitem_cost.qty_out as stockout',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at',
                'product.id as product_id'
            )->where('openitem_productledger.product_systemid', $request->systemid)->orderBy('openitem_productledger.created_at', 'DESC')->count();

            $cost_data = DB::table('openitem_productledger')->join('product', 'product.systemid', 'openitem_productledger.product_systemid')->join(
                'openitem_cost',
                'openitem_cost.openitemprodledger_id',
                'openitem_productledger.id'
            )->select(
                'openitem_productledger.id as record_id',
                'openitem_productledger.stockreport_id as sr_id',
                'openitem_productledger.csreceipt_id as cr_id',
                'openitem_productledger.type as doc_type',
                'openitem_cost.id as op_id',
                'openitem_cost.cost as cost',
                'openitem_cost.qty_in as stockin',
                'openitem_cost.qty_out as stockout',
                'openitem_cost.balance as balance',
                'openitem_cost.created_at as created_at',
                'openitem_cost.updated_at as updated_at',
                'product.id as product_id'
            )->where('openitem_productledger.product_systemid', $request->systemid)->
            orderBy('openitem_productledger.created_at', 'DESC')->
            offset($start)->limit($rowperpage)->get();


            $updated_data = collect();

            foreach ($cost_data as $data) {
                if ($data->doc_type == 'stockin') {
                    $stockreportdata = DB::table('stockreport')->whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock In';
                } elseif ($data->doc_type == 'received') {
                    $stockreportdata = DB::table('stockreport')->whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Received';
                } elseif ($data->doc_type == 'stockout') {
                    $stockreportdata = DB::table('stockreport')->whereId($data->sr_id)->first();

                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Stock Out';
                } elseif ($data->doc_type == 'cash_sales') {
                    $stockreportdata = DB::table('cstore_receipt')->whereId($data->cr_id)->first();
                    $data->doc_no = $stockreportdata->systemid;
                    $data->doc_type = 'Cash Sales';
                }
                $updated_data->push($data);
            }

            if ($request->has('search') && !empty($request->search)) {

                $search = trim($request->search);

                Log::debug('request->search:' . $request->search);

                $updated_data = DB::table('openitem_productledger')->
                    join('product', 'product.systemid', 'openitem_productledger.product_systemid')->
                    join(
                    'openitem_cost',
                    'openitem_cost.openitemprodledger_id',
                    'openitem_productledger.id'
                )->
                leftJoin('stockreport', 'stockreport.id', 'openitem_productledger.stockreport_id')->
                leftJoin('cstore_receipt', 'cstore_receipt.id', 'openitem_productledger.csreceipt_id')->
                select(
                    'openitem_productledger.id as record_id',
                    'openitem_productledger.stockreport_id as sr_id',
                    'openitem_productledger.csreceipt_id as cr_id',
                    'openitem_productledger.type as doc_type',
                    'openitem_cost.id as op_id',
                    'openitem_cost.cost as cost',
                    'openitem_cost.qty_in as stockin',
                    'openitem_cost.qty_out as stockout',
                    'openitem_cost.balance as balance',
                    'openitem_cost.created_at as created_at',
                    'openitem_cost.updated_at as updated_at',
                    'product.id as product_id',
                    DB::raw('null as doc_no')
                )->where('openitem_productledger.product_systemid', $request->systemid)->
                orderBy('openitem_productledger.created_at', 'DESC')->
                get();

                $updated_data->filter(function ($value) use ($search) {

                    Log::debug('value:' . $value->doc_type);

                    if (
                        preg_match("/$search/i", $value->doc_type)  ||
                        preg_match("/$search/i", $value->doc_no)
                    ) {

                        return $value;
                    }
                });

                $totalRecords = count($updated_data);
            }

            // return $updated_data;

            return Datatables::of($updated_data)->
                setOffset($start)->
                addIndexColumn()->
                addColumn('doc_no', function ($data) {
                $prd_cost = empty($data->cost) ? 0 : $data->cost;

                $doc_no = $data->doc_no;

                if ($data->doc_type == 'Received') {
                    $doc_no = '<a  href="javascript:window.open(\'' .
                        route('receiving_list_id', $data->doc_no) . '\')"
                    style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                } elseif ($data->doc_type == 'Stock In') {
                    $doc_no = '<a  href="javascript:window.open(\'' .
                        route('stocking.stock_report', $data->doc_no) . '\')"
                    style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                } elseif ($data->doc_type == 'Stock Out') {
                    $doc_no = '<a  href="javascript:window.open(\'' .
                        route('stocking.stock_report', $data->doc_no) . '\')"
                    style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                } elseif ($data->doc_type == 'Cash Sales') {
                    $doc_no = '<a   href="javascript:void(0)"
									onclick="showReceipt(' . $data->cr_id . ')"
                    style="text-decoration:none;" class="">' . $data->doc_no . ' </a>';
                }
                // Log::debug("display_cost_doc_no: " . $doc_no);
                return $doc_no;
            })->addColumn('type', function ($data) {
                return $data->doc_type;
            })->addColumn('cost_date', function ($data) {

                $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
                return $created_at;
            })->addColumn('cost', function ($data) {
                $prd_cost = empty($data->cost) ? 0 : $data->cost;

                if (empty($data->stockout) || $data->stockout == 0 || $data->stockout <= 0) {

                    $cost = '<a  href="javascript:void(0)"
									onclick="open_update_cost_modal('
                        . $data->product_id . ',' .
                        $prd_cost . ',' . $data->record_id . ',' .
                        $data->record_id . ')" data-prod="' .
                        $data->product_id . '" style="text-decoration:none;" class="">' .
                        number_format($data->cost / 100, 2) .
                        ' </a>';
                } else {
                    $cost = '<a style="text-decoration:none;" class="">' .
                        number_format($data->cost / 100, 2) . ' </a>';
                }

                return $cost;
            })->addColumn('stockin', function ($data) {
                return $data->stockin;
            })->addColumn('stockout', function ($data) {

                $so_qty = empty($data->stockout) ? 0 : $data->stockout * -1;

                if (empty($so_qty) || $so_qty == 0) {
                    return $so_qty;
                } else {
                    return '<a href="#" onclick="show_doc_qty_modal(' .
                        $data->stockout . ', ' . $data->op_id . ')"
							style="text-decoration:none;" class="">' . $so_qty . ' </a>';
                }
            })->addColumn('balance', function ($data) {
                return $data->balance;
            })->setTotalRecords($totalRecords)->escapeColumns([])->make(true);
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => true,
            ];
        }
    }

    public function openitem_update_cost(Request $request)
    {
        try {
            DB::table('openitem_productledger')->whereId($request->record_id)->update([
                'cost' => $request->new_cost,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('openitem_cost')->where('openitemprodledger_id', $request->record_id)->update([
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

    public function openitem_qty_population_datatable(Request $request)
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
                    openitem_cost oc
                JOIN openitemcost_qtydist opc ON opc.openitemcost_id = oc.id
                LEFT JOIN cstore_receipt cr ON cr.id = opc.csreceipt_id
                JOIN openitem_productledger opl ON opl.id = oc.openitemprodledger_id
                LEFT JOIN stockreport sr ON sr.id = opl.stockreport_id
                WHERE
                    oc.id = $op_id AND
                    opc.qty_taken <> 0;
            "));
            $c_qty_pop = [];

            foreach ($qty_population as $qp) {

                if (isset($qp->stockreport_id)) {

                    $sreport = DB::select(DB::raw("
                        SELECT
                            *
                        FROM
                            stockreport
                        WHERE
                            id = $qp->stockreport_id
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
                    $creport = DB::select(DB::raw("
                        SELECT
                            *
                        FROM
                            cstore_receipt
                        WHERE
                            id = $qp->csreceipt_id
                    "));
                    $qp->cr_systemid = (sizeof($creport) > 0) ? $creport[0]->systemid : null;
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

    public function reflect_autostock_openitemproduct_cost()
    {
        $opnProd = DB::select(
            DB::raw("

				SELECT
                    p.id as product_id,
                    p.name,
                    p.systemid,
                    srp.quantity,
                    srp.stockreport_id
				FROM
                    product p,
                    prd_openitem op,
                    stockreport sr,
                    stockreportproduct srp
				WHERE
                    op.product_id = p.id AND
                    srp.product_id = p.id AND
                    srp.stockreport_id = sr.id
				;")
        );

        if (!empty($opnProd)) {

            foreach ($opnProd as $key => $value) {
                # code...
                $stocrep = DB::table('openitem_productledger')->where('stockreport_id', $value->stockreport_id)->where('product_systemid', $value->systemid)->first();

                $product = DB::table('openitem_productledger')->where('product_systemid', $value->systemid)->latest()->first();

                if (!$stocrep) {

                    if ($product) {

                        $cost = DB::select(
                            DB::raw(
                                "
                                SELECT
                                    opl.product_systemid,
                                    max(opc.cost) as locost
                                FROM
                                     openitem_productledger opl,
                                    openitem_cost opc
                                WHERE
                                        opc.openitemprodledger_id ='" . $product->id . "'
                                GROUP BY
                                        opl.product_systemid
                                        ;"
                            )
                        );

                        $cost = $cost[0]->locost ?? 0;
                        Log::debug("LOCost: " . $cost);
                        if ($value->quantity >= 0) {
                            $lpl_id = DB::table('openitem_productledger')->insertGetID([
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

                            DB::table('openitem_cost')->insert([
                                "openitemprodledger_id" => $lpl_id,
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
                            $lpl_id = DB::table('openitem_productledger')->insertGetID([
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

                            DB::table('openitem_cost')->insert([
                                "openitemprodledger_id" => $lpl_id,
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
    }

}
