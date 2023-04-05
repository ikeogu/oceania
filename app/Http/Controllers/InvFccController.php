<?php

namespace App\Http\Controllers;

use Log;
use \App\Classes\InvFCC;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class InvFccController extends Controller
{
	/* Get Forecourt Configuration */
    public function get_config(Request $req) {
		dump('***** /api/invfcc/get_config *****');
		dump($req->all());

		/*
		{"Header":{"Api":"infx:fcc:0.5","MessageType":"bar"}}
		*/

		/* Request data
		array:2 [
		  "Header" => array:6 [
			"Api" => "infx:fcc:0.5"
			"MessageType" => "FCCGetConfigRequest"
			"MessageID" => 5
			"ApplicationSender" => "FUELPOS"
			"WorkstationID" => "1"
			"TXTimestamp" => "2021-01-25T21:54:51.867Z"                        
		  ]
		  "Payload" => []
		]
		*/

		$invfcc = new InvFCC();
		$response = $invfcc->get_config_response($data);

		dump($response);

		return $response;
	}


    public function get_fuelpoint_state(Request $req) {
		dump('***** /api/invfcc/get_fuelpoint_state *****');
		dump($req->all());

		$invfcc = new InvFCC();
		$response = $invfcc->get_fuelpoint_state_response($data);

		dump($response);

		return $response;

	}


    public function reserve_fuelpoint(Request $req) {
		dump('***** /api/invfcc/reserve_fuelpoint *****');
		dump($req->all());

		$payload = $req->input('Payload');
		$pump_no = $payload['DeviceID'];

		//dump($payload['DeviceID']);

		$invfcc = new InvFCC();
		$response = $invfcc->reserve_fuelpoint_response($pump_no);

		dump($response);

		return $response;
	}


	public function free_fuelpoint(Request $req) {
		dump('***** /api/invfcc/free_fuelpoint *****');
		dump($req->all());

		$payload = $req->input('Payload');
		$pump_no = $payload['DeviceID'];

		//dump($payload['DeviceID']);

		$invfcc = new InvFCC();
		$response = $invfcc->free_fuelpoint_response($pump_no);

		dump($response);

		return $response;
	}


    public function authorize_fuelpoint(Request $req) {
		dump('***** /api/invfcc/authorize_fuelpoint *****');
		dump($req->all());

		/* Request data
		array:2 [
		  "Header" => array:6 [
			"Api" => "infx:fcc:0.5"
			"MessageType" => "FCCAuthorizeFuelPointRequest"
			"MessageID" => 10
			"ApplicationSender" => "FUELPOS"
			"WorkstationID" => "1"
			"TXTimestamp" => "2021-01-25T21:59:47.607Z"
		  ]
		  "Payload" => array:7 [
			"DeviceID" => 1
			"AuthorizationType" => "Prepay"
			"ReleasedProducts" => array:3 [
			  0 => 100
			  1 => 200
			  2 => 300
			]
			"MaxTrxAmount" => "150.00"
			"AutoLock" => false
			"ReservationID" => 1
			"PaymentData" => null
		  ]
		]
		*/
		$payload = $req->input('Payload');
		$pump_no = $payload['DeviceID'];
		$amount = $payload['MaxTrxAmount'];
		$invfcc = new InvFCC();
		$response = $invfcc->authorize_fuelpoint_response($pump_no, $amount);

		dump($response);
		return $response;
	}


    public function terminate_fuelpoint(Request $req) {
	}


    public function lock_fuelsale(Request $req) {
		dump('***** /api/invfcc/lock_fuelsale *****');
		dump($req->all());

		/* Request data
		array:2 [
		  "Header" => array:6 [
			"Api" => "infx:fcc:0.5"
			"MessageType" => "FCCLockFuelSaleRequest"
			"MessageID" => 17
			"ApplicationSender" => "FUELPOS"
			"WorkstationID" => "1"
			"TXTimestamp" => "2021-01-25T22:00:37.625Z"
		  ]
		  "Payload" => array:2 [
			"DeviceID" => 1
			"TransactionSeqNo" => 2
		  ]
		]
		*/

		$json_response = '{
		  "Header" : {
			"Api" : "infx:FCC:0.5",
			"MessageType" : "FCCLockFuelSaleResponse",
			"MessageID" : 17,
			"ResponseID" : 1,
			"TXTimestamp" : "2010-0-0T0:00:00"
		  },
		  "Payload" : {
			"Result" : {
			  "OverallResult" : "Success",
			  "ErrorCode" : "1",
			  "ErrorText" : "OK"
			},
			"DeviceID" : 1,
			"TransactionSeqNo" : 2
		  }
		}';

		dump(json_decode($json_response));

		return $json_response;
	}


    public function clear_fuelsale(Request $req) {
		dump('***** /api/invfcc/clear_fuelsale *****');
		dump($req->all());

		/* Request data
		{
			"Header": {
				"Api": "infx:fcc:0.5",
				"MessageType": "FCCClearFuelSaleRequest",
				"MessageID": 20,
				"ApplicationSender": "FUELPOS",
				"WorkstationID": "1",
				"TXTimestamp": "2021-01-25T22:00:43.638Z"
			},
			"Payload": {
				"DeviceID": 1,
				"TransactionSeqNo": 2
			}
		}
		*/

		$invfcc = new InvFCC();
		$json_response = $invfcc->clear_fuelsale_response($pump_no);

		dump(json_decode($json_response));

		return $json_response;
	}


    public function get_delivery_details(Request $req) {
		dump('***** /api/invfcc/get_delivery_details *****');
		dump($req->all());

		/* Request data
		array:2 [
		  "Header" => array:6 [
			"Api" => "infx:fcc:0.5"
			"MessageType" => "FCCGetDeliveryDetailsRequest"
			"MessageID" => 9
			"ApplicationSender" => "FUELPOS"
			"WorkstationID" => "1"
			"TXTimestamp" => "2021-01-25T21:59:47.597Z"
		  ]
		  "Payload" => array:2 [
			"DeviceID" => 1
			"DeliveryID" => 0
		  ]
		]
		*/

		$invfcc = new InvFCC();
		$json_response = $invfcc->get_delivery_details_response($pump_no);

		dump(json_decode($json_response));

		return $json_response;
	}


    public function fuelpoint_state_change_event($dev_id, $dev_state, $ipaddr) {
		/* Event data
		array:2 [
		  "Header" => array:6 [
			"Api" => "infx:fcc:0.9"
			"MessageType" => "FCCFuelPointStateChangeEvent"
			"ApplicationSender" => "InfxFCC"
			"WorkstationID" => "FCCServer1"
			"MessageID" => 12
			"TXTimestamp" => "2021-01-25T21:54:51.935Z"
		  ]
		  "Payload" => array:1 [
			"FuelPointState" => array:12 [
			  "DeviceID" => 6
			  "FuelModeID" => 1
			  "DeviceState" => "Idle"
			  "ProductSelected" => "None"
			  "NozzleState" => "NozzleDown"
			  "SuspendedState" => "None"
			  "Reserved" => false
			  "OperatorConsentState" => "NotRequired"
			  "TransactionSeqNo" => 0
			  "ProductID" => 0
			  "NozzlesUpList" => []
			  "ReservationID" => 0
			]
		  ]
		]
		*/

        if (empty($ipaddr)) $ipaddr = env('EPS_IPADDR');
		
		$inv = new InvFCC($ipaddr);

		$ret = $inv->fuelpoint_state_change_event($event_data);

		return $ret;
	}


    public function fuelprice_change_event(Request $req) {
	}


    public function delivery_state_change_event(Request $req) {
	}


    public function delivery_started_event(Request $req) {
		dump('***** /api/invfcc/delivery_started_event *****');
		dump($req->all());

		/* Event data
		array:2 [
		  "Header" => array:5 [
			"Api" => "nfx:FCC:0.5"
			"MessageType" => "FCCDeliveryStartedEvent"
			"MessageID" => 2
			"ResponseID" => 0
			"TXTimestamp" => "2010-0-0T0:00:00"
		  ]
		  "Payload" => array:1 [
			"DeliveryState" => array:6 [
			  "DeviceID" => 1
			  "DeliveryState" => "Fueling"
			  "TransactionSeqNo" => 2
			  "ProductID" => 101
			  "Totals" => array:3 [
				"UnitPrice" => "1.239"
				"Volume" => "0"
				"Amount" => "0"
			  ]
			  "AuthorizationType" => "Preauth"
			]
		  ]
		]
		*/
	}


    public function delivery_progress_event(Request $req) {
		dump('***** /api/invfcc/delivery_progress_event *****');
		dump($req->all());

		/* Event data
		array:2 [
		  "Header" => array:5 [
			"Api" => "infx:FCC:0.5"
			"MessageType" => "FCCDeliveryProgressEvent"
			"MessageID" => 3
			"ResponseID" => 0
			"TXTimestamp" => "2010-0-0T0:00:00"
		  ]
		  "Payload" => array:3 [
			"DeviceID" => 1
			"TransactionSeqNo" => 2
			"DeliveryState" => array:3 [
			  "DeviceID" => 1
			  "TransactionSeqNo" => 2
			  "Totals" => array:3 [
				"UnitPrice" => "1.239"
				"Volume" => "0.000"
				"Amount" => "0.00"
			  ]
			]
		  ]
		]
		*/
	}


    public function delivery_complete_event(Request $req) {
		dump('***** /api/invfcc/delivery_complete_event *****');
		dump($req->all());

		/* Event data
		array:2 [
		  "Header" => array:5 [
			"Api" => "infx:FCC:0.5"
			"MessageType" => "FCCDeliveryCompleteEvent"
			"MessageID" => 32
			"ResponseID" => 1
			"TXTimestamp" => "2010-0-0T0:00:00"
		  ]
		  "Payload" => array:2 [
			"FuelSale" => array:5 [
			  "DeviceID" => 1
			  "TransactionSeqNo" => 2
			  "ProductID" => 101
			  "NozzleID" => 1
			  "DeliveryTotals" => array:3 [
				"UnitPrice" => "1.239"
				"Volume" => "18.984"
				"Amount" => "23.52"
			  ]
			]
			"MeterList" => []
		  ]
		]
		*/
	}

}
