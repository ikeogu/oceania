<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Company;
use App\Models\Location;
use App\Models\Terminal;
use App\Models\User;
use Carbon\Carbon;
use Log;
use PDF;



class CStoreAutoPDFController extends Controller
{
    // CStore Auto Sales PDF
    public static function cstoreSalesPDF(Request $request)
    {

        $id = $request->input('receipt_id');
        $dimension = array(0, 0, 226.77, 530);
        $receipt = DB::table('cstore_receipt')->find($id);

        Log::info('cstoreSalesPDF: id=' . $id);
        Log::info('cstoreSalesPDF: receipt=' . json_encode($receipt));

        $receipt = is_null($receipt) ? DB::table('cstore_receipt')->where('systemid', $request->id)->first() : $receipt;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();
        $receiptproduct = DB::table('cstore_receiptproduct')->where('receipt_id', $receipt->id)->get();

        $receiptdetails = DB::table('cstore_receiptdetails')->where('receipt_id', $receipt->id)->first();

        $ref = DB::table('cstore_receiptrefund')->join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->where('cstore_receiptrefund.cstore_receipt_id', $receipt->id)->select('cstore_receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')->first();

        if (!empty($ref)) {
            $ref->refund_amount = $ref->refund_amount / 100;
            $ref->refund_amount += $this->round_amount($ref->refund_amount) / 100;
            Log::debug("Amount =" . $ref->refund_amount);
        }

        $sst = $receiptdetails->sst ?? 0.00;
        $rounding = $receiptdetails->rounding ?? 0.00;
        $item_amount = $receiptdetails->item_amount ?? 0.00;

        $refund = '';

        $directory = Carbon::now()->format('Ymd');
        $pdf = PDF::loadView('cstore.cstore_autopdf', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptproduct',
            'receiptdetails',
            'refund',
            'sst',
            'rounding',
            'item_amount'
        ))->setPaper($dimension);

        $pdf->save(Storage::disk('cstore')->put(
                '' . $directory . '/' . $receipt->systemid . '.pdf',
                $pdf->output()
            ));
    }

    // CStore Void Auto Sales PDF
    public static function cstoreVoidSalesPDF(Request $request)
    {

        $id = $request->input('receipt_id');
        $dimension = array(0, 0, 226.77, 530);
        $receipt = DB::table('cstore_receipt')->find($id);
        $receipt = is_null($receipt) ? DB::table('cstore_receipt')->where('systemid', $request->id)->first() : $receipt;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();
        $receiptproduct = DB::table('cstore_receiptproduct')->where('receipt_id', $receipt->id)->get();

        $receiptdetails = DB::table('cstore_receiptdetails')->where('receipt_id', $receipt->id)->first();

        $ref = DB::table('cstore_receiptrefund')->join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->where('cstore_receiptrefund.cstore_receipt_id', $receipt->id)->select('cstore_receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')->first();

        /*  if (!empty($ref)) {
            $ref->refund_amount += $this->round_amount($ref->refund_amount) / 100;
        }*/

        $sst = $receiptdetails->sst ?? 0.00;
        $rounding = $receiptdetails->rounding ?? 0.00;
        $item_amount = $receiptdetails->item_amount ?? 0.00;

        $refund = '';

        $directory = Carbon::now()->format('Ymd');
        $pdf = PDF::loadView('cstore.cstore_autovoidpdf', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptproduct',
            'receiptdetails',
            'refund',
            'sst',
            'rounding',
            'item_amount'
        ))->setPaper($dimension);

        $pdf->save(Storage::disk('cstore')->put(
                '' . $directory . '/' . $receipt->systemid . '-void.pdf',
                $pdf->output()
            ));
    }

    // CStore Refund Auto Sales PDF
    public function cstoreRefundSalesPDF(Request $request)
    {

        $id = $request->input('receipt_id');
        $dimension = array(0, 0, 226.77, 530);
        $receipt = DB::table('cstore_receipt')->find($id);
        $receipt = is_null($receipt) ? DB::table('cstore_receipt')->where('systemid', $request->id)->first() : $receipt;

        $user = User::where('id', $receipt->staff_user_id)->first();
        $company = Company::where('id', $receipt->company_id)->first();
        $terminal = Terminal::where('id', $receipt->terminal_id)->first();
        $location = Location::first();
        $receiptproduct = DB::table('cstore_receiptproduct')->where('receipt_id', $receipt->id)->get();

        $receiptdetails = DB::table('cstore_receiptdetails')->where('receipt_id', $receipt->id)->first();

        $ref = DB::table('cstore_receiptrefund')->join('users', 'cstore_receiptrefund.staff_user_id', '=', 'users.id')->where('cstore_receiptrefund.cstore_receipt_id', $receipt->id)->select('cstore_receiptrefund.*', 'users.fullname as name', 'users.systemid as systemid')->first();

        if (!empty($ref)) {
            $ref->refund_amount = $ref->refund_amount / 100;
            $ref->refund_amount += $this->round_amount($ref->refund_amount) / 100;
            Log::debug("Amount =" . $ref->refund_amount);
        }

        $sst = $receiptdetails->sst ?? 0.00;
        $rounding = $receiptdetails->rounding ?? 0.00;
        $item_amount = $receiptdetails->item_amount ?? 0.00;

        $refund = '';

        $directory = Carbon::now()->format('Ymd');
        $pdf = PDF::loadView('cstore.cstore_autorefundpdf', compact(
            'company',
            'terminal',
            'location',
            'user',
            'receipt',
            'receiptproduct',
            'receiptdetails',
            'ref',
            'sst',
            'rounding',
            'item_amount'
        ))->setPaper($dimension);

        $pdf->save(Storage::disk('cstore')->put(
                '' . $directory . '/' . $receipt->systemid . '-refund.pdf',
                $pdf->output()
            ));
    }

    private function round_amount($num)
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
}
