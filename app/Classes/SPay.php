<?php
namespace App\Classes;

use Log;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;
use \App\Http\Controllers\SPayController;

/* This is the API implementation of:
SarawakPay Wallet 
POS Integration Spec v2.0.1
June 2020
*/

class SPay
{
    //const SP_PUBLIC_KEY        = "/keys/sarawakpay_public_key.pub";
    const SP_PUBLIC_KEY        = "/keys/sarawakpay_prod_public_key.pub";
    const MERCHANT_PUBLIC_KEY  = "/keys/merchant_public_key.pub";
    const MERCHANT_PRIVATE_KEY = "/keys/merchant_private_key.pem";

	private $url;
	private $ipaddr;
	private $ch = null;

	public $spay_sign = null;
	public $spay_qsign = null;
	//public $spay_merchant_id = "M100004540";	// SEDC Testing env
	//public $spay_merchant_id = "M100084528";	// Ocosystem Production env
	public $spay_merchant_id   = "M100071557";	// SEDC Production env
	public $spay_curtype = "RM";
	public $spay_transaction_type = "1";
	public $spay_notify_url = "http://ocosystem.my/spay/notify";
	public $spay_detail_url = "http://ocosystem.my/spay/detail";
	public $spay_remark = "";

    public function __construct($ipaddr) {
		$this->ipaddr = (!empty($ipaddr)) ? $ipaddr : env('SPAY_URL');
		$this->url = $this->ipaddr;

		Log::debug('ipaddr='.$this->url);
		Log::debug('url='.$this->ipaddr);
    }


	public function set_channel($api_suffix) {
		$url = $this->url.$api_suffix;
		$this->ch = curl_init($url);

		Log::debug('url='.$url);

		//curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, 10);
		//curl_setopt($this->ch,CURLOPT_TIMEOUT, 10);

		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}


	/*
	Merchant sends a POST request to create order using this API.
	*/
	public function create_order(
			$merchantId, $qrCode, $curType, $notifyURL,
			$merOrderNo, $goodsName, $detailURL, $orderAmt,
			$remark, $transactionType) {

		/*
		//dump('MERCHANT PRIVATE KEY='.
			storage_path().self::MERCHANT_PRIVATE_KEY);;
		*/

		$data = array(
            'merchantId'		=> empty($merchantId) ?
									$this->spay_merchant_id : $merchantId,
            'qrCode'			=> $qrCode,
            'curType'			=> empty($curType) ?
									$this->spay_curtype : $curType,
            'notifyURL'			=> empty($notifyURL) ?
									$this->spay_notify_url : $notifyURL,
            'merOrderNo'		=> $merOrderNo,
            'goodsName'			=> $goodsName,
            'detailURL'			=> empty($detailURL) ?
									$this->spay_detail_url : $detailURL,
            'orderAmt'			=> $orderAmt,
            'remark'			=> empty($remark) ?
									$this->spay_remark : $remark,
            'transactionType'	=> empty($transactionType) ?
									$this->spay_transaction_type :
									$transactionType
		);

		Log::debug('create_order:'.json_encode($data));

		$json_data = json_encode($data, JSON_UNESCAPED_UNICODE |
			JSON_UNESCAPED_SLASHES);

		$signed_data = $data;

		Log::debug('json_data='.$json_data);
		Log::debug(storage_path().self::MERCHANT_PRIVATE_KEY);


		/* Sign transaction data with merchant's private key */
        $this->spay_sign = Encryption::generateSignature($json_data,
			storage_path().self::MERCHANT_PRIVATE_KEY);
        $signed_data['sign'] = $this->spay_sign;

		Log::debug($signed_data);
		Log::debug(storage_path().self::SP_PUBLIC_KEY);

		/* Encrypt transaction data with Sarawak Pay public key */
        $encrypted_data = Encryption::encrypt(json_encode($signed_data,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			storage_path().self::SP_PUBLIC_KEY);


		Log::debug('encrypted_data='.json_encode($encrypted_data));

        $payload = "FAPView=JSON&formData=" .
			str_replace('+', '%2B', $encrypted_data);


		Log::debug('payload='.json_encode($payload));

		$api_suffix = "BarCodePaymentAction.createOrder.do";
		$this->set_channel($api_suffix);
        curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);

		$response = curl_exec($this->ch);
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		Log::debug('http_code='.$http_code);
		Log::debug('encrypted_response='.$response);

		/* Decrypt response from Sarawak Pay */ 
        $decrypted_response = Encryption::decrypt($response,
			storage_path().self::MERCHANT_PRIVATE_KEY);

		Log::debug('decrypted_response='.$decrypted_response);

        // Verify Server Response
        if (Encryption::verifySignature($decrypted_response,
			storage_path().self::SP_PUBLIC_KEY)) {

			$ret['response'] = $decrypted_response;
			$ret['http_code'] = $http_code;

			Log::info($ret);

            return $ret;
        }

		return false;
	}


	public function query_order(
			$merchantId, $merOrderNo, $orderNo=null) {

		$data = array(
            'merchantId'		=> empty($merchantId) ?
									$this->spay_merchant_id : $merchantId,
            'merOrderNo'		=> $merOrderNo,
            //'orderNo'			=> $orderNo,
		);

		Log::debug('query_order: data='.json_encode($data));

		$json_data = json_encode($data, JSON_UNESCAPED_UNICODE |
			JSON_UNESCAPED_SLASHES);

		$signed_data = $data;

		/* Sign transaction data with merchant's private key */
        $this->spay_qsign = Encryption::generateSignature($json_data,
			storage_path().self::MERCHANT_PRIVATE_KEY);
        $signed_data['sign'] = $this->spay_qsign;

		/* Encrypt transaction data with Sarawak Pay public key */
        $encrypted_data = Encryption::encrypt(json_encode($signed_data,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			storage_path().self::SP_PUBLIC_KEY);

        $payload = "FAPView=JSON&formData=" .
			str_replace('+', '%2B', $encrypted_data);


		$api_suffix = "BarCodePaymentAction.queryOrder.do";
		$this->set_channel($api_suffix);
        curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);


		$response = curl_exec($this->ch);
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		Log::debug('query_order: response='.$response);

		/* Decrypt response from Sarawak Pay */ 
        $decrypted_response = Encryption::decrypt($response,
			storage_path().self::MERCHANT_PRIVATE_KEY);

		Log::debug('decrypted_response='.$decrypted_response);

        // Verify Server Response
        if (Encryption::verifySignature($decrypted_response,
			storage_path().self::SP_PUBLIC_KEY)) {

			$ret['response'] = $decrypted_response;
			$ret['http_code'] = $http_code;

			Log::info($ret);

            return $ret;
        }

		return false;
	}



	public function close_channel() {
		if (!empty($this->ch)) {
			curl_close($this->ch);
		}
	}
}
?> 
