<?php

namespace App\Http\Controllers;

use DB;
use Log;
use \App\Classes\PTS2;
use \App\Classes\UserData;
use App\Models\OgFuel;
use App\Models\product;
use Illuminate\Http\Request;
use App\Models\og_pumplog;
use Illuminate\Support\Facades\Auth;

class H2Controller extends Controller
{
    public function h2SyncData() {
    Log::debug('***** H2- Hydrogen h2SyncData() *****');
    try {

      $data = [];
      $client_ip = request()->ip();
          $terminal = DB::table('terminal')->
        where('client_ip', $client_ip)->first();

      Log::debug('H2 - syncData pump_no='.request()->pump_no);
      Log::debug('H2 - syncData terminal='. $terminal->id);

      $data['master_terminal_id']	= $terminal->id;
      $data['product_id']		= 0;
      $data['pump_no']		= request()->pump_no;
      $data['payment_status'] = "Not Paid";
      $data['dose']			= 0.00;
      $data['price']			= 0.00;
      $data['litre']			= 0;
      $data['h2_receipt_id']	= null;
            //dd($data);
      Log::debug($data);

      $is_exist = DB::table('h2_mtermsync')->
        where([
          'pump_no'		=> request()->pump_no
        ])->first();

      if (!empty($is_exist)) {
        DB::table('h2_mtermsync')->
          where([
            'pump_no'		=> request()->pump_no
          ])->update($data);

      } else {
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('h2_mtermsync')->
          insert($data);
      }

    } catch (\Exception $e) {
      \Log::info([
        "msgy"	=> $e->getMessage(),
        "File"	=> $e->getFile(),
        "Line"	=> $e->getLine()
      ]);

      abort(404);
    }
  }

  public function h2GetData() {
		Log::debug('***** Hydrogen h2GetData() *****');
		try {
			$client_ip = request()->ip();
    	    $terminal = DB::table('terminal')->
				where('client_ip', $client_ip)->first();

			$data =  DB::table('h2_mtermsync')->
				select('h2_mtermsync.*')->get();

			//Log::debug('getData: data='.json_encode($data));

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

  public function h2PumpGetStatus($pumpNo, $ipaddr) {

  $conn_fail = false;

  if (empty($ipaddr)) {
    $url = env('PTS_URL');
  } else {
    $url = "http://".$ipaddr."/jsonPTS";
  }

      $pts2 = new PTS2(env('PTS_USER'), env('PTS_PASSWD'), $url);
      $res = $pts2->pump_get_status($pumpNo);

  if (!empty($res) || !empty($res->response) || !empty($ipaddr)) {

    if ($pumpNo==5 or $pumpNo==5) {
    //Log::debug('1. PTS '.$pumpNo.': '.json_encode($res));

    /*
    Log::debug('pumpGetStatus: ipaddr='.$ipaddr);
    Log::debug('pumpGetStatus: url   ='.$url);
    Log::debug('pumpGetStatus: env(PTS_USER)   ='.env('PTS_USER'));
    Log::debug('pumpGetStatus: env(PTS_PASSWD) ='.env('PTS_PASSWD'));
    */
    }
  }

      $pts2->close_channel();

  /* Have to test for connection failures.
   * We equate connection failures to pump offline */
  if ($res['response'] == null) {

    //Log::debug('pumpGetStatus: NO RESPONSE from pump '.$pumpNo);

    /* We have connection failure! */
    $conn_fail = true;

    $res['response'] = array(
      'conn_fail' => $conn_fail,
      "Protocol" => "jsonPTS",
      "Packets" => array(array(
        "Id" => 1,
        "Type" => "PumpOfflineStatus",
        "Data" => array(
          "Pump" => $pumpNo,
          "User" => "admin"
        )
      )
    ));
  }

  if (!empty($res->response)) {
    Log::debug('2. PTS '.$pumpNo.': '.json_encode($res));
  }

      return response()->json([ 'data' => $res ]);
  }


      public function pumpAuthorize($pumpNo,$type,$dose,$ipaddr,
  		$nozzle=null,$fuelgradeid=null,$price=null){

          /* Note case sensitive: type=(Volume|Amount|FullTank) */
  		if ($type == null) $type = 'Amount';

  		Log::debug('pumpAuthorize:'.$pumpNo.', type='.$type.
  			', dose='.$dose. ',nozzle='.$nozzle.
  			', fuelgradeid='.$fuelgradeid.', price='.$price);

  		if (empty($ipaddr)) {
  			$url = env('PTS_URL');
  		} else {
  			$url = "http://".$ipaddr."/jsonPTS";
  		}

  		if ($nozzle == 'null') $nozzle = null;

          $pts2 = new PTS2(env('PTS_USER'), env('PTS_PASSWD'), $url);

  		/* Hydrogen processing */
  		if (empty($dose) or $dose == 0) {
  			$dose = 0;
  			$type = 'Hydrogen';
  		}

  		$res = $pts2->pump_authorize($pumpNo, $type, $dose,
  			$nozzle, $fuelgradeid, $price);

  		Log::debug('pumpAuthorize:'.json_encode($res));

          $pts2->close_channel();
          return response()->json(['data' => $res]);
      }

  public function pumpCancelAuthorize($pumpNo, $ipaddr) {
  Log::debug('***** PS pumpCancelAuthorize('.$pumpNo.') *****');

  if (empty($ipaddr)) {
    $url = env('PTS_URL');
  } else {
    $url = "http://".$ipaddr."/jsonPTS";
  }

      $pts2 = new PTS2(env('PTS_USER'), env('PTS_PASSWD'), $url);
      $res = $pts2->pump_stop($pumpNo);
      $pts2->close_channel();
      return response()->json([ 'data' => $res ]);
  }

  	function h2DeleteData() {
  		Log::debug('***** H2 - Hydrogen deleteData() *****');

  		try {
  			$client_ip = request()->ip();
      	    $terminal = DB::table('terminal')->
  				where('client_ip', $client_ip)->first();

  			Log::debug('H2 - deleteData: pump_no='.request()->pump_no);
  			Log::debug('H2 - deleteData: terminal='. $terminal->id);


  			DB::table('h2_mtermsync')->
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
