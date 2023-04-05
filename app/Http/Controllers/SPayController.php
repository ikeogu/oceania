<?php

namespace App\Http\Controllers;

use DB;
use Log;
use \App\Classes\SPay;
use \App\Models\SpayCreateorderResponse;
use \App\Models\SpayCreateorderRequest;
use \App\Models\SpayStoreinquiryRequest;
use \App\Models\SpayStoreinquiryResponse;
use Illuminate\Http\Request;


class SPayController extends Controller
{
    public function SPayCreateOrder(Request $req) {

		$merchantId			= empty($req->input('merchantId')) ?
								null : $req->input('merchantId');
		$qrCode				= empty($req->input('qrCode')) ?
								null : $req->input('qrCode');
		$curType			= empty($req->input('curType')) ?
								null : $req->input('curType');
		$notifyURL			= empty($req->input('notifyURL')) ?
								null : $req->input('notifyURL');
		$merOrderNo			= empty($req->input('merOrderNo')) ?
								null : $req->input('merOrderNo');
		$goodsName			= empty($req->input('goodsName')) ?
								null : $req->input('goodsName');
		$detailURL			= empty($req->input('detailURL')) ?
								null : $req->input('detailURL');
		$orderAmt			= empty($req->input('orderAmt')) ?
								null : $req->input('orderAmt');
		$remark				= empty($req->input('remark')) ?
								null : $req->input('remark');
		$transactionType	= empty($req->input('transactionType')) ?
								null : $req->input('transactionType');
		
		$sp = new SPay(null);
		$res = $sp->create_order(
			$merchantId, $qrCode, $curType, $notifyURL,
			$merOrderNo, $goodsName, $detailURL, $orderAmt,
			$remark, $transactionType);

		$req->request->add(['sign' => $sp->spay_sign]);
		$sign = $sp->spay_sign;
		
		//dump('**** SPayController@SPayCreateOrder ****');
		//dump($req->all());

		// Store Request data to database
    	$this->SPayStoreOrderRequest(
			empty($merchantId) ? $sp->spay_merchant_id : $merchantId,
			$qrCode,
			empty($curType) ? $sp->spay_curtype : $curType,
			empty($notifyURL) ? $sp->spay_notify_url : $notifyURL,
			$merOrderNo,
			$goodsName,
			empty($detailURL) ? $sp->spay_detail_url : $detailURL,
			$orderAmt,
			empty($remark) ? $sp->spay_remark : $remark,
			empty($transactionType) ? $sp->spay_transaction_type : $transactionType,
			$sign);

		$response = json_decode($res['response']);
		if ($res['http_code'] == 200 && $response->ResStatus == "0") {

		 	// Store Response data to Database if comms is successful
		 	$this->SPayStoreOrderResponse($res['response']);
		}

		$sp->close_channel();
			
		return response()->json(['data' => $res]);
	}


	/* This is to store Create Order Response parameters in DB */
	public function SPayStoreOrderResponse($rawresp) {
		//dump('**** SPayStoreOrderResponse ****');
		if (!empty($rawresp)) {
			$resp = json_decode($rawresp);
		}

		$storeResponse = new SpayCreateorderResponse();

		$storeResponse->ResStatus 		= $resp->ResStatus;
		$storeResponse->ResMsg 			= $resp->ResMsg;
		$storeResponse->ResCode 		= (!empty($resp->ResCode)) ?
											$resp->ResCode : '';
		$storeResponse->merchantId 		= $resp->merchantId;
		$storeResponse->merOrderNo 		= $resp->merOrderNo;
		$storeResponse->customerId 		= $resp->customerId;

		//dump('customerId='.$resp->customerId);

		$storeResponse->orderDate 		= $resp->orderDate;
		$storeResponse->orderNo 		= $resp->orderNo;
		$storeResponse->orderAmt 		= $resp->orderAmt;
		$storeResponse->sign 			= $resp->sign;

		$old_transaction = SpayCreateorderResponse::where([
			'merOrderNo'	=> $resp->merOrderNo, 
			'customerId'	=> $resp->customerId, 
			'orderNo'		=> $resp->orderNo,
			'orderAmt'		=> $resp->orderAmt,
			'orderDate'		=> $resp->orderDate])->
			count();

		if ($old_transaction == 0 ){
			$storeResponse->save();
		}
	}


	/* This is to store Create Order Request parameters in DB */
	public function SPayStoreOrderRequest(
			$merchantId, $qrCode, $curType, $notifyURL,
			$merOrderNo, $goodsName, $detailURL, $orderAmt,
			$remark, $transactionType, $sign) {

		$storeRequest = new SpayCreateorderRequest();

		$storeRequest->merchantId			= $merchantId;
		$storeRequest->qrCode				= $qrCode;
		$storeRequest->curType				= $curType;
		$storeRequest->notifyURL			= $notifyURL;
		$storeRequest->merOrderNo			= $merOrderNo;
		$storeRequest->orderAmt				= $orderAmt;
		$storeRequest->goodsName			= $goodsName;
		$storeRequest->transactionType 		= $transactionType;
		$storeRequest->detailURL			= $detailURL;
		$storeRequest->remark				= $remark;
		$storeRequest->sign					= $sign;

		$old_transaction = SpayCreateorderRequest::where([
			'goodsName'		=> $goodsName, 
			'qrCode'		=> $qrCode,
			'orderAmt'		=> $orderAmt,
			'merOrderNo'	=> $merOrderNo])->
			count();

		if ($old_transaction == 0 ){
			$storeRequest->save();
		}
	}

	public function query_order(Request $req){	
		
		$sp = new SPay(null);

		$merOrderNo			= empty($req->input('merOrderNo')) ?
								null : $req->input('merOrderNo');
		$merchantId			= empty($merchantId) ? $sp->spay_merchant_id : $merchantId;
		
		$res = $sp->query_order($merchantId, $merOrderNo ,$orderNo = null); 


		Log::info('query_order: res='.json_encode($res));

		$response = json_decode($res['response']);

		if ($res['http_code'] == 200 && $response->orderStatus == "1") {
		 	// Store Response data to Database if comms is successful
		 	$this->StoreInquiryResponse($res['response']);
		}

		$this->StoreInquiryRequest($merchantId , $merOrderNo , $orderNo); 
		return response()->json(['data' => $res]);
	}



	public function StoreInquiryRequest($merchantId , $merOrderNo , $orderNo){
		$storeRequest = new SpayStoreinquiryRequest();

		$storeRequest->merchantId			= $merchantId;
		$storeRequest->merOrderNo			= $merOrderNo;
		$storeRequest->orderNo				= $orderNo;
		
		$storeRequest->save();
	}


	public function StoreInquiryResponse($rawresp){

		Log::debug('StoreInquiryResponse: rawresp='. $rawresp);

		if (!empty($rawresp)) {
			$resp = json_decode($rawresp);
		} else {
			return; 
		}

		Log::debug('StoreInquiryResponse: resp->ResStatus='. $resp->ResStatus);

		$storeResponse = new SpayStoreinquiryResponse();

		$storeResponse->ResStatus 		= $resp->ResStatus;
		$storeResponse->ResMsg 			= $resp->ResMsg;
		$storeResponse->ResCode 		= (!empty($resp->ResCode)) ?
											$resp->ResCode : '';
		$storeResponse->merchantId 		= $resp->merchantId;
		$storeResponse->merOrderNo 		= $resp->merOrderNo;
		$storeResponse->orderNo 		= $resp->orderNo;
		$storeResponse->orderAmt 		= $resp->orderAmt;
		$storeResponse->orderDate 		= $resp->orderDate;
		$storeResponse->tranDate 		= $resp->tranDate;
		$storeResponse->orderStatus 	= $resp->orderStatus;
		$storeResponse->sign 			= $resp->sign;

		$storeResponse->save();
	}

}
