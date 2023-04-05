<?php
namespace App\Classes;
use Log;
use GuzzleHttp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \App\Console\Commands\invfcc_gatewayd;
use \app\http\controllers\opospetrolstationpumpcontroller;

/* This is the protocol implementation of:
InvFCC
InvFCC API Specification
Version 1.X
June 2020
*/

class InvFCC
{
	private $url;
	private $ipaddr;
    private $username;
    private $password;
	private $ch = null;
	public static $mcnt = 0;
	public static array $xdet = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
	public static array $rsrv = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];

    public function __construct($ipaddr=null) {
		$this->url = env('PTS_URL');
		$this->ipaddr = env('PTS_IPADDR');
		$this->username = (!empty($username)) ? $username : env('PTS_USER');
		$this->password = (!empty($password)) ? $password : env('PTS_PASSWD');

		for ($i=1; $i<20; $i++) {
			self::$xdet[$i];
			self::$rsrv[$i];
		}


		Log::debug('url='.$this->url);
		Log::debug('ipaddr='.$this->ipaddr);
		Log::debug('username='.$this->username);
		Log::debug('password='.$this->password);
    }


	public function set_channel() {
		$url = $this->url;
		$this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_USERPWD,"$this->username:$this->password");

		//dump('url='.$url);

		//curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, 10);
		//curl_setopt($this->ch,CURLOPT_TIMEOUT, 10);

		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}


	public static function put_trans_no($pump_no, $trans_no) {
		self::$xdet[$pump_no] = $trans_no;
	}

	public static function get_trans_no($pump_no) {
		return self::$xdet[$pump_no];
	}

	public static function put_rsrv_id($pump_no, $reservation_id) {
		self::$rsrv[$pump_no] = $reservation_id;
	}

	public static function get_rsrv_id($pump_no) {
		return self::$rsrv[$pump_no];
	}

	public static function get_mcnt() {
		return self::$mcnt++;
	}

	public function get_config_response($data, $prod) {

		/*
		$json_response = '{
			"Header": {
				"Api":"infx:fcc:0.9",
				"MessageType":"FCCGetConfigResponse",
				"MessageID":5,
				"ResponseID":44
			},
			"Payload":{
				"Result": {
					"OverallResult":"Success",
					"ErrorCode":"0",
					"ErrorText":""
				},
				"FuelPointConfigList":[{
					"DeviceID":3,
					"Nozzles":[{
						"NozzleID":1,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"1.250",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"1.350",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":100,
							"Name":"Super 91",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":2,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"1.450",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"1.550",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":200,
							"Name":"Super 95",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":3,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"1.850",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"1.950",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":300,
							"Name":"Super 98",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					}]
				},{
					"DeviceID":20,
					"Nozzles":[{
						"NozzleID":1,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"1.250",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"1.350",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":100,
							"Name":"Super 91",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":2,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"1.450",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"1.550",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":200,
							"Name":"Super 95",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":3,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"2.100",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						},{
							"FuelModeID":1,
							"Price":"2.200",
							"MaxVolume":"50.000",
							"MaxAmount":"200.000"
						}],
						"Product":{
							"ProductID":300,
							"Name":"WhiteKnuckle",
							"BlendRatio":10,
							"ProductID1":200,
							"ProductID2":9993
						}
					}]
				}]
			}}';
		*/

		$msgid = $data->Header->MessageID;

		$json_response = '{
			"Header": {
				"Api":"infx:fcc:0.9",
				"MessageType":"FCCGetConfigResponse",
				"ResponseID":'.$msgid.',
				"ApplicationSender":"InfxFCC",
				"WorkstationID":"OPOSSUM_SVR",
				"MessageID":'.InvFCC::get_mcnt() .',
				"TXTimeStamp":"'.Carbon::now().'"
			},
			"Payload":{
				"Result": {
					"OverallResult":"Success",
					"ErrorCode":"ERRCD_OK",
					"ErrorText":""
				},
				"FuelPointConfigList":[{
					"DeviceID":6,
					"Nozzles":[{
						"NozzleID":1,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"0.00",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						},{
							"FuelModeID":1,
							"Price":"'.number_format($prod[1]->price,2).'",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						}],
						"Product":{
							"ProductID":'.$prod[1]->ogfuel_id.',
							"Name":"'.$prod[1]->name.'",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":2,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"0.00",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						},{
							"FuelModeID":1,
							"Price":"'.number_format($prod[2]->price,2).'",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						}],
						"Product":{
							"ProductID":'.$prod[2]->ogfuel_id.',
							"Name":"'.$prod[2]->name.'",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":3,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"0.00",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						},{
							"FuelModeID":1,
							"Price":"'.number_format($prod[3]->price,2).'",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						}],
						"Product":{
							"ProductID":'.$prod[3]->ogfuel_id.',
							"Name":"'.$prod[3]->name.'",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					},{
						"NozzleID":4,
						"CurrentFuelMode":1,
						"FuelModeConfigList":[{
							"FuelModeID":0,
							"Price":"0.00",
							"MaxVolume":"50.00",
							"MaxAmount":"200.00"
						},{
							"FuelModeID":1,
							"Price":"'.number_format($prod[4]->price,2).'",
							"MaxVolume":"0.00",
							"MaxAmount":"200.00"
						}],
						"Product":{
							"ProductID":'.$prod[4]->ogfuel_id.',
							"Name":"'.$prod[4]->name.'",
							"BlendRatio":0,
							"ProductID1":0,
							"ProductID2":0
						}
					}
				]}
			]}
		}';

		return $json_response;
	}


	public function get_fuelmode_response($data) {
		$msgid = $data->Header->MessageID;
		$pump_no = $data->Payload->DeviceID;
		$json_response = ' {
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCGetFuelModeResponse",
				"ResponseID": '.$msgid.',
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
				"MessageID": '.InvFCC::get_mcnt() .',
				"TXTimeStamp":"'.Carbon::now().'"
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"FuelMode": {
					"FuelModeID":1,
					"Name":"Credit"
				},
				"Result": {
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK",
					"ErrorText":""
				}
			}
		}';

		return $json_response;
	}


	public function get_fuelpoint_state_response($data) {
		$msgid = $data->Header->MessageID;
		$pump_no = $data->Payload->DeviceID;
		$json_response = ' {
			"Header": {
				"WorkstationID": "OPOSSUM_SVR",
				"TXTimestamp" : "'.Carbon::now().'"
				"ResponseID": '.$msgid.',
				"Api": "infx:fcc:0.9",
				"ApplicationSender": "InfxFCC",
				"MessageType": "FCCGetFuelPointStateResponse",
				"MessageID":'.InvFCC::get_mcnt() .'
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"Result": {
					"ErrorText": "",
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK"
				}
			}
		}';

		return $json_response;
	}


	public function reserve_fuelpoint_response($data, $reservation_id) {
		$pump_no = $data->Payload->DeviceID;

		$dat = date('Y-m-d H:i:s');

		$pump_reserved = DB::table('opt_mtermsync')->
			where('pump_no', $pump_no)->get();

		if(sizeof($pump_reserved) > 0){
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "MessageType" : "FCCReserveFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "Result" : {
					"OverallResult" : "ERROR",
					"ErrorCode" : "1",
					"ErrorText" : "PUMP_ALREADY_RESERVED"
				  },
				  "DeviceID" :'.$pump_no.',
				  "ReservationID" :'.$reservation_id.' 
				}
			  }';

			  return $response;
		}

		Log::info("reserve_fuelpoint: pump no=" . $pump_no);
		// Here we reserve $pump_no by putting on reserve overlay
		$pump_status = app('App\Http\Controllers\OposPetrolStationPumpController')->pumpGetStatus($pump_no, null);
		Log::info(json_encode($pump_status));
		$pump_status = json_decode($pump_status->getContent(), true);

		if($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpFillingStatus'){
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "MessageType" : "FCCReserveFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "Result" : {
					"OverallResult" : "ERROR",
					"ErrorCode" : "1",
					"ErrorText" : "PUMP_DELIVERING"
				  },
				  "DeviceID" :'.$pump_no.',
				  "ReservationID" :'.$reservation_id.' 
				}
			}';

			return $response;
		}

		if($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpOfflineStatus'){
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "MessageType" : "FCCReserveFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "Result" : {
					"OverallResult" : "ERROR",
					"ErrorCode" : "1",
					"ErrorText" : "PUMP_OFFLINE"
				  },
				  "DeviceID" :'.$pump_no.',
				  "ReservationID" :'.$reservation_id.' 
				}
			  }';
			  
			  return $response;

		}else if($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpIdleStatus'){
			DB::table('opt_mtermsync')->insert([
				'pump_no' => $pump_no,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			]);

			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "MessageType" : "FCCReserveFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "Result" : {
					"OverallResult" : "Success",
					"ErrorCode" : "1",
					"ErrorText" : "OK"
				  },
				  "DeviceID" :'.$pump_no.',
				  "ReservationID" :'.$reservation_id.' 
				}
			  }';
			  Log::info($response);

			  return $response;

		}else{
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "MessageType" : "FCCReserveFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "Result" : {
					"OverallResult" : "ERROR",
					"ErrorCode" : "1",
					"ErrorText" : "PUMP_STATUS_UNKNOWN"
				  },
				  "DeviceID" :'.$pump_no.',
				  "ReservationID" : 1
				}
			  }';

			  return $response;
		}
	}


	public function free_fuelpoint_response($data, $pump=null) {

		if (!empty($data)) {
			$pump_no = $data->Payload->DeviceID;
		} else {
			$pump_no = $pump;
		}

		// Here we release $pump_no by removing the reserve overlay
		$restext = "Success";
		$errcode = 1;
		$errtext = "OK";

		$dat = date('Y-m-d H:i:s');
		$pump_reserved = DB::table('opt_mtermsync')->
			where('pump_no', $pump_no)->get();

		if(sizeof($pump_reserved) == 0){
			$restext = "Pump not reserved.";
			$errcode = 1;
			$errtext = "Pump not reserved.";
			$msgid = (!empty($data->Header->MessageID)) ?
				$data->Header->MessageID : '';
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "WorkstationID" : "OPOSSUM_SVR",
				  "ApplicationSender" : "InfxFCC",
				  "MessageType" : "FCCFreeFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "DeviceID" :'.$pump_no.',
				  "Result" : {
					"OverallResult" : "'.$restext.'",
					"ErrorCode" : "'.$errcode.'",
					"ErrorText" : "'.$errtext.'"
				  }
				}
			  }';
			  //Log::info($response);
			  //$response = json_decode($response);
			  //Log::info(json_encode($response));
			  //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			  /*
			  $http_code = 200;
			  $ret['response'] = $response;
			  $ret['http_code'] = $http_code;
			  Log::info(json_encode($ret));
			  */
			  return $response;	
		}

		Log::info("FREE FUEL POINT: pump no: " . $pump_no);
		// Here we reserve $pump_no by putting on reserve overlay
		$pump_status = app('App\Http\Controllers\OposPetrolStationPumpController')->pumpGetStatus($pump_no, null);
		Log::info(json_encode($pump_status));
		$pump_status = json_decode($pump_status->getContent(), true);
		//return $pump_status;
		if($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpFillingStatus'){
			$restext = "Pump Delivering.";
			$errcode = 1;
			$errtext = "Pump Delivering.";
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "WorkstationID" : "OPOSSUM_SVR",
				  "ApplicationSender" : "InfxFCC",
				  "MessageType" : "FCCFreeFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "DeviceID" :'.$pump_no.',
				  "Result" : {
					"OverallResult" : "'.$restext.'",
					"ErrorCode" : "'.$errcode.'",
					"ErrorText" : "'.$errtext.'"
				  }
				}
			  }';
			  //Log::info($response);
			  //$response = json_decode($response);
			  //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			  /*
			  $http_code = 200;
			  $ret['response'] = $response;
			  $ret['http_code'] = $http_code;
			  Log::info(json_encode($ret));
			  */
			  return $response;	  

		} elseif($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpOfflineStatus' || 
		$pump_status['data']['response']['Packets'][0]['Type'] == 'PumpIdleStatus'){
			try {
				Log::debug('delete OPT Data: pump_no='.$pump_no);
				DB::table('opt_mtermsync')->
					where([
						'pump_no' => $pump_no
					])->delete();
	
			} catch (Exception $e) {
				Log::error([
					"msg"	=> $e->getMessage(),
					"file"	=> $e->getFile(),
					"line"	=> $e->getLine()	
				]);
			}
			$restext = "Pump_is_freed_successfully.";
			$errcode = 0;
			$errtext = "Pump_is_freed_successfully.";
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "WorkstationID" : "OPOSSUM_SVR",
				  "ApplicationSender" : "InfxFCC",
				  "MessageType" : "FCCFreeFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "DeviceID" :'.$pump_no.',
				  "Result" : {
					"OverallResult" : "'.$restext.'",
					"ErrorCode" : "'.$errcode.'",
					"ErrorText" : "'.$errtext.'"
				  }
				}
			  }';
			  
			  //$response = json_decode($response);
			  //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			  /*
			  $http_code = 200;
			  $ret['response'] = $response;
			  $ret['http_code'] = $http_code;
			  Log::info(json_encode($ret));
			  */
			  return $response;	  

		} else{
			$restext = "Pump status unknown.";
			$errcode = 1;
			$errtext = "Pump status unknown.";
			$msgid = $data->Header->MessageID;
			$response = '{
				"Header" : {
				  "Api" : "infx:FCC:0.9",
				  "WorkstationID" : "OPOSSUM_SVR",
				  "ApplicationSender" : "InfxFCC",
				  "MessageType" : "FCCFreeFuelPointResponse",
				  "MessageID":'.InvFCC::get_mcnt() .',
				  "ResponseID" : '.$msgid.',
				  "TXTimestamp" : "'.Carbon::now().'"
				},
				"Payload" : {
				  "DeviceID" :'.$pump_no.',
				  "Result" : {
					"OverallResult" : '.$restext.',
					"ErrorCode" : '.$errcode.'
					"ErrorText" : '.$errtext.'
				  }
				}
			  }';
			  //Log::info($response);
			  //$response = json_decode($response);
			  //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			  /*
			  $http_code = 200;
			  $ret['response'] = $response;
			  $ret['http_code'] = $http_code;
			  Log::info(json_encode($ret));
			  */
			  return $response;
		}
		// After that, we respond back success/fail to EPS
	}


	public function authorize_fuelpoint_raw($data, $restext, $errcode,
		$errtext, $trans_no) {

		dump('***** authorize_fuelpoint_raw() *****');
		dump($data); 

		$pump_no = $data->Payload->DeviceID;
		$msgid = $data->Header->MessageID;
		$json = '
		{
			"Header": {
				"WorkstationID": "OPOSSUM_SVR",
				"TXTimestamp" : "'.Carbon::now().'",
				"ResponseID": '.$msgid.',
				"Api": "infx:fcc:0.9",
				"ApplicationSender": "InfxFCC",
				"MessageType": "FCCAuthorizeFuelPointResponse",
			    "MessageID":'.InvFCC::get_mcnt() .'
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"TransactionSeqNo": '.$trans_no.',
				"Result": {
					"ErrorText": "'.$errtext.'",
					"OverallResult":"'.$restext.'",

					"ErrorCode": "'.$errcode.'"
				}
			}
		}';

		$output = preg_replace('/[^A-Za-z0-9\{\}\-\"\[\]:,._]/', '',
			trim($json));

		var_dump($output);

		return $output;
	}


	public function authorize_fuelpoint_response($data, $client, $socket,
		$prod, $reservation_id) {

		dump('**** CLASS: authorize_fuelpoint_response() *****;');

		$pump_no = $data->Payload->DeviceID;
		$amount = $data->Payload->MaxTrxAmount;
		$restext = "Success";
		$errcode = 1;
		$errtext = "OK";
		$pts_response = null;

		dump('pump_no='.$pump_no);
		dump('amount='.$amount);

		$dat = date('Y-m-d H:i:s');

		$pump_reserved = DB::table('opt_mtermsync')->
			where('pump_no', $pump_no)->get();

		dump('pump_reserved='.json_encode($pump_reserved));

		// Pump must be reserved for us!!!
		if(sizeof($pump_reserved) == 0){
			// Pump has been not been reserved
			$errcode = 1;
			$restext = "Pump HAS NOT been reserved!";
			$errtext = "Pump HAS NOT been reserved.";

			$msgid = $data->Header->MessageID;

			$response = $this->authorize_fuelpoint_raw(
				$data, $restext, $errcode, $errtext, 0);

			//$response = json_decode($response);
			return $response;
		}

		$pump_status = app('App\Http\Controllers\OposPetrolStationPumpController')->pumpGetStatus($pump_no, null);

		//dump('BEFORE pump_status='.json_encode($pump_status));

		$pump_status = json_decode($pump_status->getContent(), true);

		//dump('AFTER pump_status='.json_encode($pump_status));

		//return $pump_status;
		if($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpFillingStatus'){
			$restext = "Pump Delivering.";
			$errcode = 1;
			$errtext = "Pump Delivering.";
			$msgid = $data->Header->MessageID;

			// Generate EPS message
			$response = $this->authorize_fuelpoint_raw(
				$data, $restext, $errcode, $errtext, 0);

		} elseif($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpOfflineStatus'){
			$restext = "Pump is OFFLINE.";
			$errcode = 0;
			$errtext = "Pump is OFFLINE.";
		    
			// Generate EPS message
			$response = $this->authorize_fuelpoint_raw(
				$data, $restext, $errcode, $errtext, 0);

		}elseif($pump_status['data']['response']['Packets'][0]['Type'] == 'PumpIdleStatus'){

			$dataDetails = [
				'Pump' => $pump_no,
				'Type' => 'Amount',
				'Dose' => $amount,
				'AutoCloseTransaction' => true
			];
	
			if (!empty($nozzle)) {
				$dataDetails['Nozzle'] = null;
			}
	
			if (!empty($fuelgradeid)) {
				$dataDetails['FuelGradeId'] = null;
			}
	
			if (!empty($price)) {
				$dataDetails['Price'] = null;
			}
	
			$ptsdata = array(
				'Protocol' => 'jsonPTS',
				'Packets' => array(array (
					'Id' => 1,
					'Type' => 'PumpAuthorize',
					'Data' => $dataDetails
				))
			);
	
			$this->set_channel();
			//dump($this->ch);
			dump('pump_authorize='.json_encode($ptsdata));
	
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($ptsdata));
	
			$pts_response = json_decode(curl_exec($this->ch));

			dump('pts_response='.json_encode($pts_response));

			/* Save PTS transaction details */
			$xdet = $this->save_pts_transaction_details($pts_response);
			InvFCC::put_trans_no($xdet['pump_no'], $xdet['trans']);

			dump(InvFCC::get_trans_no($xdet['pump_no']));

			/* If PumpAuthorizeConfirmation from PTS, then we go into 
			   delivery loop */
			if (!empty($xdet['mtype'] &&
				$xdet['mtype'] == 'PumpAuthorizeConfirmation')) {

				// Send authorization event to EPS
				$gw = new invfcc_gatewayd;

				$restext = "Success";
				$errcode = "ERRCD_OK";
				$errtext = "";
				$transaction_no = $xdet['trans'];

				$response = $this->authorize_fuelpoint_raw(
					$data, $restext, $errcode, $errtext, $transaction_no);
				dump('authorize_fuelpoint_response: authorize_fuelpoint_raw response='. $response);

				$ret0 = $gw->sendEPS($response, $client);
				dump('authorize_fuelpoint_response: returns from sendEPS: ret0='.$ret0);

				// After Authorization:
				// DeliveryState=New, TransactionSeqNo=XXXX
				$output = $this->delivery_state_change_event(
					$data, $pump_no, "New", $transaction_no,
					0, 0, 0, 0);

				$ret1 = $gw->sendEPS($output, $client);
				dump('delivery_state_change_event: returns from sendEPS: ret1='.$ret1);


				// Send FCCFuelPointStateChangeEvent
				$out2 = $this->fuelpoint_state_change_event(
					$transaction_no, $pump_no, "Authorized", "None", 0, 0,
					"NozzleDown");

				$ret2 = $gw->sendEPS($out2, $client);
				dump('recvEPS: fuelpoint_state_change_event from sendEPS: ret2='.$ret2);


				$ret3 = $this->execute_delivery_loop(
					$pump_no, $xdet['trans'], $client, $socket);
				dump('returns from sendEPS: ret3='.$ret3);

				// Pump has nozzle down!
				$response = $ret3;
			}
		}

		return $response;
	}


	public function terminate_fuelpoint_response($data) {
		$msgid = $data->Header->MessageID;
		$pump_no = $data->Payload->DeviceID;
		$trans_no = $data->Payload->TransactionSeqNo;


		// Generate data for termination PTS PumpStop
		$ptsdata = array(
			'Protocol' => 'jsonPTS',
			'Packets' => array(array (
				'Id' => 1,
				'Type' => 'PumpStop',
				'Data' => array(
					'Pump' => $pump_no
				)
			))
		);

		$this->set_channel();
		//dump($this->ch);
		dump('pump_stop='.json_encode($ptsdata));

		// Sending PumpStop to PTS
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($ptsdata));
		$pts_response = json_decode(curl_exec($this->ch));

		dump('pts_response='.json_encode($pts_response));


		// Prepare notification for EPS
		$json_response = ' {
			"Header": {
				"WorkstationID": "OPOSSUM_SVR",
				"TXTimestamp" : "'.Carbon::now().'",
				"ResponseID": '.$msgid.',
				"Api": "infx:fcc:0.9",
				"ApplicationSender": "InfxFCC",
				"MessageType": "FCCTerminateFuelPointResponse",
			    "MessageID":'.InvFCC::get_mcnt() .'
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"Result": {
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK",
					"ErrorText": ""
				}
			}
		}';

		return $json_response;
	}


	public function execute_delivery_loop($pump_no, $trans, $client, $socket) {


		while (true) {
			// Check PTS for the status of the pump
			$pc = new OposPetrolStationPumpController;
			$ptsret = $pc->pumpGetStatus($pump_no, null);
			$pstat = json_decode($ptsret->getContent(), true);
			$packet = $pstat['data']['response']['Packets'][0];
			dump('execute_delivery_loop: packet=');
			dump($packet);

			$type = $packet['Type'];
			$data = $packet['Data'];

			// See if EPS has anything to say
			$gw = new invfcc_gatewayd;
			$epsret = $gw->recvEPS($trans, $client, $socket, $packet);
			dump('execute_delivery_loop: epsret=');
			dump($epsret);

			sleep(1);
			$gw = null; $pc = null;

			// Pump has detected NOZZLE DOWN! Break out!
			if ($epsret < 0) break;
		}

		dump('execute_delivery_loop: ret='.$epsret);
		return $epsret;
	}


	public function save_pts_transaction_details($response) {
		dump('***** save_pts_transaction_details() *****');
		$r = $response;
		$p = $r->Packets[0];
		$type = $p->Type;
		$data = $p->Data;
		$pump_no = $data->Pump;

		$ret['pump_no'] = $pump_no;
		$ret['mtype'] = $type;

		// In case of JSONPTS_ERROR_PUMP_BUSY_OTHER_USER errors
		// which there will be no Transaction field
		if (!empty($data->Transaction)) {
			$trans = $data->Transaction;
			$ret['trans'] = $trans;
		}

		return $ret;
	}


	public function lock_fuelsale_response($data) {
		$pump_no = $data->Payload->DeviceID;
		$trans   = $data->Payload->TransactionSeqNo;
		$msgid   = $data->Header->MessageID;
		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCLockFuelSaleResponse",
				"ResponseID": '.$msgid.',
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"TransactionSeqNo": '.$trans.',
				"Result": {
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK",
					"ErrorText": ""
				}
			}
		}';

		return $json_data;

	}


	public function clear_fuelsale_response($data) {
		$pump_no = $data->Payload->DeviceID;
		$trans   = $data->Payload->TransactionSeqNo;
		$msgid   = $data->Header->MessageID;
		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.5",
				"MessageType": "FCCClearFuelSaleResponse",
				"ResponseID": '.$msgid.',
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"TransactionSeqNo": '.$trans.',
				"Result": {
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK",
					"ErrorText": ""
				}
			}
		}';

		return $json_data;
	}


	public function get_deliverydetails_raw(
		$data, $dev_state, $pump_no, $trans_no,
		$ogfuel_id, $price, $volume, $amount) {

		// $data is from EPS FCCGetDeliveryDetailsRequest
		$msgid = $data->Header->MessageID;
		$pump_no = $data->Payload->DeviceID;

		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCGetDeliveryDetailsResponse",
				"ResponseID": '.$msgid.',
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"DeviceID": '.$pump_no.',
				"DeliveryStateList": [
					{
						"DeviceID": '.$pump_no.',
						"DeliveryState": "'.$dev_state.'",
						"TransactionSeqNo": '.$trans_no.',
						"ProductID": '.$ogfuel_id.',
						"AuthorizationType": "Prepay",
						"Locked": false,
						"PaymentData": "",
						"ConsentState": "NotRequired",
						"Totals": {
							"UnitPrice": "'.number_format($price,2).'",
							"Volume": "'.number_format($volume,2).'",
							"Amount": "'.number_format($amount,2).'"
						}
					}
				],
				"Result": {
					"OverallResult": "Success",
					"ErrorCode": "ERRCD_OK",
					"ErrorText": ""
				}
			}
		}';

		return $json_data;
	}


	/*
	This command returns details on transactions from PTS2 */
	public function pts2_get_transaction_details($pump_no, $transaction_no) {

		// Get from private instance variable when given is zero
		if ($transaction_no == 0) {
			dump('**** WHOA! Transaction No. is ZERO!');
			$transaction_no = InvFCC::get_trans_no($pump_no);
			dump('Getting from class variable: transaction_no = '.
				$transaction_no);
		}

		// Query PTS2 for the status of the pump and transaction_no
		$ptsdata = array(
			'Protocol' => 'jsonPTS',
			'Packets' => array(array (
				'Id' => 1,
				'Type' => 'PumpGetTransactionInformation',
				'Data' => array(
					'Pump' => $pump_no,
					'Transaction' => $transaction_no
				)
			))
		);

		$this->set_channel();
		//dump('pump_gettransaction_info='.json_encode($ptsdata));

		curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($ptsdata));

		$pts_response = json_decode(curl_exec($this->ch));

		/*
		dump('pts2_get_transaction_details: pts_response=');
		dump($pts_response);
		*/

		$ret = null;
		// Harvest the response
		if (!empty($pts_response)) {

			// Fetch transaction details
			$packet = $pts_response->Packets[0];

			// We have a response and it's not a PTS error
			if (!empty($packet) && empty($packet->Error)) {

				dump('pts2_get_transaction_details: packet=');
				dump($packet);

				$type = $packet->Type;
				$data = $packet->Data;

				/*
				dump($type);
				dump($data);
				*/

				$ret = $data;

			} else if (!empty($packet) && !empty($packet->Error)) {
				dump('pts2_get_transaction_details: ERROR packet=');
				dump($packet);

			} else {
				dump('pts2_get_transaction_details: ELSE packet=');
				dump($packet);
			}
		}

		return $ret;
	}


	/*
	This command returns details on transactions on the delivery stack.
	It also returns a list of all active transactions, or an individual
	transaction if a non-zero TransactionSeqNo (DeliveryID) is provided. */
	public function get_deliverydetails_response($data, $prod,
		$reservation_id, $client) {
		/*
		{#24360
		  +"Header": {#30736
			+"Api": "infx:fcc:0.5"
			+"MessageType": "FCCGetDeliveryDetailsRequest"
			+"MessageID": 2882
			+"ApplicationSender": "FUELPOS"
			+"WorkstationID": "1"
			+"TXTimestamp": "2022-06-02T14:01:07.040214500Z"
		  }
		  +"Payload": {#32051
			+"DeviceID": 6
			+"DeliveryID": 68
		  }
		}
		*/

		$msgid = $data->Header->MessageID;

		$pump_no = $data->Payload->DeviceID;
		$transaction_no = $data->Payload->DeliveryID;

		// Get the transaction details from PTS2 for the pump and transaction
		$ptsret = $this->pts2_get_transaction_details($pump_no, $transaction_no);
		//dump('ptsret=');
		//dump($ptsret);

		/*
		“State” – state of transaction, possible values:
		“WaitingNozzleUpForAuthorization” – pump is to be authorized on
			nozzle up, transaction has not started yet
		“Authorized” – pump is authorized, transaction has not started yet
		“Filling” – transaction is in process (filling is going)
		“Finished” – transaction is finished
		“Not found” – transaction can not be found: either the transaction
			number is wrong or transaction did not take place
			(no fuel was dispensed)
		*/

		$ret = null;
		if (!empty($ptsret)) {
			dump('ptsret is NOT empty');
			dump($ptsret);

			$state		= $ptsret->State;
			$transaction_no	= $ptsret->Transaction;
			$nozzle		= (!empty($ptsret->Nozzle)?$ptsret->Nozzle:0);
			$volume		= (!empty($ptsret->Volume)?$ptsret->Volume:0);
			$price		= (!empty($ptsret->Price)?$ptsret->Price:0);
			$amount		= (!empty($ptsret->Amount)?$ptsret->Amount:0);
			$start_time	= (!empty($ptsret->DateTimeStart)?$ptsret->DateTimeStart:'');
			$end_time	= (!empty($ptsret->DateTime)?$ptsret->DateTime:'');

			// Have to map the state between PTS and EPS
			// "ReadyToPayOff" from EPS is unmapped..
			switch($state) {
				case 'WaitingNozzleUpForAuthorization':
				case 'Authorized':
					$dev_state = "New";
					break;
				case 'Filling':
					dump('****** get_deliverydetails_response: '.
						'NOZZLE UP Detected: pump='.$pump_no.
						', nozzle='.$nozzle.', client='.$client);

					// DeviceState=Authorized, Reserved=true
					// Once Filling, nozzle HAS to be UP!
					$json = $this->fuelpoint_state_change_event(
						$transaction_no, $pump_no, 'Authorized',"Valid",
						0, $reservation_id,'NozzleUp');

					$gw = new invfcc_gatewayd;
					$ret2 = $gw->sendEPS($json, $client);
					dump('***** get_deliverydetails_response: '.
						'sending NozzleUp!!! ret2='.$ret2);

					$dev_state = "Fueling";
					break;
				case 'Finished':
					$dev_state = "PaidOff";
					break;
				case 'Not found':
				default:
					$dev_state = "Dead";
			}

			if($nozzle == 0) {
				$ogfuel_id = 0;
			} else {
				// Protect against $nozzle == 0
				$ogfuel_id = $prod[$nozzle]->ogfuel_id;
			}

			// Generate message for EPS
			if(!is_null($price) && !is_null($volume) && !is_null($amount)) {
				$ret = $this->get_deliverydetails_raw($data, $dev_state,
					$pump_no, $transaction_no, $ogfuel_id,
					$price, $volume, $amount);
			}
		}

		/*
		dump('**** get_deliverydetails_resonse: ');
		dump($ret);
		*/

		return $ret;
	}


	/***** START: Callback functions to Invenco FuelPOS *****/
	public function fuelpoint_state_change_event(
		$trans_no, $dev_id, $dev_state, $prd_selected,
		$ogfuel_id, $reservation_id, $nozzle) {

		$reserved = 'false';

		// If we have a valid reservation_id, obviously reserved is true
		if (!empty($reservation_id) && ($reservation_id > 0)) {
			$reserved = 'true';
		} 

		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCFuelPointStateChangeEvent",
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"FuelPointState": {
					"DeviceID": '.$dev_id.',
					"FuelModeID": 1,
					"TradingState": "Open",
					"DeviceState": "'.$dev_state.'",
					"ProductSelected": "'.$prd_selected.'",
					"NozzleState": "'.$nozzle.'",
					"SuspendedState": "None",
					"Reserved": '.$reserved.',
					"OperatorConsentState": "NotRequired",
					"TransactionSeqNo": '.$trans_no.',
					"ProductID": '.$ogfuel_id.',
					"NozzlesUpList": [],
					"ReservationID":'.$reservation_id.' 
				}
			}
		}';

		return $json_data;
	}


	public function fuelprice_change_event() {
	}


	public function delivery_started_event(
		$dev_id, $trans_no, $prod_id, $price) {
		$json_data = '{
			"Header":{
				"Api":" nfx:FCC:0.9",
				"MessageType":"FCCDeliveryStartedEvent",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"ResponseID":0,
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload":{
				"DeliveryState":{
					"DeviceID":'.$dev_id.',
					"DeliveryState":"Fueling",
					"TransactionSeqNo":'.$trans_no.',
					"ProductID":'.$prod_id.',
					"Totals":{
						"UnitPrice":"'.number_format($price,2).'",
						"Volume":"0",
						"Amount":"0"
					},
					"AuthorizationType":"Prepay"
				}
			}
		}';
		return $json_data;
	}


	public function delivery_progress_event(
		$dev_id, $trans_no, $price, $volume, $amount) {
		$json_data = '{
			"Header":{
				"Api":"infx:FCC:0.9",
				"MessageType":"FCCDeliveryProgressEvent",
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"ResponseID":0,
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload":{
				"DeviceID":'.$dev_id.',
				"TransactionSeqNo":'.$trans_no.',
				"DeliveryState":{
					"DeviceID":'.$dev_id.',
					"TransactionSeqNo":'.$trans_no.',
					"Totals":{
						"UnitPrice":"'.number_format($price,2).'",
						"Volume":"'.$volume.'",
						"Amount":"'.$amount.'"
					}
				}
			}
		}';
		return $json_data;
	}


	public function delivery_complete_event(
		$dev_id, $nozzle, $prod, $trans_no,
		$price, $volume, $amount) {

		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCDeliveryCompleteEvent",
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
			    "MessageID":'.InvFCC::get_mcnt() .',
				"ResponseID":0,
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"FuelSale": {
					"DeviceID": '.$dev_id.',
					"DeliveryState": "PaidOff",
					"TransactionSeqNo": '.$trans_no.',
					"ProductID":'.$prod[$nozzle]->ogfuel_id.',
					"NozzleID": '.$nozzle.',
					"ProductDescription":"'.$prod[$nozzle]->name.'",
					"PaymentData": "",
					"DeliveryTotals": {
						"UnitPrice": "'.number_format($price,2).'",
						"Volume": "'.$volume.'",
						"Amount": "'.$amount.'"
					}
				}
			}
		}';

		return $json_data;
	}


	public function delivery_state_change_event($data, $dev_id, $dev_state,
		$trans_no, $prod_id, $price, $volume, $amount) {

		// Only deliveries that are "locked" are able to pay it off 
		$msgid = $data->Header->MessageID;
		$json_data = '{
			"Header": {
				"Api": "infx:fcc:0.9",
				"MessageType": "FCCDeliveryStateChangeEvent",
				"ApplicationSender": "InfxFCC",
				"WorkstationID": "OPOSSUM_SVR",
				"ResponseID":'.$msgid.',
			    "MessageID":'.InvFCC::get_mcnt() .',
				"TXTimestamp" : "'.Carbon::now().'"
			},
			"Payload": {
				"DeliveryState": {
					"DeviceID": '.$dev_id.',
					"DeliveryState": "'.$dev_state.'",
					"TransactionSeqNo": '.$trans_no.',
					"ProductID": '.$prod_id.',
					"AuthorizationType": "Prepay",
					"Locked": false,
					"PaymentData": "",
					"ConsentState": "NotRequired",
					"Totals": {
						"UnitPrice": "'.number_format($price,2).'",
						"Volume": "'.number_format($volume,2).'",
						"Amount": "'.number_format($amount,2).'"
					}
				}
			}
		}';

		return $json_data;
	}
	/***** END: Callback functions to Invenco FuelPOS *****/


	public function close_channel() {
		if (!empty($this->ch)) {
			curl_close($this->ch);
		}
	}
}
?> 
