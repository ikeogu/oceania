<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FuelFulltankReceipt extends Model
{
    use HasFactory;
    protected $table = "fuelfulltank_receipt";
    public function fuel_fulltank_receipt_list()
    {
        return $this->hasOne(FuelFulltankReceiptList::class, 'fuel_fulltank_receipt_id', 'id');
    }
    public static function getFulltankCurrentLoginOut()
    {
        $loginOut = DB::table('loginout')->
            where("user_id", Auth::user()->id)->
            where("logout", null)->
            first();
        return $loginOut;
    }
    public static function getFulltankReceiptValue($brancheoddata)
    {
        //eod Values
        $fuel_receipts = DB::table('fuelfulltank_receipt')->
            selectRaw('fuelfulltank_receipt.* , fuelfulltank_receiptdetails.* ,
			fuelfulltank_receipt.id as receipt_id,
			fuelfulltank_receipt.created_at as created_at')->
            join('fuelfulltank_receiptdetails', 'fuelfulltank_receiptdetails.fulltank_receipt_id',
            'fuelfulltank_receipt.id')->
            whereNull('fuelfulltank_receipt.deleted_at')->
            whereDate('fuelfulltank_receipt.created_at',
            date('Y-m-d', strtotime($brancheoddata->created_at)))->
            where('fuelfulltank_receipt.status', "!=", 'voided')->
            orderBy('fuelfulltank_receipt.id', 'DESC')->get();
        $eodItemAmount = 0;
        $eodRound = 0;
        $eodCreditCard = 0;
        $eodCash = 0;
        $eodWallet = 0;
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
                    $eodCash +=
                        ($fuel_receipt->cash_received - $fuel_receipt->change);
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
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    break;
            }
        }
        $return_array =  [
            "eodItemAmount" => $eodItemAmount,
            "eodRound" => $eodRound,
            "eodCreditCard" => $eodCreditCard,
            "eodCash" => $eodCash,
            "eodWallet" => $eodWallet,
            "eodcreditAccount" => $eodcreditAccount,
            "eodTax" => $eodTax,
            "eodChange" => $eodChange,
            "eodTotal" => $eodTotal,
            "eodDiscount" => $eodDiscount,
        ];
        return $return_array;
    }
    public static function getFulltankUserLoginReceiptValue()
    {
        //eod Values
        $loginOut = self::getFulltankCurrentLoginOut();

        Log::info(["FT - loginOut" => $loginOut]);

        $fuel_receipts = DB::table('fuelfulltank_receipt')->
            selectRaw('fuelfulltank_receipt.* , fuelfulltank_receiptdetails.*,
			fuelfulltank_receipt.id as receipt_id,
			fuelfulltank_receipt.created_at as created_at')->
            join('fuelfulltank_receiptdetails', 'fuelfulltank_receiptdetails.fulltank_receipt_id',
            'fuelfulltank_receipt.id')->
            whereNull('fuelfulltank_receipt.deleted_at')->
            where('fuelfulltank_receipt.created_at', '>=', $loginOut->login)->
            where('fuelfulltank_receipt.staff_user_id', Auth::user()->id)->
            where('fuelfulltank_receipt.status', "!=", 'voided')->
            orderBy('fuelfulltank_receipt.id', 'DESC')->get();

        Log::info("fuelfulltank_receipts 2  =" . json_encode($fuel_receipts));

        $eodItemAmount = 0;
        $eodRound = 0;
        $eodCreditCard = 0;
        $eodCash = 0;
        $eodWallet = 0;
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
                    $eodCash +=
                        ($fuel_receipt->cash_received - $fuel_receipt->change);
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
                case "creditac":
                    $eodcreditAccount += $fuel_receipt->total;
                    break;
            }
        }
        return [
            "eodItemAmount" => $eodItemAmount,
            "eodRound" => $eodRound,
            "eodCreditCard" => $eodCreditCard,
            "eodCash" => $eodCash,
            "eodWallet" => $eodWallet,
            "eodcreditAccount" => $eodcreditAccount,
            "eodTax" => $eodTax,
            "eodChange" => $eodChange,
            "eodTotal" => $eodTotal,
            "eodDiscount" => $eodDiscount,
        ];
    }
}
