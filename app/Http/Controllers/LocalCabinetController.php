<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SetupController;
use App\Http\Controllers\SyncSalesController;
use App\Models\CarPark;
use App\Models\Company;
use App\Models\FuelReceipt;
use App\Models\Location;
use App\Models\PrdOpenitem;
use App\Models\Receipt;
use App\Models\ReceiptDetails;
use App\Models\ReceiptRefund;
use App\Models\Terminal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Log;
use Milon\Barcode\DNS2D;
use Yajra\DataTables\DataTables;
use \App\Classes\SystemID;

class LocalCabinetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Log::info('LocalCabinetController: index START');

        $user = Auth::user();
        $company = Company::first();

        $this->check_personal_shift();
        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $location = Location::first();

        /***update EOD and shift info*/
        //$this->update_eod_ps_info();

        return view('local_cabinet.local_cabinet', compact(
            'company',
            'terminal',
            'location',
            'user'
        ));
    }

    public function localCabinetDataTable(Request $request)
    {


        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $totalRecords = collect(DB::select(DB::raw("

            SELECT
                COUNT(*) AS total
            FROM
            (
                SELECT DATE_FORMAT(c.created_at, '%d%b%y') as created_at  FROM cstore_receipt c
                UNION
                SELECT DATE_FORMAT(f.created_at, '%d%b%y') as created_at FROM fuel_receipt f
                UNION
                SELECT DATE_FORMAT(e.created_at, '%d%b%y') as created_at  FROM evreceipt e
                UNION
                SELECT DATE_FORMAT(fr.created_at, '%d%b%y') as created_at  FROM fuelfulltank_receipt fr
                UNION
                SELECT DATE_FORMAT(h.created_at, '%d%b%y') as created_at  FROM h2receipt h
                UNION
                SELECT DATE_FORMAT(o.created_at, '%d%b%y') as created_at  FROM oew_receipt o
                ORDER BY UNIX_TIMESTAMP(created_at) DESC
            ) AS t
            ;
        ")))->first()->total;

        $query = "
            SELECT  DATE_FORMAT(c.created_at, '%Y-%m-%d') as created_at  FROM cstore_receipt c
            UNION
            SELECT DATE_FORMAT(f.created_at, '%Y-%m-%d') as created_at FROM fuel_receipt f
            UNION
            SELECT DATE_FORMAT(e.created_at, '%Y-%m-%d') as created_at  FROM evreceipt e
            UNION
            SELECT DATE_FORMAT(fr.created_at, '%Y-%m-%d') as created_at  FROM fuelfulltank_receipt fr
            UNION
            SELECT DATE_FORMAT(h.created_at, '%Y-%m-%d') as created_at  FROM h2receipt h
            UNION
            SELECT  DATE_FORMAT(o.created_at, '%Y-%m-%d') as created_at  FROM oew_receipt o
            ORDER BY created_at DESC
            LIMIT $rowperpage OFFSET $start
            ;
        ";
        $dataReceipt = collect(DB::select(DB::raw($query)));



        $receipt = collect();

        $dataReceipt->map(function ($z) use ($receipt) {
            $packet = collect();
            $packet->date =  $z->created_at;
            $packet->day = $z->created_at;
            $packet->ev = 0;
            $packet->opt = 0;
            $packet->created_at = strtotime($packet->date);
            $packet->sortdate = strtotime($z->created_at);

            $packet->shift = DB::table('loginout')->
             whereDate('created_at', date("Y-m-d", strtotime($z->created_at)))->
            count();
            $packet->count_receipt_fuel = "List";
            $packet->count_receipt_cstore = "List";

            $receipt->push($packet);

            // Log::info('AFTER receipt push packet');
        });

        // Log::info('AFTER dataReceipt map function');

        $receipt = $receipt->sortBy('sortdate', SORT_NATURAL, true);

        return Datatables::of($receipt)->
            setOffset($start)->
            addIndexColumn()->
         /*    addIndexColumn()->addColumn('eod', function ($receipt) {
                $day_passed = "'" . date('dMy', strtotime($receipt->date)) . "'";
                $eod_url = '<a href="#" onclick="eod_summarylist(' . $day_passed . ')" style="margin:0; text-decoration: none;">' . date('dMy', strtotime($receipt->date)) . '</a>';
                return <<<EOD
					$eod_url
EOD;
            })->
            addColumn('shift', function ($receipt) {
                $day_passed = date('dMy', strtotime($receipt->date));
                $shift_url = '<a href="' . route("pshift.list", $day_passed) . '" target="_blank" style="margin:0; text-decoration: none;">' . $receipt->shift . '</a>';
                return <<<EOD
					$shift_url
EOD;
            })->*/

            addColumn('nshift', function ($receipt) {
                $day_passed = date('dMy', strtotime($receipt->date));
                $shift_url = '<a href="' . route("local_cabinet.nshift", ['date' => $day_passed]) . '" target="_blank" style="margin:0; text-decoration: none;">' . $day_passed . '</a>';
                return <<<EOD
					$shift_url
EOD;
            })->
           /*  addColumn('hydrogen', function ($receipt) {
                $hyd_url = '<a href="' . url("h2-receipt-list") . '" target="_blank" onclick="" style="margin:0; text-decoration: none;">List</a>';
                return <<<EOD
					$hyd_url
EOD;
            })->addColumn('ev', function ($receipt) {
                $id = "'id'";
                $ev_url = '<a href="' . route('ev-receipt-list') . '" target="_blank" style="margin:0; text-decoration: none;">List</a>';
                return <<<EOD
					$ev_url
EOD;
            })->addColumn('ssrv', function ($receipt) {
                $id = "'id'";
                $ssrv_url = '<a href="' . route('outdoor-ewallet-list') . '" target="_blank" style="margin:0; text-decoration: none;">List</a>';
                return <<<EOD
					$ssrv_url
EOD;
            })->addColumn('opt', function ($receipt) {
                $id = "'id'";
                $ev_url = '<a href="' . route('local_cabinet.optList', ['date' => date('Y-m-d', $receipt->created_at)]) . ' " target="_blank" style="text-decoration: none;">List</a>';
                return <<<EOD
					$ev_url
EOD;
            })-> */
            addColumn('fuel', function ($receipt) {
                $fuel_url = '<a href="' . route('fuel-receipt-list', ['date' => date('Y-m-d', $receipt->created_at)]) . '" target="_blank" style="text-decoration: none;">' . $receipt->count_receipt_fuel . '</a>';
                return <<<EOD
					$fuel_url
EOD;
            })->
            addColumn('receipt_cstore', function ($receipt) {
                $day_passed = "'" . $receipt->day . "'";
                $cstore_url = '<a href="' . route('local_cabinet.cstore_receipt_landing', $receipt->day) . '" target="_blank" xonclick="receipt_list(' . $day_passed . ')" style="text-decoration: none;">' . $receipt->count_receipt_cstore . '</a>';
                return <<<EOD
					$cstore_url
EOD;
            })->
            setTotalRecords($totalRecords)->
            escapeColumns([])->
            make(true);
    }

    public function update_eod_ps_info()
    {
        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $user = Auth::user();
        $company = Company::first();
        $location = \App\Models\Location::first();

        $brancheoddata = DB::table('brancheod')->whereDate('created_at', '=', date('Y-m-d'))->first();

        $loginOut = FuelReceipt::getCurrentLoginOut();
        $dataPshiftdetails = DB::table('pshiftdetails')->
			where('pshift_id', '=', $loginOut->shift_id)->first();

        $eoddetail_id = null;

        if (!empty($brancheoddata)) {
            try {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);
            } catch (\Exception $e) {
                \Log::error([
                    'Error' => $e->getMessage(),
                    "File" => $e->getFile(),
                    "Line" => $e->getLine(),
                ]);
                //abort(123);
            }

            Log::info('dataForEod=' . json_encode($dataForEod));
            Log::info('brancheoddata=' . json_encode($brancheoddata));

            $eod_detail = DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->first();

            if (empty($eod_detail)) {
                $idEoddetail = DB::table('eoddetails')->insertGetId([
                    "eod_id" => $brancheoddata->id,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->update([
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $eoddetail = DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->first();

            Log::debug('eoddetail=' . json_encode($eoddetail));

            if (!empty($eoddetail)) {
                // print_r($eoddetail);
                // echo "<br>";
                // print_r($brancheoddata);
                // die($brancheoddata->id."=".$eoddetail_id);

                $eoddetail_id = $eoddetail->id;
            }
        } else {
            $brancheod = DB::table('brancheod')->insertGetId([
                "eod_presser_user_id" => $user->id,
                "location_id" => $location->id,
                "terminal_id" => $terminal->id,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $brancheoddata = DB::table('brancheod')->where('id', '=', $brancheod)->first();

            Log::info("brancheoddata=" . json_encode($brancheoddata));

            $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);

            $idEoddetail = DB::table('eoddetails')->insertGetId([
                "eod_id" => $brancheod,
                "startdate" => date('Y-m-d'),
                "total_amount" => $dataForEod["eodTotal"],
                "rounding" => $dataForEod["eodRound"],
                "sales" => $dataForEod["eodItemAmount"],
                "sst" => $dataForEod["eodTax"],
                "discount" => $dataForEod["eodDiscount"],
                "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                "cash_change" => $dataForEod["eodChange"],
                "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                "opt" => 0,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            Log::info('idEoddetail=' . json_encode($idEoddetail));

            $eoddetail_id = $idEoddetail;
            // print_r($brancheoddata);
        }

        if ($eoddetail_id != null) {
            $currentLoginOut = FuelReceipt::getCurrentLoginOut();
            DB::table('pshift')->where(
                'id',
                $currentLoginOut->shift_id
            )->update([
                'eoddetails_id' => $eoddetail_id,
            ]);

            try {
                $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();
            } catch (\Exception $e) {
                \Log::error([
                    'Error' => $e->getMessage(),
                    "File" => $e->getFile(),
                    "Line" => $e->getLine(),
                ]);
				// This is the error that pops up with LocalCabinet without
				// a fresh session after midnight
                abort(404);
            }

            if ($dataPshiftdetails == null) {

                DB::table('pshiftdetails')->insert([
                    "pshift_id" => $loginOut->shift_id,
                    "eoddetails_id" => $eoddetail_id,
                    "startdate" => date('Y-m-d H:i:s'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                DB::table('pshiftdetails')->where('id', $dataPshiftdetails->id)->update([
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"] + $dataForEod["totalCashRound"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"] + $dataForEod["totalCreditCardRound"],
                    "wallet" => $dataForEod["eodWallet"] + $dataForEod["totalWalletRound"],
                    "creditac" => $dataForEod["eodcreditAccount"] + $dataForEod["totalCreditAcRound"],
                    "oew" => $dataForEod["eodOew"] + $dataForEod["totalOewRound"],
                    "opt" => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function update_shift_cstore($shift_id, $user_id, $from, $to)
    {
        $terminals = DB::table('cstore_receipt')->distinct()->get(['terminal_id']);

        Log::info('update_ps_cstore: terminals=' . json_encode($terminals));

        foreach ($terminals as $terminal) {
            $this->update_ps_cstore($terminal->terminal_id, $shift_id, $user_id, $from, $to);
        }
    }

    public function update_ps_cstore($terminal_id, $shift_id, $user_id, $from, $to)
    {

        $cstore_total = 0;

        $total = DB::table('cstore_receiptdetails')->
            join('cstore_receipt', 'cstore_receiptdetails.receipt_id', '=', 'cstore_receipt.id')->
            whereNotIn('cstore_receipt.status', ['refunded', 'voided'])->
            where('cstore_receipt.terminal_id', $terminal_id)->
            where('cstore_receipt.staff_user_id', '=', $user_id)->
            whereBetween('cstore_receiptdetails.created_at', [$from, $to])->
            sum('total');

        // Log::info('update_ps_cstore: total='.json_encode($total));

        $refunded = DB::table('cstore_receiptdetails')->
            join('cstore_receipt', 'cstore_receiptdetails.receipt_id', '=', 'cstore_receipt.id')->
            join('cstore_receiptrefund', 'cstore_receiptrefund.cstore_receipt_id', '=', 'cstore_receipt.id')->
            where('cstore_receipt.status', '=', 'refunded')->
            where('cstore_receipt.terminal_id', $terminal_id)->
            where('cstore_receipt.staff_user_id', '=', $user_id)->
            whereBetween('cstore_receiptdetails.created_at', [$from, $to])->
            sum('cstore_receiptrefund.refund_amount');

        // Log::info('update_ps_cstore: refunded='.json_encode($refunded));

        if ($total > 0) {
            $grand_total = $total - $refunded;

            // Log::info('update_ps_cstore: grand_total='.$grand_total);

            $shift_data = DB::table('pshiftdetails')->
            where('pshift_id', '=', $shift_id)->
            first();

            // Log::info('update_ps_cstore: shift_data='.
            // json_encode($grand_total));

            if ($shift_data == null) {
                $ret = DB::table('pshiftdetails')->
                insert([
                    "pshift_id" => $shift_id,
                    "cstore" => $grand_total,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Log::info('update_ps_cstore: INSERT='.json_encode($ret));

            } else {
                $ret = DB::table('pshiftdetails')->
                where('pshift_id', '=', $shift_id)->
                update([
                    "cstore" => $grand_total,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Log::info('update_ps_cstore: INSERT='.json_encode($ret));
            }
        }
    }

    public function update_terminal_shift_fuel($shift_id, $user_id, $from, $to)
    {
        $terminals = DB::table('fuel_receipt')->distinct()->get(['terminal_id']);

        // Log::info('update_shift_fuel: terminals='.json_encode($terminals));

        foreach ($terminals as $terminal) {
            $this->update_shiftfuel($terminal->terminal_id, $shift_id, $user_id, $from, $to);
        }
    }

    public function update_shiftfuel($terminal_id, $shift_id, $user_id, $from, $to)
    {

        $shift_data = DB::table('pshiftfuel')->
        where('pshift_id', '=', $shift_id)->
        first();

        $products = DB::table('prd_ogfuel')->
        join('product', 'product.id', '=', 'prd_ogfuel.product_id')->
        where('prd_ogfuel.status', 'active')->
        select('product.id', 'product.name', 'prd_ogfuel.id as ogfuel_id')->
        get();

        // Log::debug('update_shiftfuel: products='.json_encode($products));

        $fuel_products = [];

        foreach ($products as $product) {
            // Log::debug('update_shiftfuel: productname='.json_encode($product->name));

            $total = DB::table('fuel_receiptdetails')->
                join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=',
                 'fuel_receiptdetails.receipt_id')->
                join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=',
                 'fuel_receipt.id')->
                join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=',
                'fuel_receiptdetails.receipt_id')->
                where('fuel_receiptproduct.product_id', '=', $product->id)->
                where('fuel_receipt.terminal_id', $terminal_id)->
                where('fuel_receipt.status', '!=', 'voided')->
                where('fuel_receiptlist.status', '!=', 'refunded')->
                where('fuel_receipt.staff_user_id', '=', $user_id)->
                whereNotIn('fuel_receiptlist.status', ['refunded', 'voided'])->
                whereBetween('fuel_receiptdetails.created_at', [$from, $to])->
                sum('fuel_receiptdetails.total');

            // Log::info('update_shiftfuel: total='.json_encode($total));

            $ft_total = DB::table('fuelfulltank_receiptdetails')->
                join('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id',
                 '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')->
                join('fuelfulltank_receipt', 'fuelfulltank_receiptproduct.fulltank_receipt_id',
                 '=', 'fuelfulltank_receipt.id')->
                join('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id',
                 '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')->
                where('fuelfulltank_receiptproduct.product_id', '=', $product->id)->
                where('fuelfulltank_receipt.terminal_id', $terminal_id)->
                where('fuelfulltank_receipt.status', '!=', 'voided')->
                where('fuelfulltank_receiptlist.status', '!=', 'refunded')->
                where('fuelfulltank_receipt.staff_user_id', '=', $user_id)->
                whereNotIn('fuelfulltank_receiptlist.status', ['refunded', 'voided'])->
                whereBetween('fuelfulltank_receiptdetails.created_at', [$from, $to])->
                sum('fuelfulltank_receiptdetails.total');

            // Log::info('update_shiftfuel: ft_total='.json_encode($ft_total));

            $rounding = DB::table('fuel_receiptdetails')->
                join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=',
                 'fuel_receiptdetails.receipt_id')->
                join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=',
                 'fuel_receipt.id')->
                where('fuel_receiptproduct.product_id', '=', $product->id)->
                where('fuel_receipt.status', '!=', 'voided')->
                where('fuel_receipt.terminal_id', $terminal_id)->
                where('fuel_receipt.staff_user_id', '=', $user_id)->
                whereBetween('fuel_receiptdetails.created_at', [$from, $to])->
                sum('fuel_receiptdetails.rounding');

            // Log::info('update_shiftfuel: rounding='.json_encode($rounding));

            $ft_rounding = DB::table('fuelfulltank_receiptdetails')->
                join('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receiptdetails.fulltank_receipt_id')->
                join('fuelfulltank_receipt', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=',
                'fuelfulltank_receipt.id')->
                where('fuelfulltank_receiptproduct.product_id', '=', $product->id)->
                where('fuelfulltank_receipt.status', '!=', 'voided')->
                where('fuelfulltank_receipt.terminal_id', $terminal_id)->
                where('fuelfulltank_receipt.staff_user_id', '=', $user_id)->
                whereBetween('fuelfulltank_receiptdetails.created_at', [$from, $to])->
                sum('fuelfulltank_receiptdetails.rounding');

            // Log::info('update_shiftfuel: ft_rounding='.json_encode($ft_rounding));

            $newsales_total = DB::table('fuel_receiptdetails')->
                join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=',
                 'fuel_receiptdetails.receipt_id')->
                join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')->
                join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=',
                 'fuel_receiptdetails.receipt_id')->
                where('fuel_receiptproduct.product_id', '=', $product->id)->
                where('fuel_receiptlist.status', '=', 'refunded')->
                where('fuel_receipt.terminal_id', $terminal_id)->
                where('fuel_receipt.staff_user_id', '=', $user_id)->
                whereBetween('fuel_receiptlist.created_at', [$from, $to])->
                sum('fuel_receiptlist.newsales_item_amount');

            // Log::info('update_shiftfuel: newsales_total='.json_encode($newsales_total));

            $newsales_rounding = DB::table('fuel_receiptdetails')->
                join('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=',
                'fuel_receiptdetails.receipt_id')->
                join('fuel_receipt', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')->
                join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=',
                 'fuel_receiptdetails.receipt_id')->
                where('fuel_receiptproduct.product_id', '=', $product->id)->
                where('fuel_receiptlist.status', '=', 'refunded')->
                where('fuel_receipt.terminal_id', $terminal_id)->
                where('fuel_receipt.staff_user_id', '=', $user_id)->
                whereBetween('fuel_receiptlist.created_at', [$from, $to])->
                sum('fuel_receiptlist.newsales_rounding');

            // Log::info('update_shiftfuel: newsales_rounding='.json_encode($newsales_rounding));

            if ($total > 0 || $ft_total > 0 || $newsales_total > 0) {
                $shift_data = DB::table('pshiftfuel')->where('pshift_id', '=', $shift_id)->where('ogfuel_id', $product->ogfuel_id)->first();

                // Log::info('update_shiftfuel: shift_data='.json_encode($shift_data));

                if ($shift_data == null) {
                    $ret = DB::table('pshiftfuel')->insert([
                        "pshift_id" => $shift_id,
                        "ogfuel_id" => $product->ogfuel_id,
                        "sales" => $total +
                        $ft_total +
                        $rounding +
                        $ft_rounding +
                        $newsales_total +
                        $newsales_rounding,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    // Log::info('update_shiftfuel: null shift ret='.json_encode($ret));

                } else {
                    $ret = DB::table('pshiftfuel')->whereId($shift_data->id)->update([
                        "sales" => $total +
                        $ft_total +
                        $rounding +
                        $ft_rounding +
                        $newsales_total +
                        $newsales_rounding,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    // Log::info('update_shiftfuel: existsing shift ret='.json_encode($ret));
                }
            }
        }
    }


    public function localcabinet_nshift(Request $request)
    {
        $company = Company::first();
        $approved_at = $company->approved_at;
        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
			where('client_ip', $client_ip)->first();

        $location = Location::first();
        $date = $request->date;
        return view(
            'nshift.nshift',
            compact('terminal', 'location', 'date', 'approved_at')
        );
    }

    public function get_nshift_details(Request $request)
    {

        $record = DB::table('nshift')->
            whereId($request->nshift_id)->
            first();

        return response()->json($record);
    }

    public function update_nshift_details(Request $request)
    {
        $id = $request->id;
        if (!isset($id)) {
            return response([], 404);
        }
        $update = DB::table('nshift')->
            whereId($id)->
            update([
            'cash_in' => $request->cash_in,
            'cash_out' => $request->cash_out,
            'sales_drop' => $request->sales_drop,
            'actual' => $request->drawer_amount,
            "updated_at" => date('Y-m-d H:i:s'),
        ]);

        $response = collect();
        $response->update = $update;
        return $response;
    }

    public function nshift_datatable(Request $request)
    {
        $date = request()->date;
        $start = $request->start;
        $length = $request->length;
        $d_start = date('Y-m-d H:i:s',strtotime($date));
        $d_end = date('Y-m-d 23:59:59', strtotime($date));

        $totalRecords = DB::table('nshift')
            ->leftjoin('users', 'users.systemid', 'nshift.staff_systemid')
            ->select(
                'nshift.*',
                'nshift.staff_systemid',
                'nshift.staff_name as staff_name',
                'users.systemid as staff_id'

            )->
            whereBetween('nshift.in', [$d_start, $d_end])->
            count();

        $data = DB::table('nshift')
            ->leftjoin('users', 'users.systemid', 'nshift.staff_systemid')

            ->select(
                'nshift.*',
                'nshift.staff_systemid',
                'nshift.staff_name as staff_name',
                'users.systemid as staff_id')->
            skip($start)->take($length)->
            whereBetween('nshift.in',[$d_start,$d_end])->
            orderByDesc('nshift.in')->
            get();

        return Datatables::of($data)->
            setOffset($start)->
            addIndexColumn()->
            addColumn('in', function ($dt) {
                $date = "";
                if (!is_null($dt->in)) {
                    $date = date_format(date_create($dt->in), 'dMy H:i:s');
                }

                return $date;
            })->
            addColumn('out', function ($dt) {
                $date = "";
                if (!is_null($dt->out)) {
                    $date = date_format(date_create($dt->out), 'dMy H:i:s');
                }

                return $date;
            })->
            addColumn('staff_id', function ($dt) {
                return $dt->staff_id;
            })->
            addColumn('staff_name', function ($dt) {
                return $dt->staff_name;
            })->

            addColumn('report', function ($dt) {
                return '<a  href="javascript:void(0)" onClick="downLoadPdf('.$dt->id. ')"  style="filter: background: red; /*pointer-events: none; */cursor: pointer;" data-row="0" class="delete">
                    
                <img width="25px" src="' . asset("images/pinkcrab_50x50.png") . '"" alt="">
                    <span class="d-none spinner-border spinner-border-sm"
						 	role="status" aria-hidden="true"
							style="z-index:2; position: fixed; margin-top: 3px;
							margin-left:7px"></span>
                </a>';

            })->
            addColumn('action', function ($dt) {
                return '<a  href="javascript:void(0)" onClick="openShiftModal(' . $dt->id . ')"  style=" filter: background: red; /*pointer-events: none; */cursor: pointer;" data-row="0" class="delete"> <img width="25px" src="' . asset("images/bluecrab_50x50.png") . '"" alt=""> </a>';
            })->
            escapeColumns([])
            ->setTotalRecords($totalRecords)
            ->make(true);
    }

    public function cstore_receipt_landing(Request $request)
    {
        $company = Company::first();
        $approved_at = $company->approved_at;
        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

        $location = Location::first();

        $date = $request->date;

        return view(
			'cstore.cstore_receiptlist',
            compact( 'terminal', 'location', 'date', 'approved_at')
        );
    }

    public function cstoreListTable(Request $request)
    {

        $date = $request->date;
        $draw = $request->get('draw');
        $start = empty($request->get("start")) ? 0 : $request->get("start");
        $rowperpage = $request->get("length");
        $totalRecords = DB::table('cstore_receipt')->
            whereDay('created_at', '=', date('d', strtotime($date)))->
            whereMonth('created_at', '=', date('m', strtotime($date)))->
            whereYear('created_at', '=', date('Y', strtotime($date)))->
            orderBy("id", "desc")->
            count();

        $receipt = DB::table('cstore_receipt')->
            whereDay('created_at', '=', date('d', strtotime($date)))->
            whereMonth('created_at', '=', date('m', strtotime($date)))->
            whereYear('created_at', '=', date('Y', strtotime($date)))->
            offset($start)->limit($rowperpage)->
            orderBy("id", "desc")->
            get();

        $receipt->map(function ($z) {
            $z->is_refunded = !empty(DB::table('cstore_receiptrefund')->
                where('cstore_receipt_id', $z->id)->
                first());
        });

        return Datatables::of($receipt)->
            setOffset($start)->
            addIndexColumn()->
            editColumn('created_at', function ($data) {
                return date('dMy H:i:s', strtotime($data->created_at ?? ''));
            })->
            editColumn('systemid', function ($row) {
                $systemid = ["systemid" => $row->systemid, "id" => $row->id];
                return $systemid;
            })->
            addColumn('total', function ($row) {
                $total = number_format(((($row->cash_received / 100 -
                    $row->cash_change / 100 + ((5 * round(($row->cash_received - $row->cash_change) / 5)) - ($row->cash_received - $row->cash_change)) / 100)) ?? "2"), 2);
                return $total;
            })->
            addColumn('status_color', ' ')->
            editColumn('status_color', function ($row) {
                $status = $row->status;
                if ($row->status == "voided") {
                    $status = "red";
                }
                if ($row->status == "refunded") {
                    $status = "#ff7e30";
                }
                return $status;
            })->
            setTotalRecords($totalRecords)->
            rawColumns(['total'])->
            make(true);
    }
    public function cstore_table_search_ptype(Request $request)
    {
        $date = $request->date;
        $search = $ptype = $request->ptype;

        if (strpos($search, 'creditac') !== false) {
            $ptype = 'creditac';
        }
        $receipt = DB::table('cstore_receipt')->whereDay(
            'created_at',
            '=',
            date('d', strtotime($date))
        )->where('cstore_receipt.payment_type', 'like', $ptype . '%')->
        whereMonth('created_at', '=', date('m', strtotime($date)))->whereYear('created_at', '=', date('Y', strtotime($date)))->orderBy("id", "desc")->get();

        $receipt->map(function ($z) {
            $z->is_refunded = !empty(DB::table('cstore_receiptrefund')->
            where('cstore_receipt_id', $z->id)->first());
        });

        return Datatables::of($receipt)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return date('dMy H:i:s', strtotime($data->created_at ?? ''));
            })
            ->editColumn('systemid', function ($row) {
                $systemid = ["systemid" => $row->systemid, "id" => $row->id];
                return $systemid;
            })
            ->addColumn('total', function ($row) {
                $total = number_format(((($row->cash_received / 100 - $row->cash_change / 100 + ((5 * round(($row->cash_received - $row->cash_change) / 5)) - ($row->cash_received - $row->cash_change)) / 100)) ?? "2"), 2);
                return $total;
            })->addColumn('status_color', ' ')->editColumn('status_color', function ($row) {
            $status = $row->status;
            if ($row->status == "voided") {
                $status = "red";
            }
            if ($row->status == "refunded") {
                $status = "#ff7e30";
            }
            return $status;
        })->rawColumns(['total'])
            ->make(true);
    }

    public function cstoreVoidReceipt(Request $request)
    {
        $user = Auth::user();
        \Illuminate\Support\Facades\Log::info(["request" => $request->all()]);
        DB::table('cstore_receipt')->where("id", $request->receiptid)->update([
            "voided_at" => now(),
            "void_user_id" => $user->id,
            "void_reason" => $request->reason_void,
            "status" => "voided",
        ]);

        $cstore_receipt = DB::table('cstore_receipt')->where("id", $request->receiptid)->first();
        $brancheoddata = DB::table('brancheod')->whereDate('created_at', '=', date('Y-m-d', strtotime($cstore_receipt->created_at)))->first();
        \Illuminate\Support\Facades\Log::info(json_encode($brancheoddata));

        if ($brancheoddata != null) {
            $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);

            // Log::debug('***dataForEod = ' . json_encode($dataForEod));
            DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->update([
                "startdate" => date('Y-m-d'),
                "total_amount" => $dataForEod["eodTotal"],
                "rounding" => $dataForEod["eodRound"],
                "sales" => $dataForEod["eodItemAmount"],
                "sst" => $dataForEod["eodTax"],
                "discount" => $dataForEod["eodDiscount"],
                "cash" => $dataForEod["eodCash"],
                "cash_change" => $dataForEod["eodChange"],
                "creditcard" => $dataForEod["eodCreditCard"],
                "wallet" => $dataForEod["eodWallet"],
                "creditac" => $dataForEod["eodcreditAccount"],
                "opt" => 0,
                "oew" => $dataForEod["eodOew"],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $eoddetail = DB::table('eoddetails')->
                where('eod_id', $brancheoddata->id)->
                first();

            $loginOut = DB::table('loginout')->
                where("user_id", $cstore_receipt->staff_user_id)->
                where("login", "<=", $cstore_receipt->created_at)->
                where("logout", ">", $cstore_receipt->created_at)->
                first();

            if ($loginOut == null) {
                $loginOut = DB::table('loginout')->
                    where("user_id", $cstore_receipt->staff_user_id)->
                    where("login", "<=", $cstore_receipt->created_at)->
                    where("logout", null)->
                    first();
            }

            $eoddetail_id = $eoddetail->id;
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->
                first();

            if ($eoddetail_id != null) {
                $dataForEod = FuelReceipt::getUserReceiptValueWithoutVoid($loginOut);

                if ($dataPshiftdetails != null) {

                    DB::table('pshiftdetails')->
                    where('id', $dataPshiftdetails->id)->
                    update([
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"],
                        "wallet" => $dataForEod["eodWallet"],
                        "creditac" => $dataForEod["eodcreditAccount"],
                        "opt" => 0,
                        "oew" => $dataForEod["eodOew"],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        return true;
    }

    public function checkNric(Request $request)
    {
        $nric = $request->nric;
        $data = array('nric' => $nric);
        $curl = curl_init();
        $murl = env('MOTHERSHIP_URL');
        curl_setopt_array($curl, array(
            CURLOPT_URL => $murl . '/api/checkNric',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $response = $err;
        }

        return response()->json($response, 200);
    }

    public function eodSummaryPopup(Request $request)
    {
        $eod_date = $request->eod_date;
        $date_eod = "";
        if ($request->eod_date) {
            $date_eod = date_create($request->eod_date);
            $date_eod = date_format($date_eod, 'Y-m-d');
        } else {
            $date_eod = date('Y-m-d');
        }

        $this->update_eod_ps_info();

        $user = Auth::user();
        $location = Location::first();
        $todaydate = date('Y-m-d');
        Log::info('Date of EOD: ' . $date_eod);

        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

        Log::info(["terminal" => $terminal]);

        $eoddetailsdata = DB::table('eoddetails')->whereDate('startdate', date(
            'Y-m-d',
            strtotime($date_eod)
        ))->first();

        Log::info(["eoddetailsdata" => $eoddetailsdata]);

        $receipts = Receipt::with('receiptdetails')->whereDate(
            'receipt.created_at',
            '=',
            date('Y-m-d', strtotime($date_eod))
        )->where('receipt.status', 'voided')->get();

        Log::info(["receipts" => $receipts]);

        $reverseAmount = 0;
        $reverseCash = 0;
        $reverseCard = 0;
        $reverseWallet = 0;
        $reverseTax = 0;
        $reverseRound = 0;

        foreach ($receipts as $receipt) {
            if ($receipt->payment_type == 'creditcard') {
                $reverseCard += $receipt->receiptdetails->creditcard;
            } elseif ($receipt->payment_type == 'wallet') {
                $reverseWallet += $receipt->receiptdetails->wallet;
            } else {
                $reverseCash += $receipt->receiptdetails->cash_received - $receipt->receiptdetails->change;
            }
            $reverseTax += $receipt->receiptdetails->sst;
            $reverseRound += $receipt->receiptdetails->rounding;
        }

        $reverseAmount += ($reverseCard + $reverseCash + $reverseWallet) - $reverseTax - $reverseRound;

        $refund_data = DB::table('receiptrefund')->
        join('receipt','receipt.id', 'receiptrefund.receipt_id')->
        whereDate('receipt.created_at', '=', $date_eod)->
        get()->
        sum('refund_amount');

        \Log::info(['refund total' => $refund_data]);
        $refund_p_amount = $refund_data;
        $tax_percent = ($terminal->tax_percent ?? 6);
        $refund_data = ($refund_data) / (1 + ($tax_percent / 100));
        $refund_sst = $refund_p_amount - $refund_data;
        $refund_round = $refund_data + $refund_sst;
        $refund_round = (float) number_format($this->round_amount($refund_round) / 100, 2);

        $fueleodnewsalesdata = DB::table('fuel_receipt')
            ->join('fuel_receiptlist', 'fuel_receipt.id', 'fuel_receiptlist.fuel_receipt_id')
            ->join('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')
            ->whereDate('fuel_receipt.created_at', '=', date('Y-m-d', strtotime($date_eod)))
            ->where('fuel_receipt.status', 'refunded')
            ->select(
                'fuel_receipt.id',
                'fuel_receipt.systemid',
                'fuel_receipt.payment_type',
                'fuel_receipt.status',
                'fuel_receipt.service_tax',
                'fuel_receiptlist.total',
                'fuel_receiptlist.refund',
                'fuel_receiptlist.newsales_item_amount',
                'fuel_receiptlist.newsales_tax',
                'fuel_receiptlist.newsales_rounding',
                'fuel_receiptdetails.total',
                'fuel_receiptdetails.rounding',
                'fuel_receiptdetails.item_amount',
                'fuel_receiptdetails.sst',
            )->get();

        $fueleodnewsalescreditcard = 0;
        $fueleodnewsalescreditac = 0;
        $fueleodnewsalescash = 0;
        $fueleodnewsaleswallet = 0;
        $fueleodnewsalesoew = 0;
        $fueleodnewsalessales = 0;
        $fueleodnewsalessst = 0;
        $fueleodnewsalesrounding = 0;
        $fueleodnewsalesccrounding = 0;
        $fueleodnewsalescarounding = 0;
        $fueleodnewsalescashrounding = 0;
        $fueleodnewsaleswalletrounding = 0;
        $fueleodnewsalesoewrounding = 0;

        foreach ($fueleodnewsalesdata as $fuelnewsales) {

            switch ($fuelnewsales->payment_type) {
                case "creditcard":
                    $fueleodnewsalescreditcard +=
                        ($fuelnewsales->newsales_item_amount +
                        $fuelnewsales->newsales_tax +
                        $fuelnewsales->newsales_rounding);
                    $fueleodnewsalesccrounding += $fuelnewsales->newsales_rounding;
                    break;

                case "creditac":
                    $fueleodnewsalescreditac +=
                        ($fuelnewsales->newsales_item_amount +
                        $fuelnewsales->newsales_tax +
                        $fuelnewsales->newsales_rounding);
                    $fueleodnewsalescarounding += $fuelnewsales->newsales_rounding;
                    break;

                case "cash":
                    $fueleodnewsalescash +=
                        ($fuelnewsales->newsales_item_amount +
                        $fuelnewsales->newsales_tax +
                        $fuelnewsales->newsales_rounding);
                    $fueleodnewsalescashrounding += $fuelnewsales->newsales_rounding;
                    break;

                case "wallet":
                    $fueleodnewsaleswallet +=
                        ($fuelnewsales->newsales_item_amount +
                        $fuelnewsales->newsales_tax +
                        $fuelnewsales->newsales_rounding);
                    $fueleodnewsaleswalletrounding += $fuelnewsales->newsales_rounding;
                    break;

                case "oew":
                    $fueleodnewsalesoew +=
                        ($fuelnewsales->newsales_item_amount +
                        $fuelnewsales->newsales_tax +
                        $fuelnewsales->newsales_rounding);
                    $fueleodnewsalesoewrounding += $fuelnewsales->newsales_rounding;
                    break;
                default:
            }
        }

        $h2eodrefundsdata = DB::table('h2receipt')
            ->join('h2receiptlist', 'h2receipt.id', 'h2receiptlist.h2receipt_id')
            ->join('h2receiptdetails', 'h2receipt.id', 'h2receiptdetails.receipt_id')
            ->whereDate('h2receipt.created_at', '=', date('Y-m-d', strtotime($date_eod)))
            ->where('h2receipt.status', 'refunded')
            ->select(
                'h2receipt.id',
                'h2receipt.systemid',
                'h2receipt.payment_type',
                'h2receipt.status',
                'h2receipt.service_tax',
                'h2receiptlist.total',
                'h2receiptlist.refund',
                'h2receiptdetails.total',
                'h2receiptdetails.rounding',
                'h2receiptdetails.item_amount',
                'h2receiptdetails.sst',
            )->get();

        $h2refundscreditcard = 0;
        $h2refundscreditac = 0;
        $h2refundscash = 0;
        $h2refundswallet = 0;
        $h2refundsoew = 0;
        $h2refundsnetsales = 0;
        $h2refundssst = 0;
        $h2refundsrounding = 0;

        foreach ($h2eodrefundsdata as $h2refund) {

            switch ($h2refund->payment_type) {
                case "creditcard":
                    $h2refundscreditcard += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundsrounding += $h2refund->rounding;
                    $h2refundssst += $h2refund->sst;
                    break;
                case "creditac":
                    $h2refundscreditac += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundsrounding += $h2refund->rounding;
                    $h2refundssst += $h2refund->sst;
                    break;
                case "cash":
                    $h2refundscash += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundsrounding += $h2refund->rounding;
                    $h2refundssst += $h2refund->sst;
                    break;
                case "wallet":
                    $h2refundswallet += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundsrounding += $h2refund->rounding;
                    $h2refundssst += $h2refund->sst;
                    break;
                case "oew":
                    $h2refundsoew += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundsrounding += $h2refund->rounding;
                    $h2refundssst += $h2refund->sst;
                    break;
            }
        }

        \Log::info([
            'sst' => $refund_sst,
            'tax percent' => ($terminal->tax_percent ?? 6),
            'refund_round' => $refund_round,
            'amount' => $refund_data,
        ]);

        $sst_tax = ($eoddetailsdata->sst ?? 0) - $reverseTax;
        $round = ($eoddetailsdata->rounding ?? 0) - $reverseRound;

        $totalrefundsnetsales = $h2refundsnetsales;
        $totalrefundsrounding = $h2refundsrounding;
        $totalrefundssst = $h2refundssst;
        $totalrefundscreditcard = $h2refundscreditcard;
        $totalrefundscreditac = $h2refundscreditac;
        $totalrefundscash = $h2refundscash;
        $totalrefundswallet = $h2refundswallet;
        $totalrefundsoew = $h2refundsoew;

        // eod value
        Log::info("EODDATA" . json_encode($eoddetailsdata));
        $eodSales = !empty($eoddetailsdata->sales) ? ($eoddetailsdata->sales - $totalrefundsnetsales) : null;
        $eodRound = !empty($eoddetailsdata->rounding) ? ($eoddetailsdata->rounding - $totalrefundsrounding) : null;
        $eodCreditCard = !empty($eoddetailsdata->creditcard) ? ($eoddetailsdata->creditcard - $totalrefundscreditcard + $fueleodnewsalesccrounding) : null;
        $eodCash = !empty($eoddetailsdata->cash) ? ($eoddetailsdata->cash - $totalrefundscash + $fueleodnewsalescashrounding) : null;
        $eodWallet = !empty($eoddetailsdata->wallet) ? ($eoddetailsdata->wallet - $totalrefundswallet + $fueleodnewsaleswalletrounding) : null;
        $eodcreditAccount = !empty($eoddetailsdata->creditac) ? ($eoddetailsdata->creditac - $totalrefundscreditac + $fueleodnewsalescarounding) : null;
        $eodOew = !empty($eoddetailsdata->oew) ? ($eoddetailsdata->oew - $totalrefundsoew + $fueleodnewsalesoewrounding) : null;
        $eodTax = !empty($eoddetailsdata->sst) ? ($eoddetailsdata->sst - $totalrefundssst) : null;
        //end eod value

        $user = Auth::user();
        $company = Company::first();
        $location = Location::first();
        Log::info("EOD SALES : " . $eodSales);
        return view('local_cabinet.eod_summarylist', compact(
            'round',
            'company',
            'terminal',
            'location',
            'user',
            'eoddetailsdata',
            'reverseAmount',
            'reverseTax',
            'reverseCash',
            'reverseCard',
            'eod_date',
            'refund_data',
            'refund_sst',
            'refund_round',
            'reverseWallet',
            'eodSales',
            'eodRound',
            'eodWallet',
            'eodCreditCard',
            'eodOew',
            'eodcreditAccount',
            'eodTax',
            'eodCash'
        ));
    }

    public function round_amount($num)
    {
        $num = round($num, 2);
        $split = explode('.', $num);
        if (is_array($split)) {
            $whole = $split[0];
            $dec = $split[1] ?? 0;
            $round_fig = substr($dec, 1, 1);
            if ($round_fig <= 2 && $round_fig > 0) {
                return (int)  - ($round_fig);
            } else if ($round_fig < 5 && $round_fig > 2) {
                $res = 5 - $round_fig;
                return (int) ("$res");
            } else if ($round_fig < 8 && $round_fig > 5) {
                $res = $round_fig - 5;
                return (int)  - ("$res");
            } else if ($round_fig <= 9 && $round_fig >= 8) {
                $res = 10 - $round_fig;
                return (int) ("$res");
            }
            return 0;
        } else {
            return 0;
        }
    }

    public function eodReceiptPopup(Request $request)
    {

        $receipt = DB::table('cstore_receipt')->find($request->id);

        if(!empty($receipt)){
            $user = User::where('id', $receipt->staff_user_id)->first();
            $company = Company::where('id', $receipt->company_id)->first();
            $terminal = Terminal::where('id', $receipt->terminal_id)->first();
            $location = Location::first();

            $receiptproduct = DB::table('cstore_receiptproduct')->
                where('cstore_receiptproduct.receipt_id', $receipt->id)->
                get();

            $receiptdetails = DB::table('cstore_receiptdetails')->
                where('receipt_id', $receipt->id)->first();

            $milon = new DNS2D;
            $qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");

            $ref = DB::table('cstore_receiptrefund')->
                join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->
                where('cstore_receipt_id', $receipt->id)->
                select(
                    'cstore_receiptrefund.*',
                    'users.fullname as name',
                    'users.systemid as systemid'
                )->
                first();

            if (!empty($ref)) {
                $ref->refund_amount += $this->round_amount($ref->refund_amount) / 100;
            }

            $refund = '';
            if ($ref) {
                $refund = $ref;
                return view('cstore.cstore_receipt', compact(
                    'company',
                    'terminal',
                    'location',
                    'user',
                    'receipt',
                    'receiptproduct',
                    'receiptdetails',
                    'qrcode',
                    'refund'
                ));
            } else {
                return view('cstore.cstore_receipt', compact(
                    'company',
                    'terminal',
                    'location',
                    'user',
                    'receipt',
                    'receiptproduct',
                    'receiptdetails',
                    'qrcode',
                    'refund'
                ));
            }
        }

    }

    public function eodReceiptVoid(Request $request)
    {
        $user = Auth::user();
        $receipt = Receipt::find($request->receiptid);
        $receipt->voided_at = now(); //$request->voitdatetime;
        $receipt->void_user_id = $user->id;
        $receipt->void_reason = $request->reason_void;
        $receipt->status = "voided";
        $receipt->save();
        return true;
    }

    public function ReceiptRefund(Request $request)
    {
        Log::debug('ReceiptRefund: receipt_id=' . $request->receipt_id);

        $receipt = DB::table('receipt')->find($request->receipt_id);

        if ($receipt->status == 'voided') {
            return 'VOID';
        }

        $receiptproduct = DB::table('receiptproduct')->
            join('prd_ogfuel', 'prd_ogfuel.product_id', 'receiptproduct.product_id')->
            where('receiptproduct.receipt_id', $request->receipt_id)->
            select('prd_ogfuel.id as og_id', 'receiptproduct.*')->
            first();

        $price = $receiptproduct->price / 100;

        Log::debug('ReceiptRefund: price     =' . $price);

        $refund_amt = $request->filled;

        Log::debug('ReceiptRefund: refund_amt=' . $refund_amt);
        //Log::debug('ReceiptRefund: rounding  ='.$this->round_amount($refund_amt)/100);

        //$refund_amt +=  $this->round_amount($refund_amt)/100;

        //Log::debug('ReceiptRefund: refund_amt='.$refund_amt.' (AFTER rounding)');

        if ($refund_amt <= 0) {
            $refund_amt = 0;
        }

        $refund_qty = $refund_amt / $price;

        Log::debug('ReceiptRefund: refund_qty=' . $refund_qty);

        try {
            DB::table('receiptrefund')->insert([
                'receipt_id' => $request->receipt_id,
                'staff_user_id' => $request->user()->id,
                'refund_amount' => $refund_amt,
                'qty' => $refund_qty ?? 0,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
            //do nothing
        }
        return true;
    }

    public function ReceiptRefund_cstore(Request $request)
    {
        try {

            $refund_id = DB::table('cstore_receiptrefund')->insertGetId([
                'cstore_receipt_id' => $request->receipt_id,
                'staff_user_id' => $request->user()->id,
                'refund_amount' => $request->amount,
                'description' => $request->description,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table('cstore_receipt')->where('id', $request->receipt_id)->update([
                'status' => 'refunded',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $cstore_receiptrefund = DB::table('cstore_receiptrefund')->
            where('id', $refund_id)->
            get();
            $cstore_receipt = DB::table('cstore_receipt')->
            where('id', $request->receipt_id)->
            first();

            $response_sync = Http::post(env('OCOSYSTEM_URL') . '/sync-itemized-sales/refund', [
                'cstore_receiptrefund' => json_encode($cstore_receiptrefund),
                'cstore_receipt' => json_encode($cstore_receipt),
                'id' => $request->receipt_id,
            ]);

            return $response_sync;
        } catch (Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
        }
    }

    public function ReceiptCreate(Request $request)
    {

        try {

            // $products = $request->products;
            $client_ip = request()->ip();

            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $systemid = Systemid::receipt_system_id($terminal->id);
            $pump_hardware = DB::table('local_pump')->where("pump_no", $request->pump_no)->first();

            $receipt = new Receipt();
            $receipt->systemid = $systemid;

            if ($request->payment_type == "card") {
                $receipt->payment_type = "creditcard";
                $receipt->creditcard_no = $request->creditcard_no ?? 0;
                $receipt->cash_received = ($request->cash_received ?? 0) * 100;
            } elseif ($request->payment_type == 'wallet') {
                $receipt->payment_type = "wallet";
                $receipt->cash_received = ($request->cash_received ?? 0) * 100;
            } else {
                $receipt->payment_type = $request->payment_type;
                $receipt->cash_received = ($request->cash_received ?? 0) * 100;
                $receipt->cash_change = ($request->change_amount ?? 0) * 100;
            }

            $receipt->company_name = $company->name;
            $receipt->gst_vat_sst = $company->gst_vat_sst;
            $receipt->business_reg_no = $company->business_reg_no;

            $receipt->service_tax = $terminal->tax_percent;
            $receipt->terminal_id = $terminal->id;
            $receipt->mode = $terminal->mode;

            $receipt->staff_user_id = $user->id;
            $receipt->company_id = $company->id;
            $receipt->receipt_logo = $company->corporate_logo;
            $receipt->receipt_address = $company->office_address;
            //    $receipt->currency = "NULL";//$company->currency;

            $receipt->status = "active";
            $receipt->remark = "NULL";
            $receipt->transacted = "pos";

            $receipt->pump_id = $pump_hardware->id;
            $receipt->pump_no = $request->pump_no;

            $receipt->transacted = "pos";
            $receipt->save();

            $receiptproductsdiscount = 0;

            $receiptproduct_id = DB::table('receiptproduct')->insertGetId([
                "receipt_id" => $receipt->id,
                "product_id" => $request->product_id,
                "name" => $request->product_name,
                "quantity" => $request->product_qty,
                "price" => $request->product_price * 100,
                "discount_pct" => 0,
                "discount" => 0,
                "created_at" => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $amount = (float) number_format($request->cal_item_amount);
            $price = (float) number_format($request->product_price);
            $sst = (float) number_format($request->cal_sst);
            $total_amount = (float) number_format($request->cal_total);
            $rounding = (float) number_format($request->cal_rounding);

            DB::table('itemdetails')->insert([
                "receiptproduct_id" => $receiptproduct_id,
                "amount" => $amount * 100,
                "rounding" => $rounding,
                "price" => $price * 100,
                "sst" => $sst * 100,
                "discount" => 0,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            $cash_received = 0;
            $cash_change = 0;
            $creditcard = 0;
            if ($receipt->payment_type == "cash") {

                $cash_received = $request->cash_received;
            } elseif ($receipt->payment_type == "wallet") {
                $wallet = $request->cal_total;
            } else {
                $creditcard = $request->cal_total;
            }

            DB::table('receiptdetails')->insert([
                "receipt_id" => $receipt->id,
                "total" => $request->cal_total * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->cal_item_amount * 100,
                "sst" => $request->cal_sst * 100,
                "discount" => $receiptproductsdiscount * 100,
                "cash_received" => $cash_received * 100,
                "change" => $request->change_amount * 100,
                "creditcard" => $creditcard * 100,
                "wallet" => ($wallet ?? 0) * 100,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->
                first();

            if (empty($brancheoddata)) {
                $brancheod = DB::table('brancheod')->insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('eoddetails')->insert([
                    "eod_id" => $brancheod,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('total'),
                    "rounding" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('rounding'),
                    "sales" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('item_amount'),
                    "sst" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('sst'),
                    "discount" => DB::table('itemdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('discount'),
                    "cash" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('cash_received'),
                    "cash_change" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('change'),
                    "creditcard" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('creditcard'),
                    "wallet" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('wallet'),
                    "oew" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('oew'),
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {

                DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    update([
                    "total_amount" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('total'),
                    "rounding" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('rounding'),
                    "sales" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('item_amount'),
                    "sst" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('sst'),
                    "discount" => DB::table('itemdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('discount'),
                    "cash" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('cash_received'),
                    "cash_change" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('change'),
                    "wallet" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('wallet'),
                    "creditcard" => DB::table('receiptdetails')->
                        whereDate('created_at', '=', date('Y-m-d'))->
                        sum('creditcard'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->check_personal_shift();
            $receipt = DB::table('receipt')->
                where('id', $receipt->id)->
                first();

            $receipt_details = DB::table('receiptdetails')->
                where('receipt_id', $receipt->id)->
                first();

            $receipt_product = DB::table('receiptproduct')->
                where('receipt_id', $receipt->id)->
                first();

            $user = DB::table('users')->
                where('id', $receipt->staff_user_id)->
                first();

            $itemdetails = DB::table('itemdetails')->
                where('receiptproduct_id', $receipt_product->id)->
                first();

            $payload = [
                'user' => $user, 'receipt' => $receipt,
                'receipt_details' => $receipt_details,
                'receipt_product' => $receipt_product,
                'itemdetails' => $itemdetails,
            ];

            $load = json_encode($payload);

            //API Call
            $setup = new SetupController;
            $response = $setup->updateReceiptatMotherShip($load);
            Log::debug('updateReceiptatMotherShip=' . json_encode($response));

            return $receipt->id;
        } catch (\Exception $e) {
            Log::info([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);

            return $e;
        }
    }


    public function ReceiptCreateCStore(Request $request)
    {

        Log::debug('****CS ReceiptcreateCStore() ****');
        try {
            $products = $request->products;
            $client_ip = request()->ip();

            $terminal = DB::table('terminal')->where('client_ip', $client_ip)->first();

            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $systemid = Systemid::receipt_system_id($terminal->id);

            $receipt = [];
            $receipt['systemid'] = $systemid;

            //Log::debug('CS1 $receipt='.json_encode($receipt));

            if ($request->payment_type == "card") {
                $receipt['payment_type'] = "creditcard";
                $receipt['creditcard_no'] = $request->creditcard_no ?? 0;
                $receipt['cash_received'] = ($request->cash_received ?? 0) * 100;
            } elseif ($request->payment_type == 'wallet') {
                $receipt['payment_type'] = "wallet";
                $receipt['cash_received'] = ($request->cash_received ?? 0) * 100;
            } else {
                $p_details = array_values($request->products)[0];
                // not sure what this might affect, but this currently adds now row to cash sales
                // $this->stockUpdate($p_details['qty'], $p_details['product_id'], "OUT");
                $receipt['payment_type'] = $request->payment_type;
                $receipt['cash_received'] = ($request->cash_received ?? 0) * 100;
                $receipt['cash_change'] = ($request->change_amount ?? 0) * 100;
            }

            //Log::debug('CS2 $receipt='.json_encode($receipt));

            $receipt['company_name'] = $company->name;
            $receipt['gst_vat_sst'] = $company->gst_vat_sst;
            $receipt['business_reg_no'] = $company->business_reg_no;

            $receipt['service_tax'] = $terminal->tax_percent;
            $receipt['terminal_id'] = $terminal->id;
            $receipt['mode'] = $terminal->mode;

            //Log::debug('CS3 $receipt='.json_encode($receipt));

            $receipt['staff_user_id'] = $user->id;
            $receipt['company_id'] = $company->id;

            $receipt['receipt_logo'] = $company->corporate_logo;
            $receipt['receipt_address'] = $company->office_address;

            //Log::debug('CS4 $receipt='.json_encode($receipt));
            //Log::debug('CS4 $company='.json_encode($company));
            //Log::debug('CS4 currency='.json_encode($company->currency));

            if (!empty($company->currency)) {
                $receipt['currency'] = $company->currency->code;
            } else {
                $receipt['currency'] = 'MYR';
            }

            //Log::debug('CS5 $receipt='.json_encode($receipt));

            $receipt['status'] = "active";
            $receipt['remark'] = "NULL";
            $receipt['transacted'] = "pos";

            $receipt['created_at'] = $receipt['updated_at'] = now();

            $rid = DB::table('cstore_receipt')->insertGetId($receipt);

            $receipt = DB::table('cstore_receipt')->find($rid);

            $receiptproductsdiscount = 0;

            $stock_system = new SystemID("stockreport");

            foreach ($products as $p) {
                //auto siso
                $exisitingQty = (int) app("App\Http\Controllers\CentralStockMgmtController")->
                    qtyAvailable($p['product_id']);
                $req_qty = (int) $p['qty'];

                if ($exisitingQty < $req_qty && $company->auto_stockin == 1) {
                    app("App\Http\Controllers\CentralStockMgmtController")->
                        autoStockIn($p['product_id'], ($req_qty - $exisitingQty), $rid);
                }

                $receiptproduct_id = DB::table('cstore_receiptproduct')->
                    insertGetId([
                    "receipt_id" => $receipt->id,
                    "product_id" => $p['product_id'],
                    "name" => $p['name'],
                    "quantity" => $p['qty'],
                    "price" => $p['price'] * 100,
                    "discount_pct" => $p['discount'],
                    "discount" => $p['discount_amount'] * 100,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $receiptproductsdiscount += $p['discount_amount'];

                $receiptproduct = DB::table('cstore_receiptproduct')->
                    where('id', $receiptproduct_id)->first();

                $amount = $p['item_amount'];
                $price = $p['price'];
                $sst =  $p['sst'];
                $total_amount = $p['total_amount'];

                //$item_amount = ($amount) / (1 + ($terminal->tax_percent / 100));
                //$item_amount = round($item_amount, 2);
                //$sst = abs($amount - $item_amount);

                $rounding = $this->round_amount($total_amount);

                $cstore_itemdetails_ids[] = DB::table('cstore_itemdetails')->
                    insertGetId([
                    "receiptproduct_id" => $receiptproduct->id,
                    "amount" => $amount * 100,
                    "rounding" => $rounding,
                    "price" => $price * 100,
                    "sst" => $sst * 100,
                    "discount" => $receiptproduct->discount_pct,
                    "created_at" => $receipt->created_at,
                    'updated_at' => $receipt->created_at,
                ]);


                $prd = DB::table('product')->
                    whereId($p['product_id'])->
                    first();
                if ($prd->ptype == 'inventory') {
                    Log::debug("ReceiptCreateCStore---- inventory");

                    $ledger_id =  DB::table('locprod_productledger')->
                    insertGetId([
                        "csreceipt_id"      => $rid,
                        "product_systemid"  => $prd->systemid,
                        "type"              => "cash_sales",
                        "cost"              =>  0,
                        "status"            => "active",
                        "qty"               => $p['qty'] * -1,
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_at"        => date('Y-m-d H:i:s'),
                    ]);
                    app("App\Http\Controllers\CentralStockMgmtController")->
                        process_locprod_stockout_negative_sales($prd->systemid, $p['qty'] * -1, $rid, $ledger_id);

                } else if ($prd->ptype == 'openitem') {
                    Log::debug("ReceiptCreateCStore--> Openitem");
                    $ledger_id =  DB::table('openitem_productledger')->
                    insertGetId([
                        "csreceipt_id"      => $rid,
                        "product_systemid"  => $prd->systemid,
                        "type"              => "cash_sales",
                        "cost"              =>  0,
                        "status"            => "active",
                        "qty"               => $p['qty'] * -1,
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_at"        => date('Y-m-d H:i:s'),
                    ]);
                    app("App\Http\Controllers\OpenitemController")->
                    process_openitem_stockout_negative_sales($prd->systemid, $p['qty'] * -1,
                        $rid,$ledger_id);

                 }
            }

            //$receiptproductsdiscount = 0;
            $cash_received = 0;
            $cash_change = 0;
            $creditcard = 0;
            if ($receipt->payment_type == "cash") {
                $cash_received = $request->cash_received;
            } elseif ($receipt->payment_type == "wallet") {
                $wallet = $request->cal_total;
            } else {
                $creditcard = $request->cal_total;
            }

            DB::table('cstore_receiptdetails')->
                insert([
                "receipt_id" => $receipt->id,
                "total" => $request->cal_total * 100,
                "rounding" => $request->cal_rounding * 100,
                "item_amount" => $request->cal_item_amount * 100,
                "sst" => $request->cal_sst * 100,
                "discount" => $receiptproductsdiscount * 100,
                "cash_received" => $cash_received * 100,
                "change" => $request->change_amount * 100,
                "creditcard" => $creditcard * 100,
                "wallet" => ($wallet ?? 0) * 100,
                "created_at" => $receipt->created_at,
                'updated_at' => $receipt->created_at,
            ]);

            $this->updateEOD();

            $this->check_personal_shift();

            $brancheoddata = DB::table('brancheod')->
                whereDate('created_at', '=', date('Y-m-d'))->
                first();

            Log::info(json_encode($brancheoddata));

            $loginOut = FuelReceipt::getCurrentLoginOut();
            $dataPshiftdetails = DB::table('pshiftdetails')->
                where('pshift_id', '=', $loginOut->shift_id)->
                first();

            $eoddetail_id = null;

            if ($brancheoddata == null) {
                $brancheod = DB::table('brancheod')->
                    insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $brancheoddata = DB::table('brancheod')->
                    where('id', '=', $brancheod)->
                    first();

                Log::info("yes");

                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);
                Log::info("after");

                $idEoddetail = DB::table('eoddetails')->
                    insertGetId([
                    "eod_id" => $brancheod,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"],
                    "wallet" => $dataForEod["eodWallet"],
                    "creditac" => $dataForEod["eodcreditAccount"],
                    "opt" => 0,
                    "oew" => $dataForEod["eodOew"],
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $eoddetail_id = $idEoddetail;

            } else {
                $dataForEod = FuelReceipt::getReceiptValueWithoutVoid($brancheoddata, true);

                DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    update([
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $dataForEod["eodTotal"],
                    "rounding" => $dataForEod["eodRound"],
                    "sales" => $dataForEod["eodItemAmount"],
                    "sst" => $dataForEod["eodTax"],
                    "discount" => $dataForEod["eodDiscount"],
                    "cash" => $dataForEod["eodCash"],
                    "cash_change" => $dataForEod["eodChange"],
                    "creditcard" => $dataForEod["eodCreditCard"],
                    "wallet" => $dataForEod["eodWallet"],
                    "creditac" => $dataForEod["eodcreditAccount"],
                    "opt" => 0,
                    "oew" => $dataForEod["eodOew"],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $eoddetail = DB::table('eoddetails')->
                    where('eod_id', $brancheoddata->id)->
                    first();

                if (!empty($eoddetail)) {
                    $eoddetail_id = $eoddetail->id;
                }
            }

            if ($eoddetail_id != null) {

                try {
                    $dataForEod = FuelReceipt::getUserLoginReceiptValueWithoutVoid();
                } catch (\Exception $e) {
                    \Log::error([
                        'Error' => $e->getMessage(),
                        "File" => $e->getFile(),
                        "Line" => $e->getLine(),
                    ]);
                    abort(404);
                }

                $currentLoginOut = FuelReceipt::getCurrentLoginOut();
                DB::table('pshift')->where(
                    'id',
                    $currentLoginOut->shift_id
                )->update([
                    'eoddetails_id' => $eoddetail_id,
                ]);

                if ($dataPshiftdetails == null) {
                    Log::info(["eoddetail_id" => "start"]);

                    DB::table('pshiftdetails')->insert([
                        "pshift_id" => $loginOut->shift_id,
                        "eoddetails_id" => $eoddetail_id,
                        "startdate" => date('Y-m-d'),
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"],
                        "wallet" => $dataForEod["eodWallet"],
                        "creditac" => $dataForEod["eodcreditAccount"],
                        "opt" => 0,
                        "oew" => $dataForEod["eodOew"],
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {

                    DB::table('pshiftdetails')->
                        where('id', $dataPshiftdetails->id)->
                        update([
                        "total_amount" => $dataForEod["eodTotal"],
                        "rounding" => $dataForEod["eodRound"],
                        "sales" => $dataForEod["eodItemAmount"],
                        "sst" => $dataForEod["eodTax"],
                        "discount" => $dataForEod["eodDiscount"],
                        "cash" => $dataForEod["eodCash"],
                        "cash_change" => $dataForEod["eodChange"],
                        "creditcard" => $dataForEod["eodCreditCard"],
                        "wallet" => $dataForEod["eodWallet"],
                        "creditac" => $dataForEod["eodcreditAccount"],
                        "opt" => 0,
                        "oew" => $dataForEod["eodOew"],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $cstore_receipt = DB::table('cstore_receipt')->
                whereId($receipt->id)->get();
            if (count($cstore_itemdetails_ids) > 1) {
                $cstore_itemdetails = DB::table('cstore_itemdetails')->
                    whereIn('id', $cstore_itemdetails_ids)->
                    get();
            } else {
                $cstore_itemdetails = DB::table('cstore_itemdetails')->
                    whereId($cstore_itemdetails_ids)->get();
            }
            $cstore_receiptdetails = DB::table('cstore_receiptdetails')->
                where('receipt_id', $receipt->id)->
                get();
            $cstore_receiptproduct = DB::table('cstore_receiptproduct')->
                where('receipt_id', $receipt->id)->
                get();
            $cstore_receiptrefund = DB::table('cstore_receiptrefund')->
                where('cstore_receipt_id', $receipt->id)->
                get();

            $data['cstore_receipt'] = "$cstore_receipt";
            $data['cstore_itemdetails'] = "$cstore_itemdetails";
            $data['cstore_receiptdetails'] = "$cstore_receiptdetails";
            $data['cstore_receiptproduct'] = "$cstore_receiptproduct";
            $data['cstore_receiptrefund'] = "$cstore_receiptrefund";

            $query = "select t.systemid from terminal t, cstore_receipt cr
            where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            //get localprice table
            $data['localprice'] = DB::table('localprice')->get();

            //$response_sync = $this->curlRequest(env('OCOSYSTEM_URL') . '/sync-itemized-sales' , json_encode($data));
            SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-itemized-sales', json_encode($data));

            $query = http_build_query($data, '', '&');

            $url = env('MOTHERSHIP_URL') . '/store/cstore/receipt/' . $query;
            Log::debug("url=" . $url);
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, $url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);
            $data = json_decode($apiResponse, true);

            return $receipt->id;
        } catch (\Exception $e) {
            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);

            abort(404);
        }
    }

    public function curlRequest($url, $params = null)
    {
        $ch = curl_init($url);

        /*
        $headers = array(
        'Authorization: Bearer '.$_SESSION['token'],
        );
         */

        if ($params !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=$params");
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function cstore_receipt(Request $request)
    {

        $receipt = DB::table('cstore_receipt')->find($request->id);
        $receipt = is_null($receipt) ? DB::table('cstore_receipt')->
        where('systemid',$request->id)->first(): $receipt;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();
        $receiptproduct = DB::table('cstore_receiptproduct')->
            where('receipt_id', $receipt->id)->
            get();

        $receiptdetails = DB::table('cstore_receiptdetails')->
            where('receipt_id', $receipt->id)->
            first();
        $milon = new DNS2D;
        $qrcode = $milon->getBarcodePNG($receipt->systemid, "QRCODE");

        $ref = DB::table('cstore_receiptrefund')->
            join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->
            where('cstore_receiptrefund.cstore_receipt_id', $receipt->id)->
            select('cstore_receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')->
            first();

        if (!empty($ref)) {
            $ref->refund_amount += $this->round_amount($ref->refund_amount) / 100;
        }

        $sst = $receiptdetails->sst ?? 0.00;
        $rounding = $receiptdetails->rounding ?? 0.00;
        $item_amount = $receiptdetails->item_amount ?? 0.00;

        $refund = '';
        if ($ref) {
            $refund = $ref;

            return view('cstore.cstore_receipt', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'receiptdetails',
                'qrcode',
                'refund',
                'sst',
                'rounding',
                'item_amount'
            ));
        } else {
            return view('cstore.cstore_receipt', compact(
                'company',
                'terminal',
                'location',
                'user',
                'receipt',
                'receiptproduct',
                'receiptdetails',
                'qrcode',
                'refund',
                'sst',
                'rounding',
                'item_amount'
            ));
        }
    }

    public function updateEOD()
    {
        try {
            $user = Auth::user();
            $company = Company::first();
            $location = Location::first();
            $client_ip = request()->ip();
            $terminal = DB::table('terminal')->
                where('client_ip', $client_ip)->first();

            $brancheoddata = DB::table('eoddetails')->
                whereDate('created_at', '=', date('Y-m-d'))->first();

            $cstore_receiptdetails = DB::table('cstore_receiptdetails')->
                whereDate('created_at', '=', date('Y-m-d'))->
                get();

            $cstore_itemdetails = DB::table('cstore_itemdetails')->
                whereDate('created_at', '=', date('Y-m-d'))->
                get();

            if (empty($brancheoddata)) {
                $brancheod = DB::table('brancheod')->
                    insertGetId([
                    "eod_presser_user_id" => $user->id,
                    "location_id" => $location->id,
                    "terminal_id" => $terminal->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('eoddetails')->
                    insert([
                    "eod_id" => $brancheod,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => $cstore_receiptdetails->sum('total'),
                    "rounding" => $cstore_receiptdetails->sum('rounding'),
                    "sales" => $cstore_receiptdetails->sum('item_amount'),
                    "sst" => $cstore_receiptdetails->sum('sst'),
                    "discount" => $cstore_itemdetails->sum('discount'),
                    "cash" => $cstore_receiptdetails->sum('cash_received'),
                    "cash_change" => $cstore_receiptdetails->sum('change'),
                    "creditcard" => $cstore_receiptdetails->sum('creditcard'),
                    "wallet" => $cstore_receiptdetails->sum('wallet'),
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                DB::table('eoddetails')->where('eod_id', $brancheoddata->id)->
                    update([
                    "total_amount" => $cstore_receiptdetails->sum('total'),
                    "rounding" => $cstore_receiptdetails->sum('rounding'),
                    "sales" => $cstore_receiptdetails->sum('item_amount'),
                    "sst" => $cstore_receiptdetails->sum('sst'),
                    "discount" => $cstore_itemdetails->sum('discount'),
                    "cash" => $cstore_receiptdetails->sum('cash_received'),
                    "cash_change" => $cstore_receiptdetails->sum('change'),
                    "wallet" => $cstore_receiptdetails->sum('wallet'),
                    "creditcard" => $cstore_receiptdetails->sum('creditcard'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            /*
        $receipt = DB::table('receipt')->
        where('id', $receipt_details->receipt_id)->first();

        $receipt_details = DB::table('receiptdetails')->
        where('receipt_id', $receipt->id)->first();

        $receipt_product = DB::table('receiptproduct')->
        where('receipt_id', $receipt->id)->first();

        $user = DB::table('users')->
        where('id', $receipt->staff_user_id)->first();

        $itemdetails = DB::table('itemdetails')->
        where('receiptproduct_id', $receipt_product->id)->first();

        $payload = ['user' => $user, 'receipt' => $receipt,
        'receipt_details' => $receipt_details,
        'receipt_product' => $receipt_product,
        'itemdetails' => $itemdetails];

        $load = json_encode($payload);

        //API Call
        $setup = new SetupController;
        $response = $setup->updateReceiptatMotherShip($load);
        Log::debug('updateReceiptatMotherShip=' . json_encode($response));

        return $receipt->id;
         */
        } catch (\Exception $e) {
            \Log::info([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);

            return $e;
        }
    }

    public function pssReceiptPopup()
    {
        $user = Auth::user();
        $company = Company::first();

        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $location = Location::first();
        $todaydate = date('Y-m-d');

        $startdatetime = $todaydate . " " . $location->start_work;
        if (date('Y-m-d H:i:s') > $startdatetime) {
            $startdatetime = $startdatetime;
        } else {
            $startdatetime = date('Y-m-d', strtotime(' -1 day')) . " " .
            $location->start_work;
        }

        $this->update_eod_ps_info();

        $eoddetails = DB::table('eoddetails')->whereBetween(
            'created_at',
            [$startdatetime, date('Y-m-d H:i:s')]
        )->first();

        if ($eoddetails != null) {
            $pshift = DB::table('pshift')->whereBetween(
                'created_at',
                [$startdatetime, date('Y-m-d H:i:s')]
            )->first();

            if ($pshift === null) {
                $pshift_id = DB::table('pshift')->insertGetId([
                    "eoddetails_id" => $eoddetails->id,
                    "endpshift_presser_user_id" => $user->id,
                    "terminal_id" => $terminal->id,
                    "location_id" => $location->id,
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                DB::table('pshiftdetails')->insert([
                    "pshift_id" => $pshift_id,
                    "eoddetails_id" => $eoddetails->id,
                    "startdate" => date('Y-m-d'),
                    "total_amount" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('total'),
                    "rounding" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('rounding'),
                    "sales" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('item_amount'),
                    "sst" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('sst'),
                    "discount" => DB::table('itemdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('discount'),
                    "cash" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('cash_received'),
                    "cash_change" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('change'),
                    "creditcard" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('creditcard'),

                    "wallet" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('wallet'),
                    "oew" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('oew'),
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $pshift = DB::table('pshift')->whereBetween(
                    'created_at',
                    [$startdatetime, date('Y-m-d H:i:s')]
                )->first();

                DB::table('pshiftdetails')->where('pshift_id', $pshift->id)->update([
                    "total_amount" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('total'),
                    "rounding" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('rounding'),
                    "sales" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('item_amount'),
                    "sst" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('sst'),
                    "discount" => DB::table('itemdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('discount'),
                    "cash" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('cash_received'),
                    "cash_change" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('change'),
                    "creditcard" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('creditcard'),
                    "wallet" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('wallet'),
                    "oew" => DB::table('receiptdetails')->
                        whereBetween(
                        'created_at',
                        [$startdatetime, date('Y-m-d H:i:s')]
                    )->sum('oew'),
                    "created_at" => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $pshiftdetailsdata = DB::table('pshiftdetails')->
            whereBetween(
            'created_at',
            [$startdatetime, date('Y-m-d H:i:s')]
        )->first();

        return view('local_cabinet.pss', compact(
            'company',
            'terminal',
            'location',
            'user',
            'pshiftdetailsdata'
        ));
    }


    public function check_personal_shift()
    {
        $loginout = DB::table('loginout')->
            where('user_id', Auth::user()->id)->
            latest()->first();

		Log::info('WS check_personal_shift: loginout='.json_encode($loginout));

        if (date("Y-m-d", strtotime($loginout->login)) != date("Y-m-d")) {
            DB::table('loginout')->
                where('id', $loginout->id)->update([
                'logout' => date("Y-m-d 23:59:59", strtotime($loginout->login)),
                'updated_at' => now(),
            ]);

            DB::table('loginout')->insert([
                'login' => date("Y-m-d 00:00:00"),
                'location_id' => $loginout->location_id,
                'user_id' => $loginout->user_id,
                'shift_id' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
	}


    public function pshift_table(Request $request)
    {
        $this->check_personal_shift();
        $pshift = DB::table('loginout')->
            join('users', 'users.id', 'loginout.user_id')->
            whereDate('loginout.created_at', date('Y-m-d',
            strtotime($request->date)))->
            select('loginout.*', 'users.systemid')->
            orderBy('loginout.created_at', 'desc')->get();

        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $location = Location::first();

        return view('local_cabinet.personal_shift_table',
            compact('pshift', 'terminal', 'location'));
    }

    public function pshift_detail(Request $request)
    {
        $login_time = date("Y-m-d H:i:s", strtotime($request->login_time));
        $logout_time = date("Y-m-d H:i:s", strtotime($request->logout_time ?? now()));

        $user_systemid = $request->user_systemid;

        $pshiftid = $request->shift_id;
        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $company = Company::first();
        $location = Location::first();
        $user = DB::table('users')->
            where('systemid', $user_systemid)->first();

        // update EOD and shift info
        $this->update_eod_ps_info();

        $data = DB::table('pshiftdetails')->
            where("pshift_id", $request->shift_id)->first();

        Log::debug(['pshift_detail: all()' => $request->all()]);
        Log::debug(['pshift_detail: data' => $data]);

        $sales = "0.00";
        $cash = "0.00";
        $creditcard = "0.00";
        $creditac = "0.00";
        $wallet = "0.00";
        $oew = "0.00";
        $opt = "0.00";
        $tax = "0.00";
        $round = "0.00";

        Log::info(["pshiftdetails" => ($data !== null)]);

        $fuelnewsalesdata = DB::table('fuel_receipt')->
            join('fuel_receiptlist', 'fuel_receipt.id', 'fuel_receiptlist.fuel_receipt_id')->
            join('fuel_receiptdetails', 'fuel_receipt.id', 'fuel_receiptdetails.receipt_id')->
            whereBetween('fuel_receipt.created_at', [$login_time, $logout_time])->
            where('fuel_receipt.status', 'refunded')->
            select(
            'fuel_receipt.id',
            'fuel_receipt.systemid',
            'fuel_receipt.payment_type',
            'fuel_receipt.status',
            'fuel_receipt.service_tax',
            'fuel_receiptlist.total',
            'fuel_receiptlist.refund',
            'fuel_receiptlist.newsales_item_amount',
            'fuel_receiptlist.newsales_tax',
            'fuel_receiptlist.newsales_rounding',
            'fuel_receiptdetails.total',
            'fuel_receiptdetails.rounding',
            'fuel_receiptdetails.item_amount',
            'fuel_receiptdetails.sst',
        )->get();

        $fuelnewsalescreditcard = 0;
        $fuelnewsalescreditac = 0;
        $fuelnewsalescash = 0;
        $fuelnewsaleswallet = 0;
        $fuelnewsalesoew = 0;
        $fuelnewsalessales = 0;
        $fuelnewsalessst = 0;
        $fuelnewsalesrounding = 0;
        $fuelnewsalesccrounding = 0;
        $fuelnewsalescarounding = 0;
        $fuelnewsalescashrounding = 0;
        $fuelnewsaleswalletrounding = 0;
        $fuelnewsalesoewrounding = 0;

        foreach ($fuelnewsalesdata as $fuelnewsales) {
            switch ($fuelnewsales->payment_type) {
                case "creditcard":
                    $fuelnewsalesccrounding += $fuelnewsales->newsales_rounding;
                    break;
                case "creditac":
                    $fuelnewsalescarounding += $fuelnewsales->newsales_rounding;
                    break;
                case "cash":
                    $fuelnewsalescashrounding += $fuelnewsales->newsales_rounding;
                    break;
                case "wallet":
                    $fuelnewsaleswalletrounding += $fuelnewsales->newsales_rounding;
                    break;
                case "oew":
                    $fuelnewsalesoewrounding += $fuelnewsales->newsales_rounding;
                    break;
            }
        }

        // calculate for hydrogen
        $h2eodrefundsdata = DB::table('h2receipt')->
            join('h2receiptlist', 'h2receipt.id', 'h2receiptlist.h2receipt_id')->
            join('h2receiptdetails', 'h2receipt.id', 'h2receiptdetails.receipt_id')->
            whereBetween('h2receipt.created_at', [$login_time, $logout_time])->
            where('h2receipt.status', 'refunded')->
            select(
            'h2receipt.id',
            'h2receipt.systemid',
            'h2receipt.payment_type',
            'h2receipt.status',
            'h2receipt.service_tax',
            'h2receiptlist.total',
            'h2receiptlist.refund',
            'h2receiptdetails.total',
            'h2receiptdetails.rounding',
            'h2receiptdetails.item_amount',
            'h2receiptdetails.sst',
        )->get();

        $h2refundscreditcard = 0;
        $h2refundscreditac = 0;
        $h2refundscash = 0;
        $h2refundswallet = 0;
        $h2refundsoew = 0;
        $h2refundsnetsales = 0;
        $h2refundssst = 0;
        $h2refundsrounding = 0;

        foreach ($h2eodrefundsdata as $h2refund) {
            switch ($h2refund->payment_type) {
                case "creditcard":
                    $h2refundscreditcard += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundssst += $h2refund->sst;
                    $h2refundsrounding += $h2refund->rounding;
                    break;
                case "creditac":
                    $h2refundscreditac += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundssst += $h2refund->sst;
                    $h2refundsrounding += $h2refund->rounding;
                    break;
                case "cash":
                    $h2refundscash += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundssst += $h2refund->sst;
                    $h2refundsrounding += $h2refund->rounding;
                    break;
                case "wallet":
                    $h2refundswallet += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundssst += $h2refund->sst;
                    $h2refundsrounding += $h2refund->rounding;
                    break;
                case "oew":
                    $h2refundsoew += ($h2refund->refund + $h2refund->rounding);
                    $h2refundsnetsales += $h2refund->item_amount;
                    $h2refundssst += $h2refund->sst;
                    $h2refundsrounding += $h2refund->rounding;
                    break;
            }
        }

        $refundsnetsales = $h2refundsnetsales;
        $refundscash = $h2refundscash;
        $refundscreditcard = $h2refundscreditcard;
        $refundscreditac = $h2refundscreditac;
        $refundswallet = $h2refundswallet;
        $refundsoew = $h2refundsoew;
        $refundssst = $h2refundssst;
        $refundsrounding = $h2refundsrounding;

        if ($data !== null) {
            $sales = number_format(($data->sales - $refundsnetsales) / 100, 2);
            $cash = ($data->cash - $refundscash + $fuelnewsalescashrounding);
            $creditcard = number_format(($data->creditcard - $refundscreditcard + $fuelnewsalesccrounding) / 100, 2);
            $creditac = number_format(($data->creditac - $refundscreditac + $fuelnewsalescarounding) / 100, 2);
            $wallet = number_format(($data->wallet - $refundswallet + $fuelnewsaleswalletrounding) / 100, 2);
            $oew = number_format(($data->oew - $refundsoew + $fuelnewsalesoewrounding) / 100, 2);
            $tax = number_format(($data->sst - $refundssst) / 100, 2);
            $round = number_format(($data->rounding - $refundsrounding) / 100, 2);
        }

        $logout_time = $request->logout_time ?? '';

        $shift_to = $logout_time;
        if ($logout_time == '') {
            $shift_to = date("Y-m-d H:i:s", strtotime(now()));
        }

        $from = date('Y-m-d H:i:s', strtotime($login_time));
        $to = date('Y-m-d H:i:s', strtotime($shift_to));

        $this->update_shift_cstore($pshiftid, $user->id, $from, $to);

        $cstore_total = 0;

        $cstore_amount = DB::table('pshiftdetails')->
            where('pshift_id', $pshiftid)->first();

        if (!empty($cstore_amount)) {
            $cstore_total = $cstore_amount->cstore;
        }

        $non_op_cash_in = 0;
        $non_op_cash_out = 0;
        $sales_drop = 0;
        $actual_drawer_amount = 0;

        $shift_data = DB::table('pshift')->
            whereId($pshiftid)->first();

        if (!empty($shift_data)) {
            $non_op_cash_in = $shift_data->non_op_cash_in;
            $non_op_cash_out = $shift_data->non_op_cash_out;
            $sales_drop = $shift_data->sales_drop;
            $actual_drawer_amount = $shift_data->actual_drawer_amount;

            /*update FUEL PRODUCTS for shift*/
            Log::debug('shift fuel products - $shift_data->id=' . $shift_data->id);
            Log::debug('shift fuel products - $user->id=' . $user->id);
            Log::debug('shift fuel products - $from=' . $from);
            Log::debug('shift fuel products - $to=' . $to);

            $this->update_terminal_shift_fuel($shift_data->id, $user->id, $from, $to);
        }

        $fuel_products = DB::table('pshiftfuel')->join('prd_ogfuel', 'prd_ogfuel.id', '=', 'pshiftfuel.ogfuel_id')->join('product', 'product.id', '=', 'prd_ogfuel.product_id')->where('pshift_id', $pshiftid)->get();

        return view('local_cabinet.pss', compact(
            'terminal',
            'company',
            'user',
            'wallet',
            'oew',
            'opt',
            'location',
            'sales',
            'cash',
            'creditcard',
            'cstore_total',
            'fuel_products',
            'creditac',
            'tax',
            'round',
            'login_time',
            'non_op_cash_in',
            'non_op_cash_out',
            'sales_drop',
            'actual_drawer_amount',
            'logout_time',
            'pshiftid'
        ));
    }

    public function pss_save_inputs(Request $request)
    {
        try {

            $currentLoginOut = FuelReceipt::getCurrentLoginOut();
            DB::table('pshift')->where(
                'id',
                $request->shift_id
            )->update([
                'non_op_cash_in' => $request->non_op_cash_in == 0 ? null : ($request->non_op_cash_in),
                'non_op_cash_out' => $request->non_op_cash_out == 0 ? null : ($request->non_op_cash_out),
                'sales_drop' => $request->sales_drop == 0 ? null : ($request->sales_drop),
                'actual_drawer_amount' => $request->actual_drawer_amount == 0 ? null : ($request->actual_drawer_amount),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            Log::debug([
                '$request->shift_id' => $request->shift_id,
                'non_op_cash_in' => ($request->non_op_cash_in),
                'non_op_cash_out' => ($request->non_op_cash_out),
                'sales_drop' => ($request->sales_drop),
                'actual_drawer_amount' => ($request->actual_drawer_amount),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            //abort(404);
        }
    }

    public function ShiftPopup(Request $request)
    {
        $date = date('Y-m-d', strtotime($request->date));

        Log::debug('Request: ' . $date);
        $pshift = DB::table('pshift')->
            join('users', 'users.id', '=', 'pshift.endpshift_presser_user_id')->
            whereBetween('pshift.created_at', [$date . ' 00:00:01', $date . ' 23:59:59'])->
            select('pshift.*', 'users.fullname', 'users.systemid')->
            get();

        Log::debug('PSS Data: ' . json_encode($pshift));

        return view('local_cabinet.personal_shift_table',
            compact('pshift'));
    }

    public function PersonalShiftDetails(Request $request)
    {
        $eod_date = $request->eod_date;

        Log::debug('Pshift: ' . $eod_date);
        if ($request->eod_date) {
            $date_eod = date_create($request->eod_date);
            $date_eod = date_format($date_eod, 'Y-m-d');
        } else {
            $date_eod = date('Y-m-d');
        }

        $user = Auth::user();
        $location = Location::first();
        $todaydate = date('Y-m-d');

        $client_ip = request()->ip();
        $terminal = DB::table('terminal')->
            where('client_ip', $client_ip)->first();

        $eoddetailsdata = DB::table('eoddetails')->
            whereDate('startdate', '=', $date_eod)->first();

        $receipts = Receipt::with('receiptdetails')->
            whereDate('receipt.created_at', '=', $date_eod)->
            where('receipt.status', 'voided')->
            get();

        $reverseAmount = 0;
        $reverseCash = 0;
        $reverseCard = 0;
        $reverseTax = 0;

        foreach ($receipts as $receipt) {
            if ($receipt->payment_type == 'creditcard') {
                $reverseCard += $receipt->receiptdetails->creditcard;
            } else {
                $reverseCash += $receipt->receiptdetails->cash_received - $receipt->receiptdetails->change;
            }
            $reverseTax += $receipt->receiptdetails->sst;
        }
        $reverseAmount += ($reverseCard + $reverseCash) - $reverseTax;

        $refund_data = DB::table('receiptrefund')->
            join('receipt', 'receipt.id', 'receiptrefund.receipt_id')->
            whereDate('receipt.created_at', '=', $date_eod)->
            get();

        $refundTax = 0;
        $refundAmount = 0;
        $refundCash = 0;
        $refundCard = 0;

        foreach ($refund_data as $receipt) {
            if ($receipt->payment_type == 'creditcard') {
                $refundCard += ($receipt->refund_amount * 100);
            } else {
                $refundCash += ($receipt->refund_amount * 100);
            }

            $refundAmount += $refundCash + $refundCard;

            $refundTax = $refundAmount / 100 * $terminal->tax_percent;
            $refundAmount -= $refundTax;
        }

        $reverseAmount += $refundAmount;
        $reverseCash += $refundCash;
        $reverseCard += $refundCard;
        $reverseTax += $refundTax;

        $sst_tax = $eoddetailsdata->sst - $reverseTax;
        $round = ($eoddetailsdata->sales + $sst_tax - $reverseAmount) / 100;
        $round = (float) number_format($this->round_amount($round) / 100, 2);

        \Log::info([
            'refund amount' => $refundAmount,
            'refund Cash' => $refundCash,
            'refundCard' => $refundCard,
            'refundTax' => $refundTax,
            'sst_tax' => $sst_tax,
        ]);

        $user = Auth::user();
        $company = Company::first();
        $location = Location::first();
        return view('local_cabinet.eod_summarylist', compact(
            'round',
            'company',
            'terminal',
            'location',
            'user',
            'eoddetailsdata',
            'reverseAmount',
            'reverseTax',
            'reverseCash',
            'reverseCard',
            'eod_date'
        ));
    }

    public function optList()
    {
        try {
            $opt = [];
            return view("local_cabinet.opt_table", compact("opt"));

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function optListData()
    {

        try {

            return Datatables::of([])->
                addIndexColumn()->
                rawColumns(['action'])->
                make(true);

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function evList()
    {
        try {
            $ev = [];
            return view("local_cabinet.ev_table", compact("ev"));

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function evListData()
    {
        try {
            $data = CarPark::all();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a  href="javascript:void(0)" onclick="actionClick(' . $row->id . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="images/bluecrab_50x50.png" alt=""> </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function evReceiptDetail()
    {
        try {
            $company = Company::first();
            $receipt = Receipt::first();
            $receiptdetails = ReceiptDetails::first();
            $refund = ReceiptRefund::first();
            return view("local_cabinet.ev_receipt",
                compact("company", "receipt", "receiptdetails", "refund"));

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function loyaltyPoints(Request $request)
    {
        try {

            $receipt = Receipt::find($request->id);
            $receiptproduct = DB::table('receiptproduct')->leftjoin('product', 'product.id', 'receiptproduct.product_id')->where('receipt_id', $receipt->id)->get();
            $loyatypoints = array();

            $i = 0;
            foreach ($receiptproduct as $pro) {
                $loyatypoints[$i]['systemid'] = $pro->systemid;
                $loyatypoints[$i]['name'] = $pro->name;
                $loyatypoints[$i]['quantity'] = $pro->quantity;
                $loyatypoints[$i]['thumb'] = $pro->thumbnail_1;
                $loyatypoints[$i]['price'] = $pro->price;
                $sub_prod_table = '';
                switch ($pro->ptype) {
                    case 'oilgas':
                        $sub_prod_table = 'prd_ogfuel';
                        break;
                    case 'inventory':
                        $sub_prod_table = 'prd_openitem';
                        break;
                    default:
                        $loyatypoints[$i]['loyalty'] = 0;
                }
                if ($sub_prod_table != '') {
                    $prd_ = DB::table($sub_prod_table)->where('product_id', $pro->product_id)->get()->first();
                    $loyatypoints[$i]['loyalty'] = $prd_->loyalty;
                }
            }

            return view(
                'local_cabinet.loyalty_point',
                compact('loyatypoints')
            );
        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            //abort(404);
        }
    }

    // added optimisation functions

    public function process_locprod_stockout($systemid, $curr_qty)
    {

        try {
            // Get oldest non zero balance -- 1st Level
            $oldest_bal = DB::table('locationproduct_cost')->
                join(
                    'locprod_productledger',
                    'locprod_productledger.id',
                    'locationproduct_cost.locprodprodledger_id'
                )->
                select(
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
                )->
                where("locprod_productledger.product_systemid", $systemid)->
                where('locationproduct_cost.balance', '>', 0)->
                orderBy('locationproduct_cost.created_at', 'asc')->
                first();

            $cost = empty($oldest_bal) ? 0 : $oldest_bal->cost;

            if (!empty($oldest_bal)) {

                $compare = $curr_qty;
                if ($oldest_bal->balance >= ($compare * -1)) {

                    DB::table('locationproduct_cost')->
                    whereId($oldest_bal->id)->
                    update([
                        "qty_out" => $curr_qty + $oldest_bal->qty_out,
                        "balance" => $oldest_bal->qty_in + ($curr_qty + $oldest_bal->qty_out),
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                } else {
                    $carry_over_bal = $curr_qty + $oldest_bal->balance;

                    DB::table('locationproduct_cost')->
                    whereId($oldest_bal->id)->
                    update([
                        "qty_out" => $oldest_bal->qty_in * -1,
                        "balance" => 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                    ]);

                    // 2nd Level
                    $oldest_bal = DB::table('locationproduct_cost')->
                        join(
                            'locprod_productledger',
                            'locprod_productledger.id',
                            'locationproduct_cost.locprodprodledger_id'
                        )->
                        select(
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
                        )->
                        where("locprod_productledger.product_systemid", $systemid)->
                        where('locationproduct_cost.balance', '>', 0)->
                        orderBy('locationproduct_cost.created_at', 'asc')->
                        first();

                    if (!empty($oldest_bal)) {

                        $compare2 = $carry_over_bal;

                        if ($oldest_bal->balance >= ($compare2 * -1)) {
                            DB::table('locationproduct_cost')->
                            whereId($oldest_bal->id)->
                            update([
                                "qty_out" => $carry_over_bal,
                                "balance" => $oldest_bal->balance + $carry_over_bal,
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);

                        } else {
                            $carry_over_bal = $carry_over_bal + $oldest_bal->balance;

                            DB::table('locationproduct_cost')->
                            whereId($oldest_bal->id)->
                            update([
                                "qty_out" => $oldest_bal->qty_in * -1,
                                "balance" => 0,
                                "updated_at" => date('Y-m-d H:i:s'),
                            ]);

                            // 3rd Level
                            $oldest_bal = DB::table('locationproduct_cost')->
                                join(
                                    'locprod_productledger',
                                    'locprod_productledger.id',
                                    'locationproduct_cost.locprodprodledger_id'
                                )->
                                select(
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
                                )->
                                where("locprod_productledger.product_systemid", $systemid)->
                                where('locationproduct_cost.balance', '>', 0)->
                                orderBy('locationproduct_cost.created_at', 'asc')->
                                first();

                            if (!empty($oldest_bal)) {

                                $compare3 = $carry_over_bal;

                                if ($oldest_bal->balance >= ($compare3 * -1)) {
                                    DB::table('locationproduct_cost')->
                                    whereId($oldest_bal->id)->
                                    update([
                                        "qty_out" => $carry_over_bal,
                                        "balance" => $oldest_bal->qty_in + $carry_over_bal,
                                        "updated_at" => date('Y-m-d H:i:s'),
                                    ]);

                                } else {
                                    $carry_over_bal = $carry_over_bal + $oldest_bal->balance;

                                    DB::table('locationproduct_cost')->
                                    whereId($oldest_bal->id)->
                                    update([
                                        "qty_out" => $oldest_bal->qty_in * -1,
                                        "balance" => 0,
                                        "updated_at" => date('Y-m-d H:i:s'),
                                    ]);

                                    // 4th Level
                                    $oldest_bal = DB::table('locationproduct_cost')->
                                        join(
                                            'locprod_productledger',
                                            'locprod_productledger.id',
                                            'locationproduct_cost.locprodprodledger_id'
                                        )->
                                        select(
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
                                        )->
                                        where("locprod_productledger.product_systemid", $systemid)->
                                        where('locationproduct_cost.balance', '>', 0)->
                                        orderBy('locationproduct_cost.created_at', 'asc')->
                                        first();

                                    if (!empty($oldest_bal)) {

                                        $compare4 = $carry_over_bal;

                                        if ($oldest_bal->balance >= ($compare4 * -1)) {
                                            DB::table('locationproduct_cost')->
                                            whereId($oldest_bal->id)->
                                            update([
                                                "qty_out" => $carry_over_bal,
                                                "balance" => $oldest_bal->qty_in + $carry_over_bal,
                                                "updated_at" => date('Y-m-d H:i:s'),
                                            ]);

                                        } else {
                                            $carry_over_bal = $carry_over_bal + $oldest_bal->balance;

                                            DB::table('locationproduct_cost')->
                                            whereId($oldest_bal->id)->
                                            update([
                                                "qty_out" => $oldest_bal->qty_in * -1,
                                                "balance" => 0,
                                                "updated_at" => date('Y-m-d H:i:s'),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(500);
        }
    }

    public function stockUpdate($qty, $product_id, $stock_type)
    {
        // helo
        Log::debug('****stockUpdate()*****');
        try {
            $user_id = \Auth::user()->id;
            $table_data = [['qty' => $qty, 'product_id' => $product_id]];
            $stock_type = $stock_type;

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
                $locationproduct = DB::table('locationproduct')->
                where([
                    'product_id' => $value['product_id'],
                ])->first();

                if ($locationproduct) { // modify existing location product

                    $locationproduct = DB::table('locationproduct')->
                    where([
                        'product_id' => $value['product_id'],
                    ])->increment('quantity', $curr_qty);
                } else {
                    DB::table('locationproduct')->
                    insert([
                        "location_id" => $location->id,
                        "product_id" => $value['product_id'],
                        "quantity" => $curr_qty,
                        "damaged_quantity" => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

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

                } else if ($stock_type == "OUT") {
                    $earliest_cost = DB::table('locprod_productledger')->
                        where('product_systemid', $prd->systemid)->
                        whereNotNull('cost')->
                        orderBy('created_at', 'asc')->
                        first();

                    $cost = empty($earliest_cost) ? 0 : $earliest_cost->cost;

                    $openitemprodid = DB::table('openitem_productledger')->
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

                    $this->process_locprod_stockout($prd->systemid, $curr_qty);
                }

                PrdOpenitem::where('product_id', $value['product_id'])->
                    get()->map(function ($f) {
                    $f->qty = app("App\Http\Controllers\CentralStockMgmtController")->
                        qtyAvailable($f->product_id);
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
}
