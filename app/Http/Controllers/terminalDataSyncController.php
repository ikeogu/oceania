<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DB;

class terminalDataSyncController extends Controller
{
	function syncData() {
		Log::debug('***** syncData() *****');
		try {
			
			$data = [];	
			$client_ip = request()->ip();
    	    $terminal = DB::table('terminal')->
				where('client_ip', $client_ip)->first();

			Log::debug('syncData pump_no='.request()->pump_no);
			Log::debug('syncData terminal='. $terminal->id);

			$data['master_terminal_id']	= $terminal->id;
			$data['product_id']		= request()->product_id ?? 0;
			$data['pump_no']		= request()->pump_no;
			$data['payment_status'] = request()->payment_status;
			$data['dose']			= request()->dose;
			$data['price']			= request()->price;
			$data['litre']			= request()->litre;
			$data['receipt_id']		= request()->receipt_id;
			$data['transactionid']	= request()->transactionid;

			Log::debug($data);

			$is_exist = DB::table('mtermsync')->
				where([
					'pump_no'		=> request()->pump_no
				])->first();

			if (!empty($is_exist)) {
				DB::table('mtermsync')->
					where([
						'pump_no'		=> request()->pump_no
					])->update($data);
				
			} else {
				$data['created_at'] = now();
				$data['updated_at'] = now();
				DB::table('mtermsync')->
					insert($data);
			}

		} catch (\Exception $e) {
			Log::error([
				"msg"	=> $e->getMessage(),
				"File"	=> $e->getFile(),
				"Line"	=> $e->getLine()	
			]);
		}
	}


	function getDataByPump() {
		$pump_no = request()->my_pump;
		
		Log::debug('***** getDataByPump: my_pump='.
			json_encode(request()->my_pump));

		try {
			$client_ip = request()->ip();

			Log::debug('getDataByPump: client_ip='.$client_ip);

			$terminal = DB::table('terminal')->
				where('client_ip', $client_ip)->first();

			Log::debug('getDataByPump: terminal='.
				json_encode($terminal));

			$data =  DB::table('mtermsync')->
				where('master_terminal_id', $terminal->id)->
				where('pump_no', $pump_no)->
				get();

			Log::info('getDataByPump: data='.json_encode($data));

			return response()->json($data);

		} catch (\Exception $e) {
			Log::error([
				"msg"	=> $e->getMessage(),
				"File"	=> $e->getFile(),
				"Line"	=> $e->getLine()	
			]);
		}
	}



	function getData() {
		//Log::debug('***** getData() *****');
		try {
			$client_ip = request()->ip();
    	    $terminal = DB::table('terminal')->
				where('client_ip', $client_ip)->first();

			$data =  DB::table('mtermsync')->
				leftjoin('product','product.id', 'mtermsync.product_id')->
			/*	where([
					'mtermsync.master_terminal_id'	=> $terminal->id,
					'mtermsync.pump_no'		=> request()->pump_no
				])->*/
				select('mtermsync.*', 'product.systemid as psystemid',
					'product.id as product_id','product.name as pname',
					'product.thumbnail_1')->get();

			//Log::debug('getData: data='.json_encode($data));

			return response()->json($data);

		} catch (\Exception $e) {
			Log::error([
				"msg"	=> $e->getMessage(),
				"File"	=> $e->getFile(),
				"Line"	=> $e->getLine()	
			]);
		}
	}


	function deleteDataByTransactionId() {
		Log::debug('***** deleteDataByTranactionId() *****');
		$transid = request()->transactionid;
		
		try {
			$data =  DB::table('mtermsync')->
				where('transactionid', $transid)->
				delete();

			Log::info('deleteDataByTransactionId: data='.json_encode($data));

			return response()->json($data);

		} catch (\Exception $e) {
			Log::error([
				"msg"	=> $e->getMessage(),
				"File"	=> $e->getFile(),
				"Line"	=> $e->getLine()	
			]);
		}
	}


	function deleteData() {
		Log::debug('***** deleteData() *****');

		try {
			$client_ip = request()->ip();
    	    $terminal = DB::table('terminal')->
				where('client_ip', $client_ip)->first();
						
			Log::debug('deleteData: pump_no='.request()->pump_no);
			Log::debug('deleteData: terminal='. $terminal->id);


			DB::table('mtermsync')->
				where([
					'master_terminal_id'	=> $terminal->id,
					'pump_no' => request()->pump_no
				])->delete();

		} catch (Exception $e) {
			Log::error([
				"msg"	=> $e->getMessage(),
				"file"	=> $e->getFile(),
				"line"	=> $e->getLine()	
			]);
		}
	}
}
