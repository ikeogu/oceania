<?php

namespace App\Http\Controllers;

use \App\Classes\SPay;
use \App\Classes\E1_100;
use \App\Classes\InvFCC;
use Illuminate\Http\Request;
use \App\Http\Controllers\SPayController;
use \App\Http\Controllers\InvFccController;

class TestController extends Controller
{

public function spay_create_order() {
	$req = new Request();
	$req->setMethod('POST');
	$req->request->add(['merchantId'	=> 'M100004540']);
	$req->request->add(['qrCode'		=> '966751617999111176']);
	$req->request->add(['curType'		=> 'RM']);
	$req->request->add(['notifyURL'		=> 'https://google.com']);
	$req->request->add(['merOrderNo'	=> strval(time()) ]);
	$req->request->add(['goodsName'		=> 'Twisties Special Pack']);
	$req->request->add(['detailURL'		=> 'https://youtube.com']);
	$req->request->add(['orderAmt'		=> '13.43']);
	$req->request->add(['remark'		=> 'Special Promotional Pack']);
	$req->request->add(['transactionType' => '1']);

	$spc = new SPayController;
	$raw = $spc->SPayCreateOrder($req);		

	$response = $raw->original['data']['response'];
	dump(json_decode($response));
}


public function invfcc_reserve_fuelpoint($pump_no) {
	$data = array(
		'Header' => array(
			"Api" => "infx:fcc:0.5",
			"MessageType" => "FCCReserveFuelPointRequest",
			"MessageID" => 8,
			"ApplicationSender" => "FUELPOS",
			"WorkstationID" => "1",
			"TXTimestamp" => "2021-01-25T21:59:47.583Z"
		),
		'Payload' => array(
			'DeviceID' => $pump_no
		)
	);

	$req = new Request();
	$req->setMethod('POST');
	$req->request->add($data);

	$invfcc = new InvFccController;
	$raw = $invfcc->reserve_fuelpoint($req);

	dump($raw);
}



public function invfcc_authorize_fuelpoint($pump_no, $amount) {
	/*
	{
		"Header": {
			"Api": "infx:fcc:0.9",
			"MessageType": "FCCAuthorizeFuelPointRequest",
			"MessageID": 33,
			"ApplicationSender": "PointOfSale",
			"WorkstationID": "WS1",
			"TXTimestamp": "2021-09-26T22:00:40+1300"
		},
		"Payload": {
			"DeviceID": 1,
			"AuthorizationType": "Prepay",
			"ReservationID": 94,
			"ReleasedProducts": [],
			"MaxTrxAmount": "10.00",
			"AutoLock": false,
			"ConsentNeeded": false
		}
	}
	*/

	$data = array(
		'Header' => array(
			"Api" => "infx:fcc:0.5",
			"MessageType" => "FCCAuthorizeFuelPointRequest",
			"MessageID" => 8,
			"ApplicationSender" => "FUELPOS",
			"WorkstationID" => "1",
			"TXTimestamp" => "2021-01-25T21:59:47.583Z"
		),
		'Payload' => array(
			'DeviceID' => $pump_no,
			'AuthorizationType'=> 'Prepay',
			'ReservationID'=> 94,
			'ReleasedProducts'=> [],
			'MaxTrxAmount'=> $amount,
			'AutoLock'=> false,
			'ConsentNeeded'=> false
		)
	);

	$req = new Request();
	$req->setMethod('POST');
	$req->request->add($data);

	$invfcc = new InvFccController;
	$raw = $invfcc->authorize_fuelpoint($req);

	dump($raw);

}

public function invfcc_free_fuelpoint($pump_no){
	$data = array(
		'Header' => array(
			"Api" => "infx:fcc:0.5",
			"MessageType" => "FCCReserveFuelPointRequest",
			"MessageID" => 8,
			"ApplicationSender" => "FUELPOS",
			"WorkstationID" => "1",
			"TXTimestamp" => "2021-01-25T21:59:47.583Z"
		),
		'Payload' => array(
			'DeviceID' => $pump_no
		)
	);

	$req = new Request();
	$req->setMethod('POST');
	$req->request->add($data);

	$invfcc = new InvFccController;
	$raw = $invfcc->free_fuelpoint($req);

	dump($raw);
}

/*
public function get_all_terminal_status() {
	dump('TestController@get_all_terminal_status()');

	$e1 = new E1_100('127.0.0.1');
	$res = $e1->get_all_terminal_status();

	dump($res);

	$e1->close_channel();
}


public function preauth_req() {
	dump('TestController@preauth_req()');

	$vtid = 2;
	$items = array(
		array (
			'amount' => 1.95,
			"productId" => "1050000000432",
			"quantity" => 3,
			"unitPrice" => 3.43
		),
		array (
			'amount' => 43.39,
			"productId" => "1050000009293",
			"quantity" => 32,
				"unitPrice" => 9.14
			)
		);

		dump($vtid);
		dump($items);
		dump($res);

		$e1 = new E1_100('127.0.0.1');
		$res = $e1->preauth_req($vtid, $items);


		$e1->close_channel();
	}
	*/
}
