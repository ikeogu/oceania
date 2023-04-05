<?php

namespace App\Http\Controllers;
use App\Models\FuelReceipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OptTerminalSyncController extends Controller
{
    //
    function optGetTerminalData() {
		//Log::debug('***** getData() *****');
		try {
			$data =  DB::table('opt_mtermsync')->
				select('opt_mtermsync.*')->get();
			return response()->json($data);

		} catch (Exception $e) {
			Log::info([
				"msg"	=> $e->getMessage(),
				"File"	=> $e->getFile(),
				"Line"	=> $e->getLine()	
			]);

			abort(404);
		}
	}

	public function syncOPT(Request $request)
    {
        try {
            $receipt = new FuelReceipt();
            $receipt->id = 1;

            //Sync tables
            $data = array();
            $data['opt_receipt'] = DB::table('opt_receipt')->whereId($receipt->id)->first();
            $data['opt_receiptdetails'] = DB::table('opt_receiptdetails')->where('receipt_id',$receipt->id)->first();
            $data['opt_receiptproduct'] = DB::table('opt_receiptproduct')->where('receipt_id',$receipt->id)->first();
            $data['opt_receiptlist'] = DB::table('opt_receiptlist')->where('opt_receipt_id',$receipt->id)->first();
            
            $query = "select t.systemid from terminal t, opt_receipt cr where cr.terminal_id = t.id GROUP BY t.id, t.systemid";
            $data['terminal_systemid'] = DB::select(DB::raw($query));

            $response_sync = SyncSalesController::curlRequest(env('MOTHERSHIP_URL') . '/sync-opt-receipt' , json_encode($data));
            return $response_sync;

            return $receipt->id;

        } catch (\Exception $e) {
            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);

            return $e;
        }
    }
}
