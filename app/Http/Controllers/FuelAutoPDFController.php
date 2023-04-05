<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Company;
use App\Models\FuelReceipt;
use App\Models\FuelReceiptdetails;
use App\Models\FuelReceiptList;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Classes\SystemID;
use PDF;
use Illuminate\Support\Facades\Storage;
use Dompdf\Exception;

class FuelAutoPDFController extends Controller
{
    //
    public static function generate_pdf_receipt(Request $request)
    {
        $id = $request->input('receipt_id');
        $dimension = array(0, 0, 226.77, 500);
        try {
            $location = Location::first();
            $company = Company::first();
            Log::debug('Generated PDF fuelReceipt: id      =' . $id);

            //if ($request->source == 'fuel') {

            $receipt = FuelReceipt::with("user")->where('id', $id)->first();

            $receiptdetails = FuelReceiptdetails::where('receipt_id', $id)->first();

            // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuel_receiptproduct')->where('receipt_id', $id)->get();

            // This is obsoleted. Get refund data from fuel_receiptlist table
            $refund = FuelReceiptList::where("fuel_receipt_id", $id)->join('users', 'users.id', 'fuel_receiptlist.refund_staff_user_id')->first();

            $receiptCount = DB::table('creditact_ledger')->where('document_no', $id)->get()->count();

            $terminal = DB::table('terminal')->where('id', $receipt->terminal_id)->first();

            Log::debug('FuelReceipt: receiptdetails=' .
                json_encode($receiptdetails));

            $invoiceName = Carbon::now()->format('Ymd');
            $pdf = PDF::loadView('fuel_receipt.fuel_receipt_autopdf', compact(
                "location",
                "user",
                "receiptproduct",
                "terminal",
                "company",
                "receipt",
                "receiptdetails",
                "refund",
                "receiptCount"
            ))->setPaper($dimension);

            $pdf->save(Storage::disk('local')->put(
                    '' . $invoiceName . '/' . $receipt->systemid . '.pdf',
                    $pdf->output()
                ));
        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }


    public static function generate_void_pdf(Request $request)
    {
        $id = $request->input('receipt_id');
        $dimension = array(0, 0, 226.77, 530);
        try {
            $location = Location::first();
            $company = Company::first();
            Log::debug('Generated Void PDF fuelReceipt: id      =' . $id);

            //if ($request->source == 'fuel') {

            $receipt = FuelReceipt::with("user")->where('id', $id)->first();
            $receiptdetails = FuelReceiptdetails::where('receipt_id', $id)->first();

            // Fetch staff user from receipt NOT from currently
            // logged in user
            $user = DB::table('users')->where('id', $receipt->staff_user_id)->first();

            $receiptproduct = DB::table('fuel_receiptproduct')->where('receipt_id', $id)->get();

            // This is obsoleted. Get refund data from fuel_receiptlist table
            $refund = FuelReceiptList::where("fuel_receipt_id", $id)->join('users', 'users.id', 'fuel_receiptlist.refund_staff_user_id')->first();

            $receiptCount = DB::table('creditact_ledger')->where('document_no', $id)->get()->count();

            $terminal = DB::table('terminal')->where('id', $receipt->terminal_id)->first();

            Log::debug('FuelReceiptVoid: receiptdetails=' .
                json_encode($receiptdetails));

            $invoiceName = Carbon::now()->format('Ymd');
            $pdf = PDF::loadView('fuel_receipt.fuel_receipt_autovoidpdf', compact(
                "location",
                "user",
                "receiptproduct",
                "terminal",
                "company",
                "receipt",
                "receiptdetails",
                "refund",
                "receiptCount"
            ))->setPaper($dimension);

            $pdf->save(Storage::disk('local')->put(
                    $invoiceName . '/' . $receipt->systemid . '-void.pdf',
                    $pdf->output()
                ));
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }
}
