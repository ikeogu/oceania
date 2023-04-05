<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FuelReceipt extends Model
{
    use HasFactory;

    protected $table = "fuel_receipt";

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'void_user_id', 'id');
    }

    public static function get_terminal_ids()
    {
        $ids = [];
        $terminal_ids = DB::table('terminal')->whereNull('deleted_at')->whereNotNull('client_ip')->where('status', 'active')->get();

        foreach ($terminal_ids as $id) {
            $ids[] = $id->id;
        }
        return $ids;
    }

    public static function getReceiptValueWithoutVoid($brancheoddata, $eod = false)
    {
        if ($eod) {
            $brancheoddata->terminal_id = self::get_terminal_ids();
        } else {
            $brancheoddata->terminal_id = [$brancheoddata->terminal_id];
        }

        $fuel_receipts = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptdetails.* ,
			fuel_receipt.id as receipt_id,
			fuel_receipt.created_at as created_at ')->join(
                'fuel_receiptdetails',
                'fuel_receiptdetails.receipt_id',
                'fuel_receipt.id'
            )->whereNull('fuel_receipt.deleted_at')->whereIn('fuel_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'fuel_receipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->whereNotIn('fuel_receipt.status', ['voided', 'refunded'])->orderBy('fuel_receipt.id', 'DESC')->get();

        $oew_receipts = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptdetails.* , oew_receiptlist.* ,
			oew_receipt.id as receipt_id,
			oew_receipt.created_at as created_at ')->join(
                'oew_receiptdetails',
                'oew_receiptdetails.receipt_id',
                'oew_receipt.id'
            )->join(
                'oew_receiptlist',
                'oew_receiptlist.oew_receipt_id',
                'oew_receipt.id'
            )->whereNull('oew_receipt.deleted_at')->whereIn('oew_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'oew_receipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->whereNotIn('oew_receipt.status', ['voided', 'refunded'])->orderBy('oew_receipt.id', 'DESC')->get();

        $fulltank_receipts = DB::table('fuelfulltank_receipt')->selectRaw('fuelfulltank_receipt.* , fuelfulltank_receiptdetails.* ,
			fuelfulltank_receipt.id as receipt_id,
			fuelfulltank_receipt.created_at as created_at')->join(
                'fuelfulltank_receiptdetails',
                'fuelfulltank_receiptdetails.fulltank_receipt_id',
                'fuelfulltank_receipt.id'
            )->whereNull('fuelfulltank_receipt.deleted_at')->whereIn('fuelfulltank_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'fuelfulltank_receipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->where('fuelfulltank_receipt.status', "!=", 'voided')->orderBy('fuelfulltank_receipt.id', 'DESC')->get();
        $ev_receipts = DB::table('evreceipt')->selectRaw('evreceipt.* , evreceiptdetails.* ,
            evreceipt.id as receipt_id,
            evreceipt.created_at as created_at ')->join(
                'evreceiptdetails',
                'evreceiptdetails.evreceipt_id',
                'evreceipt.id'
            )->whereNull('evreceipt.deleted_at')->whereIn('evreceipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'evreceipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->where('evreceipt.status', "!=", 'voided')->orderBy('evreceipt.id', 'DESC')->get();

        $cstore_receipts = DB::table('cstore_receipt')->selectRaw('cstore_receipt.* , cstore_receiptdetails.*,
			cstore_receipt.id as receipt_id ,
			cstore_receipt.created_at as created_at ')->join(
                'cstore_receiptdetails',
                'cstore_receiptdetails.receipt_id',
                'cstore_receipt.id'
            )->whereNull('cstore_receipt.deleted_at')->whereIn('cstore_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'cstore_receipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->where('cstore_receipt.status', "!=", 'voided')->orderBy('cstore_receipt.id', 'DESC')->get();

        $h2_receipts = DB::table('h2receipt')->selectRaw('h2receipt.* , h2receiptdetails.*,
			h2receipt.id as receipt_id,
			h2receipt.created_at as created_at')->join(
                'h2receiptdetails',
                'h2receiptdetails.receipt_id',
                'h2receipt.id'
            )->whereNull('h2receipt.deleted_at')->whereIn('h2receipt.terminal_id', $brancheoddata->terminal_id)->whereDate(
                'h2receipt.created_at',
                date('Y-m-d', strtotime($brancheoddata->created_at))
            )->where('h2receipt.status', "!=", 'voided')->orderBy('h2receipt.id', 'DESC')->get();

        $fuel_refunds = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptlist.* ,  fuel_receipt.id as receipt_id,
            fuel_receipt.created_at as created_at ')->join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')->whereNull('fuel_receipt.deleted_at')->whereIn('fuel_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate('fuel_receipt.created_at',  date('Y-m-d', strtotime($brancheoddata->created_at)))->where('fuel_receipt.status', 'refunded')->orderBy('fuel_receipt.id', 'DESC')->get();
        Log::info("FR : " . json_encode($fuel_refunds));

        $oew_refunds = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptlist.* ,  oew_receipt.id as receipt_id,
            oew_receipt.created_at as created_at ')->join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')->whereNull('oew_receipt.deleted_at')->whereIn('oew_receipt.terminal_id', $brancheoddata->terminal_id)->whereDate('oew_receipt.created_at',  date('Y-m-d', strtotime($brancheoddata->created_at)))->where('oew_receipt.status', 'refunded')->orderBy('oew_receipt.id', 'DESC')->get();
        Log::info("OEW Refunds : " . json_encode($oew_refunds));

        $eodItemAmount = 0;
        $eodRound = 0;
        $eodCreditCard = 0;
        $eodCash = 0;
        $eodWallet = 0;
        $eodOew = 0;
        $eodcreditAccount = 0;
        $eodTax = 0;
        $eodChange = 0;
        $eodTotal = 0;
        $eodDiscount = 0;
        $totalCashRound = 0;
        $totalCreditCardRound = 0;
        $totalCreditAcRound = 0;
        $totalWalletRound = 0;
        $totalOewRound = 0;
        $fuelround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $fulltankround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $oewround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $cstoreround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $evround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $h2round = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];

        foreach ($fuel_receipts as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->item_amount;
            $eodRound += $fuel_receipt->rounding;
            Log::debug('FUEL REF  eodRound=' . $eodRound .
                ', round=' . $fuel_receipt->rounding);
            $eodTax += $fuel_receipt->sst;
            $eodTotal += $fuel_receipt->total;
            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += $fuel_receipt->total;
                    $eodChange += $fuel_receipt->change;
                    $fuelround['cash'] += $fuel_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $fuel_receipt->total;
                    $fuelround['creditcard'] += $fuel_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $fuel_receipt->total;
                    $fuelround['wallet'] += $fuel_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $fuel_receipt->total;
                    $fuelround['oew'] += $fuel_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    $fuelround['creditac'] += $fuel_receipt->rounding;
                    break;
            }
        }

        foreach ($oew_receipts as $oew_receipt) {
            $eodItemAmount += $oew_receipt->item_amount;
            $eodRound += $oew_receipt->rounding;
            Log::debug('OEW REF  eodRound=' . $eodRound .
                ', round=' . $oew_receipt->rounding);
            $eodTax += $oew_receipt->sst;
            $eodTotal += $oew_receipt->total;
            switch ($oew_receipt->payment_type) {
                case "cash":
                    $eodCash += $oew_receipt->total;
                    $eodChange += $oew_receipt->change;
                    $oewround['cash'] += $oew_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $oew_receipt->total;
                    $oewround['creditcard'] += $oew_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $oew_receipt->total;
                    $oewround['wallet'] += $oew_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += ($oew_receipt->total);
                    $oewround['oew'] += $oew_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $oew_receipt->total;
                    $oewround['creditac'] += $oew_receipt->rounding;
                    break;
            }
        }

        foreach ($fulltank_receipts as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->item_amount;
            Log::debug('FT   eodItemAmount=' . $eodItemAmount .
                ', item_amount=' . $fuel_receipt->item_amount);

            $eodRound += $fuel_receipt->rounding;
            $eodTax += $fuel_receipt->sst;
            $eodTotal += $fuel_receipt->total;
            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += $fuel_receipt->total;
                    $eodChange += $fuel_receipt->change;
                    $fulltankround['cash'] += $fuel_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $fuel_receipt->total;
                    $fulltankround['creditcard'] += $fuel_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $fuel_receipt->total;
                    $fulltankround['wallet'] += $fuel_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $fuel_receipt->total;
                    $fulltankround['oew'] += $fuel_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    $fulltankround['creditac'] += $fuel_receipt->rounding;
                    break;
            }
        }
        foreach ($fuel_refunds as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->newsales_item_amount;
            $eodRound += $fuel_receipt->newsales_rounding;
            Log::debug('FUEL REF  eodRound=' . $eodRound .
                ', round=' . $fuel_receipt->newsales_rounding);
            $eodTax += $fuel_receipt->newsales_tax;
            $eodTotal += ($fuel_receipt->total - $fuel_receipt->refund);
            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "creditcard":
                    $eodCreditCard += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "oew":
                    $eodOew += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "creditac":
                    $eodcreditAccount += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
            }
        }

        foreach ($oew_refunds as $oew_receipt) {
            $eodItemAmount += $oew_receipt->newsales_item_amount;
            $eodRound += $oew_receipt->newsales_rounding;
            Log::debug('OEW REF  eodRound=' . $eodRound .
                ', round=' . $oew_receipt->newsales_rounding);
            $eodTax += $oew_receipt->newsales_tax;
            $eodTotal += ($oew_receipt->total - $oew_receipt->refund);
            switch ($oew_receipt->payment_type) {
                case "cash":
                    $eodCash += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "creditcard":
                    $eodCreditCard += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "oew":
                    $eodOew += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "creditac":
                    $eodcreditAccount += ($oew_receipt->total - $oew_receipt->refund);
                    break;
            }
        }



        foreach ($cstore_receipts as $cstore_receipt) {
            $eodItemAmount += $cstore_receipt->item_amount;

            Log::debug('CSTORE eodItemAmount=' . $eodItemAmount .
                ', item_amount=' . $cstore_receipt->item_amount);

            $eodRound += $cstore_receipt->rounding;
            $eodTax += $cstore_receipt->sst;
            $eodTotal += $cstore_receipt->total;

            switch ($cstore_receipt->payment_type) {
                case "cash":
                    $eodCash += $cstore_receipt->total;
                    //$cstoreround['cash'] += $cstore_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $cstore_receipt->total;
                    //$cstoreround['creditcard'] += $cstore_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $cstore_receipt->total;
                    //$cstoreround['wallet'] += $cstore_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $cstore_receipt->total;
                    //$cstoreround['oew'] += $cstore_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $cstore_receipt->total;
                    //$cstoreround['creditac'] += $cstore_receipt->rounding;
                    break;
            }
        }

        Log::debug('ev_receipts=' . json_encode($ev_receipts));

        foreach ($ev_receipts as $ev_receipt) {
            $eodItemAmount += $ev_receipt->item_amount;

            Log::debug('EV eodItemAmount=' . $eodItemAmount .
                ', item_amount=' . $ev_receipt->item_amount);

            $eodRound += $ev_receipt->rounding;
            $eodTax += $ev_receipt->sst;
            $eodTotal += $ev_receipt->total;

            switch ($ev_receipt->payment_type) {
                case "cash":
                    $eodCash += $ev_receipt->total;
                    $eodChange += $ev_receipt->change;
                    $evround['cash'] += $ev_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $ev_receipt->total;
                    $evround['creditcard'] += $ev_receipt->rounding;
                    break;
                case "wallet":
                    $eodWallet += $ev_receipt->total;
                    $evround['wallet'] += $ev_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $ev_receipt->total;
                    $evround['oew'] += $ev_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $ev_receipt->total;
                    $evround['creditac'] += $ev_receipt->rounding;
                    break;
            }
        }

        Log::debug('h2_receipts=' . json_encode($h2_receipts));

        foreach ($h2_receipts as $h2_receipt) {
            $eodItemAmount += $h2_receipt->item_amount;

            Log::debug(json_encode(['h2 eodItemAmount' => $eodItemAmount, 'item_amount' => $h2_receipt->item_amount]));

            $eodRound   += $h2_receipt->rounding;
            $eodTax     += $h2_receipt->sst;
            $eodTotal   += $h2_receipt->total;

            switch ($h2_receipt->payment_type) {
                case "cash":
                    $eodCash += $h2_receipt->total;
                    $eodChange += $h2_receipt->change;
                    $h2round['cash'] += $h2_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $h2_receipt->total;
                    $h2round['creditcard'] += $h2_receipt->rounding;
                    break;
                case "wallet":
                    $eodWallet += $h2_receipt->total;
                    $h2round['wallet'] += $h2_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $h2_receipt->total;
                    $h2round['oew'] += $h2_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $h2_receipt->total;
                    $h2round['creditac'] += $h2_receipt->rounding;
                    break;
            }
        }

        $totalCashRound = $fuelround['cash'] +
            $fulltankround['cash'] +
            $evround['cash'] +
            $cstoreround['cash'] +
            $oewround['cash'] +
            $h2round['cash'];

        $totalCreditCardRound = $fuelround['creditcard'] +
            $fulltankround['creditcard'] +
            $evround['creditcard'] +
            $cstoreround['creditcard'] +
            $oewround['creditcard'] +
            $h2round['creditcard'];

        $totalCreditAcRound = $fuelround['creditac'] +
            $fulltankround['creditac'] +
            $evround['creditac'] +
            $cstoreround['creditac'] +
            $oewround['creditac'] +
            $h2round['creditac'];

        $totalWalletRound = $fuelround['wallet'] +
            $fulltankround['wallet'] +
            $evround['wallet'] +
            $cstoreround['wallet'] +
            $oewround['wallet'] +
            $h2round['wallet'];

        $totalOewRound = $fuelround['oew'] +
            $fulltankround['oew'] +
            $evround['oew'] +
            $cstoreround['oew'] +
            $oewround['oew'] +
            $h2round['oew'];

        Log::info("EOD ROUND : " . $eodRound);
        return [
            "eodItemAmount" => $eodItemAmount,
            "eodRound" => $eodRound,
            "eodCreditCard" => $eodCreditCard,
            "eodCash" => $eodCash,
            "eodWallet" => $eodWallet,
            "eodOew" => $eodOew,
            "eodcreditAccount" => $eodcreditAccount,
            "eodTax" => $eodTax,
            "eodChange" => $eodChange,
            "eodTotal" => $eodTotal,
            "eodDiscount" => $eodDiscount,
            "totalCashRound" => $totalCashRound,
            "totalCreditCardRound" => $totalCreditCardRound,
            "totalCreditAcRound" => $totalCreditAcRound,
            "totalWalletRound" => $totalWalletRound,
            "totalOewRound" => $totalOewRound,
            "fuelRound" => $fuelround,
            "fulltankRound" => $fulltankround,
            "oewRound" => $oewround,
            "cstoreRound" => $cstoreround,
            "evRound" => $evround,
            "h2Round" => $h2round,
        ];
    }

    public static function getCurrentLoginOut()
    {
        Log::info('LO loginlogout  getCurrentLoginOut: Auth::user()=' .
            json_encode(Auth::user()));

        if (!empty(Auth::user())) {
            return DB::table('loginout')->where("user_id", Auth::user()->id)->whereNull("logout")->first();
        } else {
            return;
        }
    }

    public static function getUserLoginReceiptValueWithoutVoid($eod = false)
    {
        //eod Values
        $loginOut = self::getCurrentLoginOut();
        if (empty($loginOut)) return;

        Log::info('WS loginout=' . json_encode($loginOut));

        $shift_data = DB::table('pshift')->whereId($loginOut->shift_id)->first();

        if (empty($shift_data)) {
            throw new \Exception('WS Error: Shift Not Found! shift_id=' .
                $loginOut->shift_id);
        }

        if ($eod) {
            $shift_data->terminal_id = self::get_terminal_ids();
        } else {
            $shift_data->terminal_id = [$shift_data->terminal_id];
        }

        $fuel_receipts = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptdetails.*,
			fuel_receipt.id as receipt_id,
			fuel_receipt.created_at as created_at ')->join(
                'fuel_receiptdetails',
                'fuel_receiptdetails.receipt_id',
                'fuel_receipt.id'
            )->whereNull('fuel_receipt.deleted_at')->where('fuel_receipt.created_at', '>=', $loginOut->login)->whereIn('fuel_receipt.terminal_id', $shift_data->terminal_id)->where('fuel_receipt.staff_user_id', Auth::user()->id)->whereNotIn('fuel_receipt.status', ['voided', 'refunded'])->orderBy('fuel_receipt.id', 'DESC')->get();

        $oew_receipts = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptdetails.*,
			oew_receipt.id as receipt_id,
			oew_receipt.created_at as created_at ')->join(
                'oew_receiptdetails',
                'oew_receiptdetails.receipt_id',
                'oew_receipt.id'
            )->whereNull('oew_receipt.deleted_at')->where('oew_receipt.created_at', '>=', $loginOut->login)->whereIn('oew_receipt.terminal_id', $shift_data->terminal_id)->where('oew_receipt.staff_user_id', Auth::user()->id)->whereNotIn('oew_receipt.status', ['voided', 'refunded'])->orderBy('oew_receipt.id', 'DESC')->get();

        $fulltank_receipts = DB::table('fuelfulltank_receipt')->selectRaw('fuelfulltank_receipt.* , fuelfulltank_receiptdetails.*,
			fuelfulltank_receipt.id as receipt_id,
			fuelfulltank_receipt.created_at as created_at')->join(
                'fuelfulltank_receiptdetails',
                'fuelfulltank_receiptdetails.fulltank_receipt_id',
                'fuelfulltank_receipt.id'
            )->whereNull('fuelfulltank_receipt.deleted_at')->where('fuelfulltank_receipt.created_at', '>=', $loginOut->login)->whereIn('fuelfulltank_receipt.terminal_id', $shift_data->terminal_id)->where('fuelfulltank_receipt.staff_user_id', Auth::user()->id)->where('fuelfulltank_receipt.status', "!=", 'voided')->orderBy('fuelfulltank_receipt.id', 'DESC')->get();

        $h2_receipts = DB::table('h2receipt')->selectRaw('h2receipt.* , h2receiptdetails.*,
			h2receipt.id as receipt_id ,
			h2receipt.created_at as created_at ')->join(
                'h2receiptdetails',
                'h2receiptdetails.receipt_id',
                'h2receipt.id'
            )->whereNull('h2receipt.deleted_at')->where('h2receipt.created_at', '>=', $loginOut->login)->whereIn('h2receipt.terminal_id', $shift_data->terminal_id)->where('h2receipt.staff_user_id', Auth::user()->id)->where('h2receipt.status', "!=", 'voided')->orderBy('h2receipt.id', 'DESC')->get();

        $ev_receipts = DB::table('evreceipt')->selectRaw('evreceipt.* , evreceiptdetails.*,
            evreceipt.id as receipt_id,
            evreceipt.created_at as created_at ')->join(
                'evreceiptdetails',
                'evreceiptdetails.evreceipt_id',
                'evreceipt.id'
            )->whereNull('evreceipt.deleted_at')->where('evreceipt.created_at', '>=', $loginOut->login)->whereIn('evreceipt.terminal_id', $shift_data->terminal_id)->where('evreceipt.staff_user_id', Auth::user()->id)->where('evreceipt.status', "!=", 'voided')->orderBy('evreceipt.id', 'DESC')->get();

        $cstore_receipts = DB::table('cstore_receipt')->selectRaw('cstore_receipt.* , cstore_receiptdetails.* ,
			cstore_receipt.id as receipt_id ,
			cstore_receipt.created_at as created_at ')->join(
                'cstore_receiptdetails',
                'cstore_receiptdetails.receipt_id',
                'cstore_receipt.id'
            )->whereNull('cstore_receipt.deleted_at')->where('cstore_receipt.created_at', '>=', $loginOut->login)->whereIn('cstore_receipt.terminal_id', $shift_data->terminal_id)->where('cstore_receipt.staff_user_id', Auth::user()->id)->where('cstore_receipt.status', "!=", 'voided')->orderBy('cstore_receipt.id', 'DESC')->get();

        $fuel_refunds = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptlist.* ,  fuel_receipt.id as receipt_id,
            fuel_receipt.created_at as created_at ')->join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')->whereNull('fuel_receipt.deleted_at')->where('fuel_receipt.created_at',  '>=', $loginOut->login)->whereIn('fuel_receipt.terminal_id', $shift_data->terminal_id)->where('fuel_receipt.staff_user_id', Auth::user()->id)->where('fuel_receipt.status', 'refunded')->orderBy('fuel_receipt.id', 'DESC')->get();

        $oew_refunds = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptlist.* ,  oew_receipt.id as receipt_id,
            oew_receipt.created_at as created_at ')->join('oew_receiptlist', 'oew_receiptlist.oew_receipt_id', 'oew_receipt.id')->whereNull('oew_receipt.deleted_at')->where('oew_receipt.created_at',  '>=', $loginOut->login)->whereIn('oew_receipt.terminal_id', $shift_data->terminal_id)->where('oew_receipt.staff_user_id', Auth::user()->id)->where('oew_receipt.status', 'refunded')->orderBy('oew_receipt.id', 'DESC')->get();

        Log::info("cstore_receipts 2=" . json_encode($cstore_receipts));
        Log::info("fuel_receipts 2  =" . json_encode($fuel_receipts));
        Log::info("REF PS : " . json_encode($fuel_refunds));

        $eodItemAmount = 0;
        $eodRound = 0;
        $eodCreditCard = 0;
        $eodCash = 0;
        $eodWallet = 0;
        $eodOew = 0;
        $eodcreditAccount = 0;
        $eodTax = 0;
        $eodChange = 0;
        $eodTotal = 0;
        $eodDiscount = 0;
        $totalCashRound = 0;
        $totalCreditCardRound = 0;
        $totalCreditAcRound = 0;
        $totalWalletRound = 0;
        $totalOewRound = 0;
        $fuelround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $oewround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $fulltankround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $cstoreround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $evround = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];
        $h2round = [
            'creditcard' => 0,
            'cash' => 0,
            'wallet' => 0,
            'oew' => 0,
            'creditac' => 0,
        ];

        foreach ($fuel_receipts as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->item_amount;
            $eodRound += $fuel_receipt->rounding;
            $eodTax += $fuel_receipt->sst;
            $eodTotal += $fuel_receipt->total;

            Log::info('EOD fuel_receipt: eodItemAmount=' . $eodItemAmount);
            Log::info('EOD fuel_receipt: eodRound=' . $eodRound);
            Log::info('EOD fuel_receipt: eodTax=' . $eodTax);
            Log::info('EOD fuel_receipt: eodTotal=' . $eodTotal);

            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += $fuel_receipt->total;
                    $eodChange += $fuel_receipt->change;
                    $fuelround['cash'] += $fuel_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $fuel_receipt->total;
                    $fuelround['creditcard'] += $fuel_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $fuel_receipt->total;
                    $fuelround['wallet'] += $fuel_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $fuel_receipt->total;
                    $fuelround['oew'] += $fuel_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    $fuelround['creditac'] += $fuel_receipt->rounding;
                    break;
            }
        }

        foreach ($oew_receipts as $oew_receipt) {
            $eodItemAmount += $oew_receipt->item_amount;
            $eodRound += $oew_receipt->rounding;
            $eodTax += $oew_receipt->sst;
            $eodTotal += $oew_receipt->total;

            switch ($oew_receipt->payment_type) {
                case "cash":
                    $eodCash += $oew_receipt->total;
                    $eodChange += $oew_receipt->change;
                    $oewround['cash'] += $oew_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $oew_receipt->total;
                    $oewround['creditcard'] += $oew_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $oew_receipt->total;
                    $oewround['wallet'] += $oew_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $oew_receipt->total;
                    $oewround['oew'] += $oew_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $oew_receipt->total;
                    $oewround['creditac'] += $oew_receipt->rounding;
                    break;
            }
        }

        foreach ($fulltank_receipts as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->item_amount;
            $eodRound += $fuel_receipt->rounding;
            $eodTax += $fuel_receipt->sst;
            $eodTotal += $fuel_receipt->total;

            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += $fuel_receipt->total;
                    $eodChange += $fuel_receipt->change;
                    $fulltankround['cash'] += $fuel_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $fuel_receipt->total;
                    $fulltankround['creditcard'] += $fuel_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $fuel_receipt->total;
                    $fulltankround['wallet'] += $fuel_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $fuel_receipt->total;
                    $fulltankround['oew'] += $fuel_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    $fulltankround['creditac'] += $fuel_receipt->rounding;
                    break;
            }
        }

        foreach ($h2_receipts as $h2_receipt) {
            $eodItemAmount += $h2_receipt->item_amount;
            $eodRound += $h2_receipt->rounding;
            $eodTax += $h2_receipt->sst;
            $eodTotal += $h2_receipt->total;
            switch ($h2_receipt->payment_type) {
                case "cash":
                    $eodCash += $h2_receipt->total;
                    $eodChange += $h2_receipt->change;
                    $h2round['cash'] += $h2_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $h2_receipt->total;
                    $h2round['creditcard'] += $h2_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $h2_receipt->total;
                    $h2round['wallet'] += $h2_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $h2_receipt->total;
                    $h2round['oew'] += $h2_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $h2_receipt->total;
                    $h2round['creditac'] += $h2_receipt->rounding;
                    break;
            }
        }

        foreach ($ev_receipts as $ev_receipt) {
            $eodItemAmount += $ev_receipt->item_amount;
            $eodRound += $ev_receipt->rounding;
            $eodTax += $ev_receipt->sst;
            $eodTotal += $ev_receipt->total;

            switch ($ev_receipt->payment_type) {
                case "cash":
                    $eodCash += $ev_receipt->total;
                    $eodChange += $ev_receipt->change;
                    $evround['cash'] += $ev_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $ev_receipt->total;
                    $evround['creditcard'] += $ev_receipt->rounding;
                    break;
                case "wallet":
                    $eodWallet += $ev_receipt->total;
                    $evround['wallet'] += $ev_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $ev_receipt->total;
                    $evround['oew'] += $ev_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $ev_receipt->total;
                    $evround['creditac'] += $ev_receipt->rounding;
                    break;
            }
        }

        foreach ($cstore_receipts as $cstore_receipt) {
            $eodItemAmount += $cstore_receipt->item_amount;
            $eodRound += $cstore_receipt->rounding;
            $eodTax += $cstore_receipt->sst;
            $eodTotal += $cstore_receipt->total;

            switch ($cstore_receipt->payment_type) {
                case "cash":
                    $eodCash += $cstore_receipt->total;
                    $eodChange += $cstore_receipt->change;
                    //$cstoreround['cash'] += $cstore_receipt->rounding;
                    break;
                case "creditcard":
                    $eodCreditCard += $cstore_receipt->total;
                    //$cstoreround['creditcard'] += $cstore_receipt->rounding;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $cstore_receipt->total;
                    //$cstoreround['wallet'] += $cstore_receipt->rounding;
                    break;
                case "oew":
                    $eodOew += $cstore_receipt->total;
                    //$cstoreround['oew'] += $cstore_receipt->rounding;
                    break;
                case "creditac":
                    $eodcreditAccount += $cstore_receipt->total;
                    //$cstoreround['creditac'] += $cstore_receipt->rounding;
                    break;
            }
        }

        foreach ($fuel_refunds as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->newsales_item_amount;
            Log::info("EIAPS:" . $eodItemAmount . ": NSIT : " . $fuel_receipt->newsales_item_amount);
            $eodRound += $fuel_receipt->newsales_rounding;
            Log::debug('FUEL REF  eodRound=' . $eodRound .
                ', round=' . $fuel_receipt->newsales_rounding);
            $eodTax += $fuel_receipt->newsales_tax;
            $eodTotal += ($fuel_receipt->total - $fuel_receipt->refund);
            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "creditcard":
                    $eodCreditCard += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "oew":
                    $eodOew += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
                case "creditac":
                    $eodcreditAccount += ($fuel_receipt->total - $fuel_receipt->refund);
                    break;
            }
        }

        foreach ($oew_refunds as $oew_receipt) {
            $eodItemAmount += $oew_receipt->newsales_item_amount;
            Log::info("EIAPS:" . $eodItemAmount . ": NSIT : " . $oew_receipt->newsales_item_amount);
            $eodRound += $oew_receipt->newsales_rounding;
            Log::debug('FUEL REF  eodRound=' . $eodRound .
                ', round=' . $oew_receipt->newsales_rounding);
            $eodTax += $oew_receipt->newsales_tax;
            $eodTotal += ($oew_receipt->total - $oew_receipt->refund);
            switch ($oew_receipt->payment_type) {
                case "cash":
                    $eodCash += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "creditcard":
                    $eodCreditCard += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "oew":
                    $eodOew += ($oew_receipt->total - $oew_receipt->refund);
                    break;
                case "creditac":
                    $eodcreditAccount += ($oew_receipt->total - $oew_receipt->refund);
                    break;
            }
        }

        $totalCashRound = $fuelround['cash'] +
            $fulltankround['cash'] +
            $evround['cash'] +
            $cstoreround['cash'] +
            $oewround['cash'] +
            $h2round['cash'];

        $totalCreditCardRound = $fuelround['creditcard'] +
            $fulltankround['creditcard'] +
            $evround['creditcard'] +
            $cstoreround['creditcard'] +
            $oewround['creditcard'] +
            $h2round['creditcard'];

        $totalCreditAcRound = $fuelround['creditac'] +
            $fulltankround['creditac'] +
            $evround['creditac'] +
            $cstoreround['creditac'] +
            $oewround['creditac'] +
            $h2round['creditac'];

        $totalWalletRound = $fuelround['wallet'] +
            $fulltankround['wallet'] +
            $evround['wallet'] +
            $cstoreround['wallet'] +
            $oewround['wallet'] +
            $h2round['wallet'];

        $totalOewRound = $fuelround['oew'] +
            $fulltankround['oew'] +
            $evround['oew'] +
            $cstoreround['oew'] +
            $oewround['oew'] +
            $h2round['oew'];

        return [
            "eodItemAmount" => $eodItemAmount,
            "eodRound" => $eodRound,
            "eodCreditCard" => $eodCreditCard,
            "eodCash" => $eodCash,
            "eodWallet" => $eodWallet,
            "eodOew" => $eodOew,
            "eodcreditAccount" => $eodcreditAccount,
            "eodTax" => $eodTax,
            "eodChange" => $eodChange,
            "eodTotal" => $eodTotal,
            "eodDiscount" => $eodDiscount,
            "totalCashRound" => $totalCashRound,
            "totalCreditCardRound" => $totalCreditCardRound,
            "totalCreditAcRound" => $totalCreditAcRound,
            "totalWalletRound" => $totalWalletRound,
            "totalOewRound" => $totalOewRound,
            "fuelRound" => $fuelround,
            "oewRound" => $oewround,
            "fulltankRound" => $fulltankround,
            "cstoreRound" => $cstoreround,
            "evRound" => $evround,
            "h2Round" => $h2round,
        ];
    }

    public static function getUserReceiptValueWithoutVoid($loginOut, $eod = false)
    {
        //eod Values

        $shift_data = DB::table('pshift')->whereId($loginOut->shift_id)->first();
        Log::info(["2. loginOut" => $loginOut]);
        $fuel_receipts = [];
        $cstore_receipts = [];

        if ($eod) {
            $shift_data->terminal_id = self::get_terminal_ids();
        } else {
            $shift_data->terminal_id = [$shift_data->terminal_id];
        }

        if ($loginOut->logout == null) {
            $fuel_receipts = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptdetails.* ,
					fuel_receiptproduct.* ,fuel_receiptlist.* ,
					fuel_receipt.id as receipt_id ,
					fuel_receipt.created_at as created_at ')->join(
                    'fuel_receiptdetails',
                    'fuel_receiptdetails.receipt_id',
                    'fuel_receipt.id'
                )->join(
                    'fuel_receiptproduct',
                    'fuel_receiptproduct.receipt_id',
                    'fuel_receipt.id'
                )->join(
                    'fuel_receiptlist',
                    'fuel_receiptlist.fuel_receipt_id',
                    'fuel_receipt.id'
                )->whereNull('fuel_receipt.deleted_at')->where('fuel_receipt.created_at', '>=', $loginOut->login)->where('fuel_receipt.staff_user_id', $loginOut->user_id)->whereIn('fuel_receipt.terminal_id', $shift_data->terminal_id)->where('fuel_receipt.status', "!=", 'voided')->orderBy('fuel_receipt.id', 'DESC')->get();

            $oew_receipts = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptdetails.* ,
					oew_receiptproduct.* ,oew_receiptlist.* ,
					oew_receipt.id as receipt_id ,
					oew_receipt.created_at as created_at ')->join(
                    'oew_receiptdetails',
                    'oew_receiptdetails.receipt_id',
                    'oew_receipt.id'
                )->join(
                    'oew_receiptproduct',
                    'oew_receiptproduct.receipt_id',
                    'oew_receipt.id'
                )->join(
                    'oew_receiptlist',
                    'oew_receiptlist.oew_receipt_id',
                    'oew_receipt.id'
                )->whereNull('oew_receipt.deleted_at')->where('oew_receipt.created_at', '>=', $loginOut->login)->where('oew_receipt.staff_user_id', $loginOut->user_id)->whereIn('oew_receipt.terminal_id', $shift_data->terminal_id)->where('oew_receipt.status', "!=", 'voided')->orderBy('oew_receipt.id', 'DESC')->get();

            $cstore_receipts = DB::table('cstore_receipt')->selectRaw('cstore_receipt.* , cstore_receiptdetails.* ,
					cstore_receipt.id as receipt_id ,
					cstore_receipt.created_at as created_at ')->join(
                    'cstore_receiptdetails',
                    'cstore_receiptdetails.receipt_id',
                    'cstore_receipt.id'
                )->whereNull('cstore_receipt.deleted_at')->where('cstore_receipt.created_at', '>=', $loginOut->login)->where('cstore_receipt.staff_user_id', $loginOut->user_id)->whereIn('cstore_receipt.terminal_id', $shift_data->terminal_id)->where('cstore_receipt.status', "!=", 'voided')->orderBy('cstore_receipt.id', 'DESC')->get();
        } else {
            $fuel_receipts = DB::table('fuel_receipt')->selectRaw('fuel_receipt.* , fuel_receiptdetails.* ,
					fuel_receiptproduct.* ,fuel_receiptlist.* ,
					fuel_receipt.id as receipt_id ,
					fuel_receipt.created_at as created_at ')->join(
                    'fuel_receiptdetails',
                    'fuel_receiptdetails.receipt_id',
                    'fuel_receipt.id'
                )->join(
                    'fuel_receiptproduct',
                    'fuel_receiptproduct.receipt_id',
                    'fuel_receipt.id'
                )->join(
                    'fuel_receiptlist',
                    'fuel_receiptlist.fuel_receipt_id',
                    'fuel_receipt.id'
                )->whereNull('fuel_receipt.deleted_at')->where('fuel_receipt.created_at', '>=', $loginOut->login)->where('fuel_receipt.created_at', '<', $loginOut->logout)->whereIn('fuel_receipt.terminal_id', $shift_data->terminal_id)->where('fuel_receipt.staff_user_id', $loginOut->user_id)->where('fuel_receipt.status', "!=", 'voided')->orderBy('fuel_receipt.id', 'DESC')->get();

            $oew_receipts = DB::table('oew_receipt')->selectRaw('oew_receipt.* , oew_receiptdetails.* ,
					oew_receiptproduct.* ,oew_receiptlist.* ,
					oew_receipt.id as receipt_id ,
					oew_receipt.created_at as created_at ')->join(
                    'oew_receiptdetails',
                    'oew_receiptdetails.receipt_id',
                    'oew_receipt.id'
                )->join(
                    'oew_receiptproduct',
                    'oew_receiptproduct.receipt_id',
                    'oew_receipt.id'
                )->join(
                    'oew_receiptlist',
                    'oew_receiptlist.oew_receipt_id',
                    'oew_receipt.id'
                )->whereNull('oew_receipt.deleted_at')->where('oew_receipt.created_at', '>=', $loginOut->login)->where('oew_receipt.created_at', '<', $loginOut->logout)->where('oew_receipt.staff_user_id', $loginOut->user_id)->whereIn('oew_receipt.terminal_id', $shift_data->terminal_id)->where('oew_receipt.status', "!=", 'voided')->orderBy('oew_receipt.id', 'DESC')->get();

            $cstore_receipts = DB::table('cstore_receipt')->selectRaw('cstore_receipt.* , cstore_receiptdetails.* ,
					cstore_receipt.id as receipt_id ,
					cstore_receipt.created_at as created_at ')->join(
                    'cstore_receiptdetails',
                    'cstore_receiptdetails.receipt_id',
                    'cstore_receipt.id'
                )->whereNull('cstore_receipt.deleted_at')->where('cstore_receipt.created_at', '>=', $loginOut->login)->where('cstore_receipt.created_at', '<', $loginOut->logout)->whereIn('cstore_receipt.terminal_id', $shift_data->terminal_id)->where('cstore_receipt.staff_user_id', $loginOut->user_id)->where('cstore_receipt.status', "!=", 'voided')->orderBy('cstore_receipt.id', 'DESC')->get();
        }

        /*
        Log::info("cstore_receipt receipt user=" . json_encode($cstore_receipts));
        Log::info("fuel_receipt receipt user  =" . json_encode($fuel_receipts));
		*/

        $eodItemAmount = 0;
        $eodRound = 0;
        $eodCreditCard = 0;
        $eodCash = 0;
        $eodWallet = 0;
        $eodOew = 0;
        $eodcreditAccount = 0;
        $eodTax = 0;
        $eodChange = 0;
        $eodTotal = 0;
        $eodDiscount = 0;

        foreach ($fuel_receipts as $fuel_receipt) {
            $eodItemAmount += $fuel_receipt->item_amount;
            $eodRound += $fuel_receipt->round;
            $eodTax += $fuel_receipt->sst;
            $eodTotal += $fuel_receipt->total;

            switch ($fuel_receipt->payment_type) {
                case "cash":
                    $eodCash += $fuel_receipt->total;
                    $eodChange += $fuel_receipt->change;
                    break;
                case "creditcard":
                    $eodCreditCard += $fuel_receipt->total;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $fuel_receipt->total;
                    break;
                case "oew":
                    $eodOew += $fuel_receipt->total;
                    break;
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    break;
            }
        }

        foreach ($cstore_receipts as $cstore_receipt) {
            $eodItemAmount += $cstore_receipt->item_amount;
            $eodRound += $cstore_receipt->rounding;
            $eodTax += $cstore_receipt->sst;
            $eodTotal += $cstore_receipt->total;

            switch ($cstore_receipt->payment_type) {
                case "cash":
                    $eodCash += $cstore_receipt->total;
                    $eodChange += $cstore_receipt->change;
                    break;
                case "creditcard":
                    $eodCreditCard += $cstore_receipt->total;
                    break;
                case "point":
                    break;
                case "wallet":
                    $eodWallet += $cstore_receipt->total;
                    break;
                case "oew":
                    $eodOew += $cstore_receipt->total;
                    break;
                case "creditac":
                    $eodcreditAccount += $cstore_receipt->total;
                    break;
            }
        }

        return [
            "eodItemAmount" => $eodItemAmount,
            "eodRound" => $eodRound,
            "eodCreditCard" => $eodCreditCard,
            "eodCash" => $eodCash,
            "eodWallet" => $eodWallet,
            "eodOew" => $eodOew,
            "eodcreditAccount" => $eodcreditAccount,
            "eodTax" => $eodTax,
            "eodChange" => $eodChange,
            "eodTotal" => $eodTotal,
            "eodDiscount" => $eodDiscount,
        ];
    }


    public function fuel_receipt_list()
    {
        return $this->hasOne(FuelReceiptList::class, 'fuel_receipt_id', 'id');
    }
}
