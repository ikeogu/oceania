<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Location;
use App\Models\Product;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use Yajra\DataTables\DataTables;

class FuelMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fm_guide()
    {
        return view("fuel_movement.fmguide");
    }

    public function fuelStockIn()
    {
        $company = Company::first();
        $location = Location::first();
        return view("fuel_stockmgmt.stockin", compact('location', 'company'));
    }

    public function mainDatatable(Request $request)
    {
        try {
            $product_id = $request->ogfuel_id;

            $data = DB::table('fuelmovement')->
                join('prd_ogfuel', 'fuelmovement.ogfuel_id', 'prd_ogfuel.id')->
                where('prd_ogfuel.product_id', $product_id)->
                select('fuelmovement.*', 'prd_ogfuel.product_id')->
                get();

            return Datatables::of($data)->
                addIndexColumn()->
                addColumn('date', function ($fuel_list) {
            })->
                addColumn('cforward', function ($fuel_list) {
            })->
                addColumn('sales', function ($fuel_list) {
            })->
                addColumn('receipt', function ($fuel_list) {
                $qty = number_format(app("App\Http\Controllers\CentralStockMgmtController")->
                        qtyAvailable($fuel_list->product_id), 2);

                $url = route('fuel_movement.showproductledgerReceipt', $fuel_list->id);
                return <<<EOD
						<span class="os-linkcolor" onclick="window.open('$url')" style="cursor:pointer">$qty</span>
EOD;
            })->
                addColumn('book', function ($fuel_list) {
            })->
                addColumn('tank_dip', function ($fuel_list) {
            })->
                addColumn('daily_variance', function ($fuel_list) {
            })->
                addColumn('cumulative', function ($fuel_list) {
            })->
                addColumn('percentage', function ($fuel_list) {
            })->
                escapeColumns([])->
                make(true);

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }

    public function fuelProduct(Request $request)
    {
        $data = Product::query()->whereptype('oilgas')->get();

        if ($request->type == 'out') {
            $data = $data->filter(function ($product) {
                return app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($product->id) > 0;
            });
        }

        return Datatables::of($data)->
            addIndexColumn()->
            addColumn('product_name', function ($data) {
            $img_src = '/images/product/' .
            $data->systemid . '/thumb/' .
            $data->thumbnail_1;

            $img = "<img src='$img_src' data-field='inven_pro_name' style=' width: 25px;
			height: 25px;display: inline-block;margin-right: 8px;object-fit:contain;'>";

            return $img . $data->name;
        })->addColumn('inven_existing_qty', function ($data) {
            $product_id = $data->id;
            $qty = app("App\Http\Controllers\CentralStockMgmtController")->
                bookValAvailable($product_id);
            $qty = number_format($qty, 2);
            return <<<EOD
			<span id="qty_$product_id">$qty</span>
EOD;
        })->addColumn('inven_qty', function ($data) {
            $product_id = $data->id;
            return view('fuel_stockmgmt.inven_qty', compact('product_id'));
        })
            ->rawColumns(['inven_existing_qty', 'inven_qty', 'product_name'])
            ->make(true);
    }

    public function fuelStockOut()
    {
        $company = Company::first();
        $location = Location::first();
        return view("fuel_stockmgmt.stockout", compact('location', 'company'));
    }

    public function getOgFuelQualifiedProducts($company_id = null)
    {
        $products_chunck = array();
        $filter = array();

        $ids = DB::table('prd_ogfuel')->get()->
            pluck('product_id');

        $products = DB::table('product')->
            select('product.*', 'prd_ogfuel.id as og_f_id')->
            join('prd_ogfuel', 'product.id', 'prd_ogfuel.product_id')->
            whereIn('product.id', $ids)->
            where([
            ['product.name', '<>', null],
            ['product.photo_1', '!=', null],
        ])->
            get();

        return $products;
    }

    public function showOgFuelQualifiedProducts()
    {
        $products = $this->getOgFuelQualifiedProducts();
        $output = "";
        foreach ($products as $product) {
            $output .= '<button class="btn btn-success bg-enter btn-log sellerbuttonwide ps-function-btn pump_credit_card_product" href_fuel_prod_name="'
            . $product->name . '" href_fuel_prod_id="' . $product->id . '" href_fuel_prod_thumbnail="' . $product->thumbnail_1 . '" href_fuel_prod_systemid="'
            . $product->systemid . '" style="width: 129px !important;"> <span>' . $product->name . ' </span></button>';
        }

        $totalRecords = count($products);

        $response = [
            'data' => $products,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'output' => $output,
        ];
        return response()->json($response);
    }

    public function showproductledgerSale(Request $request)
    {
        $product = DB::table('product')->where("id", $request->fuel_prod_id)->first();
        $location = Location::first();
        $receipts = DB::table('fuel_receipt')->
            select('fuel_receipt.*', 'fuel_receiptproduct.quantity as quantity', 'fuel_receiptdetails.id as receiptdetails_id',
            'receiptrefund.qty', 'receiptrefund.refund_amount')->
            join('fuel_receiptproduct', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')
            ->whereDate('fuel_receipt.created_at', '=', date('Y-m-d', strtotime($request->date)))
            ->leftJoin('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')
            ->leftJoin('receiptrefund', 'receiptrefund.receipt_id', 'fuel_receipt.id')
            ->where("fuel_receiptproduct.product_id", $request->fuel_prod_id)->orderBy('fuel_receipt.id', 'desc')->get();

        $fuel_receipts = DB::table('fuel_receipt')->
            select('fuel_receipt.*', 'fuel_receiptproduct.quantity as quantity', 'fuel_receiptdetails.id as receiptdetails_id',
            'receiptrefund.qty', 'receiptrefund.refund_amount')->
            join('fuel_receiptproduct', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')
            ->whereDate('fuel_receipt.created_at', '=', date('Y-m-d', strtotime($request->date)))
            ->leftJoin('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')
            ->leftJoin('receiptrefund', 'receiptrefund.receipt_id', 'fuel_receipt.id')
            ->where("fuel_receiptproduct.product_id", $request->fuel_prod_id)->orderBy('fuel_receipt.id', 'desc')->get();

        $oew_receipts = DB::table('oew_receipt')->
            select('oew_receipt.*', 'oew_receiptproduct.quantity as quantity', 'oew_receiptdetails.id as receiptdetails_id',
            'receiptrefund.qty', 'receiptrefund.refund_amount')->
            join('oew_receiptproduct', 'oew_receipt.id', 'oew_receiptproduct.receipt_id')
            ->whereDate('oew_receipt.created_at', '=', date('Y-m-d', strtotime($request->date)))
            ->leftJoin('oew_receiptdetails', 'oew_receipt.id', 'oew_receiptdetails.receipt_id')
            ->leftJoin('receiptrefund', 'receiptrefund.receipt_id', 'oew_receipt.id')
            ->where("oew_receiptproduct.product_id", $request->fuel_prod_id)->orderBy('oew_receipt.id', 'desc')->get();

        $receipts = $fuel_receipts->merge($oew_receipts);
        $date = $request->date;
        $id = $request->fuel_prod_id;
        return view("fuel_movement.productledger_sale", compact("receipts", "location", "product", "id", "date"));
    }

    public function showproductledgerReceipt(Request $request)
    {
        try {
            $product_id = $request->product_id;

            $stockData = DB::table('stockreport')->
                leftjoin('location', 'location.id', 'stockreport.location_id')->
                join('stockreportproduct', 'stockreportproduct.stockreport_id', 'stockreport.id')->
                select("stockreport.*", 'stockreportproduct.quantity',
                "location.name as location_name")->
                orderBy('stockreport.updated_at', 'desc')->
                where('stockreportproduct.product_id', $product_id)->
                get();

            return view("fuel_movement.productledger_receipt", compact('stockData'));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function cForwardUpdate(Request $request)
    {
        DB::table('fuelmovement')->where('id', $request->id)
            ->update([
                "cforward" => $request->c_Forward,
                "book" => $request->book,
                "daily_variance" => $request->daily_variance,
                "cumulative" => $request->cumulative,
                "percentage" => $request->percentage,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        return true;
    }

    public function tankdipUpdate(Request $request)
    {
        $company = DB::table('company')->first();
        $location = DB::table('location')->first();

        $prd_og_fuel = DB::table('prd_ogfuel')->where('product_id', $request->ogfuel_id)->first();

        $is_exist = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_og_fuel->id)->
            whereDate('date', date('Y-m-d', strtotime($request->date)))->
            first();

        if (empty($is_exist)) {
            $this->add_fuel_movement($location->id, $prd_og_fuel->id,
                $company->id, date('Y-m-d', strtotime($request->date)), 0);
        }

        $yesterday_rec = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_og_fuel->id)->
            whereDate('date', date('Y-m-d', strtotime('-1 day ' . $request->date)))->
            first();

        $prev = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_og_fuel->id)->
            whereDate('date', date('Y-m-d', strtotime($request->date)))->
            first();

        $next_rec = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_og_fuel->id)->
            whereDate('date', date('Y-m-d', strtotime('+1 day ' . $prev->date)))->
            first();

        $cumulative = ($yesterday_rec->cumulative ?? 0) + $request->tank_dip - $prev->book;

        if ($prev->book != 0) {
            $percentage = abs(($cumulative / $prev->book) * 100) * 100;
        }

        DB::table('fuelmovement')->
            where('ogfuel_id', $prd_og_fuel->id)->
            whereDate('date', date('Y-m-d', strtotime($request->date)))->
            update([
                "tank_dip" => $request->tank_dip,
                "daily_variance" => $request->tank_dip - $prev->book,
                "cumulative" => $cumulative,
                "percentage" => $percentage ?? 0,
                'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Log::debug("tankdipUpdate: tank_dip=" . json_encode($request->tank_dip));

        if (date('d', strtotime('+1 day ' . $prev->date)) != 1) {
            if (!empty($next_rec)) {

                if (!empty($next_rec)) {
                    $book = $request->tank_dip;
                }
                //- $next_rec->sales; + $next_rec->receipt;

                DB::table('fuelmovement')->where('id', $next_rec->id)->
                    update([
                    'book' => $book ?? 00,
                    'cforward' => $request->tank_dip,
                ]);

            } else {
                $this->add_fuel_movement(
                    $location->id,
                    $prd_og_fuel->id,
                    $company->id,
                    date('Y-m-d', strtotime('+1 day ' . $prev->date)),
                    $request->tank_dip);
            }
        }

        return true;
    }

    public function add_fuel_movement($location_id, $ogfuel_id, $franchisee_company_id, $created_at, $cforward)
    {
        DB::table('fuelmovement')->insert([
            "location_id" => $location_id,
            "ogfuel_id" => $ogfuel_id,
            "franchisee_company_id" => $franchisee_company_id,
            "date" => $created_at,
            "cforward" => $cforward,
            "sales" => "0.00",
            "receipt" => "0.00",
            "book" => $cforward,
            "tank_dip" => "0.00",
            "daily_variance" => "0.00",
            "cumulative" => "0.00",
            "percentage" => "0.00",
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
        ]);
    }

    public function showOgFuelProducts()
    {
        $ids = DB::table('prd_ogfuel')->get()->
            pluck('product_id');

        $products = DB::table('product')->
            select('product.*', 'prd_ogfuel.id as og_f_id')->
            join('prd_ogfuel', 'product.id', 'prd_ogfuel.product_id')->
            whereIn('product.id', $ids)->
            where('product.ptype', 'oilgas')->
            where([
            ['product.name', '<>', null],
            ['product.photo_1', '!=', null],
        ])->
            get();
        $output = "";

        foreach ($products as $product) {
            $output .= "<div class='col-md-12 ml-0 pl-0'><div class='row align-items-center d-flex'>
			<div class='col-md-2'>
			<img class='thumbnail productselect sellerbutton' href_fuel_thumbnail='' href_fuel_prod_name='" . $product->name . "'
			href_fuel_prod_id='" . $product->id . "' href_fuel_prod_thumbnail='" . $product->thumbnail_1 . "' href_fuel_prod_systemid='" . $product->systemid . "'
			style='padding-top:0;object-fit:contain;float:right;width:30px !important;height:30px !important;margin-left:0;margin-top:2px;margin-right:0;margin-bottom:2px' src='/images/product/" . $product->systemid . "/thumb/" . $product->thumbnail_1 . "'>
			</div>
			<div class='col-md-10 pl-0 productselect' href_fuel_thumbnail=''
			href_fuel_prod_name='" . $product->name . "' href_fuel_prod_id='" . $product->id . "' href_fuel_prod_thumbnail='" . $product->thumbnail_1 . "' href_fuel_prod_systemid='" . $product->systemid . "'
			style='cursor:pointer;line-height:1.2;margin-left:0;font-size:20px;padding-top:0;text-align: left;'>" . $product->name . "</div></div></div>";
        }

        $totalRecords = count($products);
        $response = [
            'data' => $products,
            'output' => $output,
        ];
        return response()->json($response);
    }

    public function updateSalescolumn($fuel_prod_id)
    {

        Log::info("***** updateSalescolumn(" . $fuel_prod_id . ") *****");

        // $product = DB::table('product')->where("id",$fuel_prod_id)->first();
        $qtysum = DB::table('fuel_receiptdetails')
            ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
            ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
            ->where('fuel_receiptproduct.product_id', '=', $fuel_prod_id)
            ->where('fuel_receipt.status', '!=', 'voided')
            ->whereDate('fuel_receipt.created_at', date('Y-m-d') . ' 00:00:00')
            ->sum('fuel_receiptproduct.quantity');

        $refund = DB::table('fuel_receiptdetails')
            ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
            ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
            ->leftJoin('receiptrefund', 'receiptrefund.receipt_id', 'fuel_receipt.id')
            ->where('fuel_receiptproduct.product_id', '=', $fuel_prod_id)
            ->where('fuel_receipt.status', '!=', 'voided')
            ->whereDate('fuel_receipt.created_at', date('Y-m-d') . ' 00:00:00')
            ->sum('receiptrefund.qty');

        /*Update Sales Column for Fulltank too*/
        $fulltank_quantity = DB::table('fuelfulltank_receiptdetails')
            ->join('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')
            ->join('fuelfulltank_receipt', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
            ->where('fuelfulltank_receiptproduct.product_id', '=', $fuel_prod_id)
            ->where('fuelfulltank_receipt.status', '!=', 'voided')
            ->whereDate('fuelfulltank_receipt.created_at', date('Y-m-d') . ' 00:00:00')
            ->sum('fuelfulltank_receiptproduct.quantity');
        /*Update Sales Column for Fulltank too*/



        $prd_ogfuel = DB::table('prd_ogfuel')->where("product_id", $fuel_prod_id)->first();

        $rec = DB::table('fuelmovement')->where('date', date('Y-m-d'))->
            where('ogfuel_id', $prd_ogfuel->id)->first();

        $book = 0;
        if (!empty($rec)) {
            $book = $rec->cforward - $qtysum + $rec->receipt;
        }

        $book += $refund;

        $sales_after_refund = $fulltank_quantity + $qtysum - $refund;

        Log::info("sales  =" . $qtysum);
        Log::info("refund = " . $refund);
        Log::info("book   =" . $book);
        Log::info("sales_after_refund =" . $sales_after_refund);

        DB::table('fuelmovement')->where('date', date('Y-m-d'))->
            where('ogfuel_id', $prd_ogfuel->id)->
            update([
            'book' => $book ?? 0,
            //"sales" => $qtysum,
            "sales" => $sales_after_refund,
            "updated_at" => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    public function fuelmovementmaintable(Request $request)
    {
        $startDate = Carbon::now(); //returns current day
        $firstDay = $startDate->firstOfMonth();
        // return strtotime($firstDay)." ".strtotime($request->startmonth);
        $company = Company::first();
        $location = Location::first();
        $prd_ogfuel = DB::table('prd_ogfuel')->where("product_id", $request->fuel_prod_id)->first();
        $today = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_ogfuel->id)->
            where('date', date('Y-m-d'))->first();
        if ($today === null) {
            $lastday = DB::table('fuelmovement')->
                where('ogfuel_id', $prd_ogfuel->id)->
                where('date', date('Y-m-d', strtotime(' -1 day')))->first();
            if (strtotime($request->startmonth) == strtotime(date('Y-m-d'))) {
                DB::table('fuelmovement')->insert([
                    "location_id" => $location->id,
                    "ogfuel_id" => $prd_ogfuel->id,
                    "franchisee_company_id" => $company->id,
                    "date" => date('Y-m-d'),
                    "cforward" => "0.00",
                    "sales" => "0.00",
                    "receipt" => "0.00",
                    "book" => "0.00",
                    "tank_dip" => "0.00",
                    "daily_variance" => "0.00",
                    "cumulative" => "0.00",
                    "percentage" => "0.00",
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            } elseif ($lastday === null) {
                DB::table('fuelmovement')->insert([
                    "location_id" => $location->id,
                    "ogfuel_id" => $prd_ogfuel->id,
                    "franchisee_company_id" => $company->id,
                    "date" => date('Y-m-d'),
                    "cforward" => "0.00",
                    "sales" => "0.00",
                    "receipt" => "0.00",
                    "book" => "0.00",
                    "tank_dip" => "0.00",
                    "daily_variance" => "0.00",
                    "cumulative" => "0.00",
                    "percentage" => "0.00",
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            } else {
                $percentage = 0;
                $book = $lastday->tank_dip;
                $daily_variance = 0 - $book;
                $cumulative = $daily_variance + $lastday->cumulative;
                if ($cumulative != 0) {
                    $percentage = $cumulative / $book * 100;
                }
                DB::table('fuelmovement')->insert([
                    "location_id" => $location->id,
                    "ogfuel_id" => $prd_ogfuel->id,
                    "franchisee_company_id" => $company->id,
                    "date" => date('Y-m-d'),
                    "cforward" => $lastday->tank_dip,
                    "sales" => "0.00",
                    "receipt" => "0.00",
                    "book" => $book,
                    "tank_dip" => "0.00",
                    "daily_variance" => $daily_variance,
                    "cumulative" => $cumulative,
                    "percentage" => $percentage,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);

            }
        }
        $lastday = DB::table('fuelmovement')->
            where('ogfuel_id', $prd_ogfuel->id)->
            where('date', date('Y-m-d', strtotime(' -1 day')))->first();
        // return $lastday;
        if ($lastday === null) {
            $datecheck = date('Y-m-d', strtotime(' -1 day'));
            $i = 1;
            $j = 1;
            // return $datecheck;
            while (strtotime($request->startmonth) <= strtotime($datecheck)) {
                $lastday = DB::table('fuelmovement')->
                    where('ogfuel_id', $prd_ogfuel->id)->
                    where('date', $datecheck)->first();

                $i++;
                if ($lastday === null) {
                    DB::table('fuelmovement')->insert([
                        "location_id" => $location->id,
                        "ogfuel_id" => $prd_ogfuel->id,
                        "franchisee_company_id" => $company->id,
                        "date" => $datecheck,
                        "cforward" => "0.00",
                        "sales" => "0.00",
                        "receipt" => "0.00",
                        "book" => "0.00",
                        "tank_dip" => "0.00",
                        "daily_variance" => "0.00",
                        "cumulative" => "0.00",
                        "percentage" => "0.00",
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);
                }
                $datecheck = date('Y-m-d', strtotime(' -' . $i . ' day'));
            }
            // return $i."j".$j;
        }

        $this->updateSalescolumn($request->fuel_prod_id);
        $product_id = $request->fuel_prod_id;
        $product_id = $request->fuel_prod_id;
        $startdate = $request->startmonth;
        $enddate = $request->endmonth;

        $data = DB::table('fuelmovement')->
            join('prd_ogfuel', 'fuelmovement.ogfuel_id', 'prd_ogfuel.id')->
            where('prd_ogfuel.product_id', $product_id)->
            whereBetween('date', [$startdate, $enddate])->
            select('fuelmovement.*', 'prd_ogfuel.product_id')->
            get()->map(function ($f) use ($product_id) {
            $yesterday_rec = DB::table('fuelmovement')->
                join('prd_ogfuel', 'fuelmovement.ogfuel_id', 'prd_ogfuel.id')->
                where('prd_ogfuel.product_id', $product_id)->
                whereDate('date', date("Y-m-d", strtotime($f->date . ' -1day')))->
                select('fuelmovement.*', 'prd_ogfuel.product_id')->
                first();

            $f->receipt = DB::table('stockreportproduct')->
                leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
                where('stockreportproduct.product_id', $product_id)->
                whereYear("stockreport.created_at", date("Y", strtotime($f->date)))->
                whereMonth("stockreport.created_at", date("m", strtotime($f->date)))->
                whereDay("stockreport.created_at", date("d", strtotime($f->date)))->
                get()->sum('quantity');

            $f
                ->sales_pos = DB::table('fuel_receiptdetails')
                ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
                ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
                ->where('fuel_receiptproduct.product_id', '=', $product_id)
                ->where('fuel_receipt.status', '!=', 'voided')
                ->whereDate('fuel_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('fuel_receiptproduct.quantity');

            $f->fulltank_sales = DB::table('fuelfulltank_receiptdetails')
            ->join('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')
            ->join('fuelfulltank_receipt', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
            ->where('fuelfulltank_receiptproduct.product_id', '=', $product_id)
            ->where('fuelfulltank_receipt.status', '!=', 'voided')
            ->whereDate('fuelfulltank_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
            ->sum('fuelfulltank_receiptproduct.quantity');

            $f->oew_sales = DB::table('oew_receiptdetails')
            ->join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=', 'oew_receiptdetails.receipt_id')
            ->join('oew_receipt', 'oew_receiptproduct.receipt_id', '=', 'oew_receipt.id')
            ->where('oew_receiptproduct.product_id', '=', $product_id)
            ->where('oew_receipt.status', '!=', 'voided')
            ->whereDate('oew_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
            ->sum('oew_receiptproduct.quantity');

            $f
                ->sales_neg = DB::table('fuel_receiptdetails')
                ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
                ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
                ->leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')
                ->where('fuel_receiptproduct.product_id', '=', $product_id)
                ->where('fuel_receipt.status', '!=', 'voided')
                ->whereDate('fuel_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('fuel_receiptlist.refund_qty');

            $f
                ->oew_sales_neg = DB::table('oew_receiptdetails')
                ->join('oew_receiptproduct', 'oew_receiptproduct.receipt_id', '=', 'oew_receiptdetails.receipt_id')
                ->join('oew_receipt', 'oew_receiptproduct.receipt_id', '=', 'oew_receipt.id')
                ->leftJoin('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')
                ->where('oew_receiptproduct.product_id', '=', $product_id)
                ->where('oew_receipt.status', '!=', 'voided')
                ->whereDate('oew_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('oew_receiptlist.refund_qty');
            // \DB::raw('fuel_receiptlist.refund / fuel_receiptproduct.price')
            $f->sales = $f->fulltank_sales + $f->oew_sales + $f->sales_pos - $f->sales_neg - $f->oew_sales_neg;

            /* Log::info("Date ". date("Y-m-d", strtotime($f->date)));

            Log::info("net ". $f->sales);
            Log::info("sales ". $f->sales_pos);
            Log::info("refund ". $f->sales_neg);
            */
            $f->book += $f->receipt - $f->fulltank_sales - $f->oew_sales + $f->sales_neg + $f->oew_sales_neg;

            $f->daily_variance = $f->tank_dip - $f->book;

            //$f->cumulative = ($yesterday_rec->tank_dip ?? 0) - ($yesterday_rec->book ?? 0) + $f->daily_variance;
            $f->cumulative = ($yesterday_rec->cumulative ?? 0) + $f->daily_variance;

            if ($f->book != 0) {
                $f->percentage = abs(($f->cumulative / $f->book) * 100);
            }

            /*
            \Log::info([

            'current tank_dip' => $f->tank_dip,
            'current book' => $f->book,
            'current daily_variance' => $f->daily_variance,

            'yesterday tank_dip' =>$yesterday_rec->tank_dip ?? 0,
            'yesterday book' => $yesterday_rec->book ?? 0 ,
            'yesterday daily_variance' => ($yesterday_rec->tank_dip ?? 0) - ($yesterday_rec->book ?? 0)

            ]);*/
            return $f;
        });

        return $data;
    }

    public function showproductledgersReceipt(Request $request)
    {
        $product = DB::table('product')->where("id", $request->fuel_prod_id)->first();
        $location = Location::first();
        $data = collect();

        DB::table('receipt')->
            select('receipt.*', 'receiptproduct.quantity as quantity', 'receiptdetails.id as receiptdetails_id')->
            join('receiptproduct', 'receipt.id', 'receiptproduct.receipt_id')->
            leftJoin('receiptdetails', 'receipt.id', 'receiptdetails.receipt_id')->
            where("receiptproduct.product_id", $request->product_id)->get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at;
            $packet->doc_type = "Cash Sales";
            $data->push($packet);
        });
        DB::table('fuelfulltank_receipt')->
            select('fuelfulltank_receipt.*', 'fuelfulltank_receiptproduct.quantity as quantity', 'fuelfulltank_receiptdetails.id as receiptdetails_id')->
            join('fuelfulltank_receiptproduct', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptproduct.fulltank_receipt_id')->
            leftJoin('fuelfulltank_receiptdetails', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptdetails.fulltank_receipt_id')->
            where("fuelfulltank_receiptproduct.product_id", $request->product_id)->get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at;
            $packet->doc_type = "Full Tank Sales";
            $data->push($packet);
        });

        DB::table('stockreportproduct')->
            leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
            where('stockreportproduct.product_id', $product->id)->
            whereYear("stockreport.created_at", date("Y", strtotime($request->date)))->
            whereMonth("stockreport.created_at", date("m", strtotime($request->date)))->
            whereDay("stockreport.created_at", date("d", strtotime($request->date)))->
            orderBy('stockreport.id', 'desc')->
            get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at ?? "";
            $packet->doc_type = ucfirst($product->type);
            $packet->staff = DB::table('users')->find($product->creator_user_id)->fullname;
            $data->push($packet);
        });

        return view("fuel_movement.productledger_receipt", compact("location", "product", "data"));
    }

    public function showOnlyProductLedgersReceipt(Request $request)
    {
        $product = DB::table('product')->where("id", $request->fuel_prod_id)->first();
        $location = Location::first();
        $data = collect();

        DB::table('fuelfulltank_receipt')->
            select('fuelfulltank_receipt.*', 'fuelfulltank_receiptproduct.quantity as quantity', 'fuelfulltank_receiptdetails.id as receiptdetails_id')->
            join('fuelfulltank_receiptproduct', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptproduct.fulltank_receipt_id')->
            leftJoin('fuelfulltank_receiptdetails', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptdetails.fulltank_receipt_id')->
            where("fuelfulltank_receiptproduct.product_id", $request->product_id)->get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at;
            $packet->doc_type = "Full Tank Sales";
            $data->push($packet);
        });
        DB::table('fuel_receipt')->
            select('fuel_receipt.*', 'fuel_receiptproduct.quantity as quantity', 'fuel_receiptdetails.id as receiptdetails_id')->
            join('fuel_receiptproduct', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')->
            leftJoin('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')->
            where("fuel_receiptproduct.product_id", $request->product_id)->get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at;
            $packet->doc_type = "Cash Sales";
            $data->push($packet);
        });

        DB::table('stockreportproduct')->
            leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
            where('stockreportproduct.product_id', $product->id)->
            whereYear("stockreport.created_at", date("Y", strtotime($request->date)))->
            whereMonth("stockreport.created_at", date("m", strtotime($request->date)))->
            whereDay("stockreport.created_at", date("d", strtotime($request->date)))->
            orderBy('stockreport.id', 'desc')->
            get()->map(function ($product) use ($data) {
            $packet = collect();
            $packet->id = $product->id;
            $packet->status = $product->status;
            $packet->systemid = $product->systemid;
            $packet->quantity = $product->quantity;
            $packet->created_at = $product->created_at;
            $packet->voided_at = $product->voided_at ?? "";
            $packet->doc_type = ucfirst($product->type);
            $packet->staff = DB::table('users')->find($product->creator_user_id)->fullname;
            $data->push($packet);
        });

        return view("fuel_movement.productledger_receipt", compact("location", "product", "data"));
    }

    public function dataTable($id, $date)
    {
        $receipt_new = collect();
        $receipts = DB::table('fuel_receipt')->
            select('fuel_receipt.*', 'fuel_receiptproduct.quantity as quantity',
            'fuel_receiptproduct.price as price', 'fuel_receiptlist.refund_qty',
            'fuel_receiptdetails.id as receiptdetails_id', 'fuel_receiptlist.refund as refund_amount')->
            join('fuel_receiptproduct', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')
            ->whereDate('fuel_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
            ->leftJoin('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')
            ->leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')
            ->where("fuel_receiptproduct.product_id", $id)->orderBy('fuel_receipt.id', 'desc')->get();
        // var_dump($receipts);exit();
        DB::table('fuel_receipt')->
            select('fuel_receipt.*', 'fuel_receiptproduct.quantity as quantity',
            'fuel_receiptproduct.price as price', 'fuel_receiptlist.refund_qty',
            'fuel_receiptdetails.id as receiptdetails_id', 'fuel_receiptlist.refund as refund_amount')->
            join('fuel_receiptproduct', 'fuel_receipt.id', 'fuel_receiptproduct.receipt_id')
            ->whereDate('fuel_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
            ->leftJoin('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')
            ->leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')
            ->where("fuel_receiptproduct.product_id", $id)->orderBy('fuel_receipt.id', 'desc')
            ->get()->map(function ($receipts) use ($receipt_new) {

            if ($receipts->status === "refunded") {
                $packet = collect();
                $packet->id = $receipts->id;
                $packet->systemid = $receipts->systemid;
                $packet->quantity = -1 * $receipts->refund_qty;
                $packet->status = "Refund";
                $packet->created_at = $receipts->created_at;
                $packet->is_ft = false;
                $packet->is_oew = false;
                $receipt_new->push($packet);
            }
            $packet = collect();
            $packet->id = $receipts->id;
            $packet->is_ft = false;
            $packet->is_oew = false;
            $packet->systemid = $receipts->systemid;
            $packet->quantity = $receipts->quantity;
            if ($receipts->status === "voided") {
                $packet->status = "Void";
            } else { $packet->status = "Cash Sales";}
            $packet->created_at = $receipts->created_at;
            $receipt_new->push($packet);
            // print_r($packet);

        });
        $oew_receipts = DB::table('oew_receipt')->
            select('oew_receipt.*', 'oew_receiptproduct.quantity as quantity',
            'oew_receiptproduct.price as price', 'oew_receiptlist.refund_qty',
            'oew_receiptdetails.id as receiptdetails_id', 'oew_receiptlist.refund as refund_amount')->
            join('oew_receiptproduct', 'oew_receipt.id', 'oew_receiptproduct.receipt_id')
            ->whereDate('oew_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
            ->leftJoin('oew_receiptdetails', 'oew_receipt.id', 'oew_receiptdetails.receipt_id')
            ->leftJoin('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')
            ->where("oew_receiptproduct.product_id", $id)->orderBy('oew_receipt.id', 'desc')->get();
        // var_dump($receipts);exit();
        DB::table('oew_receipt')->
            select('oew_receipt.*', 'oew_receiptproduct.quantity as quantity',
            'oew_receiptproduct.price as price', 'oew_receiptlist.refund_qty',
            'oew_receiptdetails.id as receiptdetails_id', 'oew_receiptlist.refund as refund_amount')->
            join('oew_receiptproduct', 'oew_receipt.id', 'oew_receiptproduct.receipt_id')
            ->whereDate('oew_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
            ->leftJoin('oew_receiptdetails', 'oew_receipt.id', 'oew_receiptdetails.receipt_id')
            ->leftJoin('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')
            ->where("oew_receiptproduct.product_id", $id)->orderBy('oew_receipt.id', 'desc')
            ->get()->map(function ($receipts) use ($receipt_new) {

            if ($receipts->status === "refunded") {
                $packet = collect();
                $packet->id = $receipts->id;
                $packet->systemid = $receipts->systemid;
                $packet->quantity = -1 * $receipts->refund_qty;
                $packet->status = "Refund";
                $packet->created_at = $receipts->created_at;
                $packet->is_oew = true;
                $packet->is_ft = false;
                $receipt_new->push($packet);
            }
            $packet = collect();
            $packet->id = $receipts->id;
            $packet->is_ft = false;
            $packet->is_oew = true;
            $packet->systemid = $receipts->systemid;
            $packet->quantity = $receipts->quantity;
            if ($receipts->status === "voided") {
                $packet->status = "Void";
            } else { $packet->status = "Outdoor Ewallet Sales";}
            $packet->created_at = $receipts->created_at;
            $receipt_new->push($packet);
            // print_r($packet);

        });
        $fulltank_receipts =  DB::table('fuelfulltank_receipt')->
        select('fuelfulltank_receipt.*', 'fuelfulltank_receiptproduct.quantity as quantity',
        'fuelfulltank_receiptproduct.price as price', 'fuelfulltank_receiptlist.refund_qty',
        'fuelfulltank_receiptdetails.id as receiptdetails_id', 'fuelfulltank_receiptlist.refund as refund_amount')->
        join('fuelfulltank_receiptproduct', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptproduct.fulltank_receipt_id')
        ->whereDate('fuelfulltank_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
        ->leftJoin('fuelfulltank_receiptdetails', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptdetails.fulltank_receipt_id')
        ->leftJoin('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', 'fuelfulltank_receipt.id')
        ->where("fuelfulltank_receiptproduct.product_id", $id)->orderBy('fuelfulltank_receipt.id', 'desc')
        ->get();
        DB::table('fuelfulltank_receipt')->
            select('fuelfulltank_receipt.*', 'fuelfulltank_receiptproduct.quantity as quantity',
            'fuelfulltank_receiptproduct.price as price', 'fuelfulltank_receiptlist.refund_qty',
            'fuelfulltank_receiptdetails.id as receiptdetails_id', 'fuelfulltank_receiptlist.refund as refund_amount')->
            join('fuelfulltank_receiptproduct', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptproduct.fulltank_receipt_id')
            ->whereDate('fuelfulltank_receipt.created_at', '=', date('Y-m-d', strtotime($date)))
            ->leftJoin('fuelfulltank_receiptdetails', 'fuelfulltank_receipt.id', 'fuelfulltank_receiptdetails.fulltank_receipt_id')
            ->leftJoin('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', 'fuelfulltank_receipt.id')
            ->where("fuelfulltank_receiptproduct.product_id", $id)->orderBy('fuelfulltank_receipt.id', 'desc')
            ->get()->map(function ($fulltank_receipts) use ($receipt_new) {

            $packet = collect();
            $packet->id = $fulltank_receipts->id;
            $packet->systemid = $fulltank_receipts->systemid;
            $packet->quantity = $fulltank_receipts->quantity;
            $packet->status = "Full Tank Sales";
            $packet->created_at = $fulltank_receipts->created_at;
            $packet->is_ft = true;
            $packet->is_oew = false;
            $receipt_new->push($packet);

            //print_r($packet);

        });
        $receipt_new = collect($receipt_new)->sortByDesc('created_at')->all();
        return Datatables::of($receipt_new)->
            addIndexColumn()->
            addColumn('date', function ($data) {
            $created_at = Carbon::parse($data->created_at)->format('dMy H:i:s');
            return <<<EOD
					$created_at
EOD;
        })->
            addColumn('systemid', function ($data) {
              $ft = $data->is_ft ? true : '0';
              $oew = $data->is_oew ? true : '0';
            $systemid = !empty($data->systemid) ? '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="showReceipt(' . $data->id . ', '. $ft .', ' . $oew .')" > ' . $data->systemid . '</a>' : 'Receipt ID';

            return <<<EOD
					$systemid
EOD;
        })->
            addColumn('type', function ($data) {
            if ($data->status === "Refund") {
                $type = "Refund";
            } elseif ($data->status === "Void") {
                $type = "Void";
            } else if($data->status === 'Full Tank Sales') {
                $type = "Full Tank Sales";
            } else if($data->status === 'Outdoor Ewallet Sales') {
                $type = "Outdoor Ewallet Sales";
            } else {
                $type = "Cash Sales";
            }
            return <<<EOD
					$type
EOD;
        })->
            addColumn('location', function ($data) {
            $location = Location::first();
            return <<<EOD
					$location->name
EOD;
        })->
            addColumn('qty', function ($data) {
            $qty = $data->quantity;
            if ($data->status === "Void") {
                $qty = "0.00";
            }
            return <<<EOD
					$qty;
EOD;
        })->

            addColumn('status_color', ' ')->
            editColumn('status_color', function ($row) {
            $status = "none";
            if ($row->status === "Void") {
                $status = "red";
            } elseif ($row->status === "Refund") {
                $status = "rgba(255, 126, 48)";
            }
            if ($row->quantity < 0) {
                $status = "#ff7e30";
            }
            return $status;

        })->
            rawColumns(['action'])->
            escapeColumns([])->
            make(true);
    }

    public function fuelmovementPDF(Request $request)
    {

        $startDate = Carbon::now(); //returns current day
        $location = Location::first();
        $product_id = $request->fuel_prod_id;
        $product_id = $request->fuel_prod_id;
        $startdate = $request->startmonth;
        $enddate = $request->endmonth;
        $superdata['startmonth'] = $request->startmonth;
        $superdata['endmonth'] = $request->endmonth;
        $superdata['fuel_prod_name'] = $request->fuel_prod_name;
        $superdata['date'] = $request->date;
        $superdata['systemid'] = DB::table('product')->where('id', $request->fuel_prod_id)->first();
        $superdata['location'] = $request->location;

        $superdata['data'] = DB::table('fuelmovement')->
            join('prd_ogfuel', 'fuelmovement.ogfuel_id', 'prd_ogfuel.id')->
            where('prd_ogfuel.product_id', $product_id)->
            whereBetween('date', [$startdate, $enddate])->
            select('fuelmovement.*', 'prd_ogfuel.product_id')->
            orderBy('date', 'ASC')->
            get()->map(function ($f) use ($product_id) {
            $yesterday_rec = DB::table('fuelmovement')->
                join('prd_ogfuel', 'fuelmovement.ogfuel_id', 'prd_ogfuel.id')->
                where('prd_ogfuel.product_id', $product_id)->
                whereDate('date', date("Y-m-d", strtotime($f->date . ' -1day')))->
                select('fuelmovement.*', 'prd_ogfuel.product_id')->
                first();

            $f->receipt = DB::table('stockreportproduct')->
                leftjoin('stockreport', 'stockreport.id', 'stockreportproduct.stockreport_id')->
                where('stockreportproduct.product_id', $product_id)->
                whereYear("stockreport.created_at", date("Y", strtotime($f->date)))->
                whereMonth("stockreport.created_at", date("m", strtotime($f->date)))->
                whereDay("stockreport.created_at", date("d", strtotime($f->date)))->
                get()->sum('quantity');
            $f->fulltank_sales = DB::table('fuelfulltank_receiptdetails')
                ->join('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')
                ->join('fuelfulltank_receipt', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
                ->where('fuelfulltank_receiptproduct.product_id', '=', $product_id)
                ->where('fuelfulltank_receipt.status', '!=', 'voided')
                ->whereDate('fuelfulltank_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('fuelfulltank_receiptproduct.quantity');

            $f
                ->sales_pos = DB::table('fuel_receiptdetails')
                ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
                ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
                ->where('fuel_receiptproduct.product_id', '=', $product_id)
                ->where('fuel_receipt.status', '!=', 'voided')
                ->whereDate('fuel_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('fuel_receiptproduct.quantity');

            $f
                ->sales_neg = DB::table('fuel_receiptdetails')
                ->join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receiptdetails.receipt_id')
                ->join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')
                ->leftJoin('receiptrefund', 'receiptrefund.receipt_id', 'fuel_receipt.id')
                ->where('fuel_receiptproduct.product_id', '=', $product_id)
                ->where('fuel_receipt.status', '!=', 'voided')
                ->whereDate('fuel_receiptdetails.created_at', date("Y-m-d", strtotime($f->date)))
                ->sum('receiptrefund.qty');
            $f->sales = $f->fulltank_sales + $f->sales_pos - $f->sales_neg;

            // Log::info("Date ". date("Y-m-d", strtotime($f->date)));

            // Log::info("sales ". $f->sales_pos);
            // Log::info("refund ". $f->sales_neg);
            // Log::info("net ". $f->sales);

            $f->book += $f->receipt - $f->fulltank_sales;

            $f->daily_variance = $f->tank_dip - $f->book;

            //$f->cumulative = ($yesterday_rec->tank_dip ?? 0) - ($yesterday_rec->book ?? 0) + $f->daily_variance;
            $f->cumulative = ($yesterday_rec->cumulative ?? 0) + $f->daily_variance;

            if ($f->book != 0) {
                $f->percentage = abs(($f->cumulative / $f->book) * 100);
            }

            /*
            \Log::info([

            'current tank_dip' => $f->tank_dip,
            'current book' => $f->book,
            'current daily_variance' => $f->daily_variance,

            'yesterday tank_dip' =>$yesterday_rec->tank_dip ?? 0,
            'yesterday book' => $yesterday_rec->book ?? 0 ,
            'yesterday daily_variance' => ($yesterday_rec->tank_dip ?? 0) - ($yesterday_rec->book ?? 0)

            ]);*/
            return $f;
        });

        $dt = explode(' ', $request->date);

        Log::debug('FuelMovement: ' . json_encode($superdata['data']));
        mb_internal_encoding('UTF-8');

        // return view('fuel_movement.fuel_movement_pdf', compact('data'));
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('fuel_movement.fuel_movement_pdf',
                compact('superdata'));

        // download PDF file with download method
        // $pdf = PDF::loadHTML('<p>Hello World!!</p>');
        // return $pdf->stream();
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
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('fuel_movement_rpt_' . strtolower($request->fuel_prod_name) . '_' . strtolower($dt[0]) . '_' . $dt[1] . '.pdf');
    }

}
