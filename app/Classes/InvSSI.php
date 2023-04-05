<?php
namespace App\Classes;
use Log;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;

/* API implementation of: Invenco's Site Systems Interface */

class InvSSI
{
	private $url;
	private $ipaddr;
	private $ch = null;

    public function __construct($ipaddr = null) {
		if (!empty($ipaddr)) {
			$this->ipaddr = $ipaddr; 

		} else {
			$this->ipaddr = env('EPS_IPADDR');
		}

		$this->url = "http://".$this->ipaddr;
		Log::debug('ipaddr='.$this->ipaddr);
		Log::debug('url='.$this->url);
    }


	/* $api_suffix = ":8189/api/1.0/terminals" */
	public function set_channel($api_suffix) {
		$url = $this->url.$api_suffix;
		$this->ch = curl_init($url);

		dump('url='.$url);

		//curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, 10);
		//curl_setopt($this->ch,CURLOPT_TIMEOUT, 10);

		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}

	/* LIST OF SSI APIs:
	POST : http://<host>:8189/api/1.1/sales/processed/{transactionId}
	POST : http://<host>:8189/api/1.1/sales/processed?date={date}&time={time}


	GET : http://<host>:8189/api/1.0/transactions/sale/{transactionId}? vtid={vtid}&format={format}
	POST http://<host>:8189/api/1.0/transactions/sale/?vtid={vtid}
	POST http://<host>:8189/api/1.0/transactions/authorise/?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/sale/{transactionId}? vtid={vtid}&format={format}
	GET : http://<host>:8189/api/1.0/transactions/ authorise/{transactionId}?vtid={vtid}&format={format}
	DELETE http://<host>:8189/api/1.0/transactions/authorise/{transactionId}?vtid={vtid}
	POST http://<host>:8189/api/1.0/reconciliation/dayclose
	POST http://<host>:8189/api/1.0/reconciliation/dayclose?vtid=2
	GET : http://<host>:8189/api/1.0/reconciliation/dayclose/{transactionId}?format={format}
	GET : http://<host>:8189/api/1.0/terminals

	POST http://<host>:8189/api/1.0/transactions/balance?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/balance/{transactionId}?vtid={vtid}
&format={format}
	POST http://<host>:8189/api/1.0/transactions/reprint/?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/reprint/{transactionId}?vtid={vtid}&format={format}
	*/


	/*
	GET : http://<host>:8189/api/1.0/terminals
	*/
	public function get_all_terminals() {

		$api_suffix = ":8189/api/1.0/terminals";
		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}

	/*
	GET : http://<host>:8189/api/1.0/terminal/status
	curl -X 'GET'   http://$EPS_IPADDR:8189/api/1.0/terminal/status \
		-H 'accept: application/json'
	*/
	public function get_all_terminal_status() {

		$api_suffix = ":8189/api/1.0/terminal/status";
		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}

	/*
	GET : http://<host>:8189/api/1.1/sales/new{/noOfSales}
	*/
	public function get_completed_sales($no_of_sales=20) {
		$api_suffix = ":8189/api/1.1/sales/new/".$no_of_sales.'?format=pdb';

		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 


		// TODO: DANGER! Hardcoding response for testing only!!
		/*
		$response = json_encode('[
			{
				"saleId": 4,
				"terminalId": 2,
				"mop": "708381",
				"cardType": "FLEET",
				"receipt": "Card Desc
				Mesra card\r\nTerminal
				12345678\r\nSite Id
				␣
				˓ → 123456789012345\r\nSite Tran No
				000006\r\nPOS STAN
				000004\r\nInvoice␣
				˓ → No
				a1f692\\r\n>>Ref
				000000000012\r\nCard
				70838155.....
				˓ → 3375\r\nCard Exp
				05/59\r\nB.Approval
				100005\r\nB.Trace No
				␣
				˓ →
				6543\r\n------------------------------\r\n{f12}APPROVED 00 000\r\n",
				"amount": 169.76,
				"originalAmount": 169.76,
				"transactionTime": "21/02/2019 13:30:37",
				"saleItems": [
				{
				"amount": 122,
				"productId": "401",
				"quantity": 10,
				"unitPrice": 12.2,
				"originalAmount": 122
				},
				{
				"amount": 47.76,
				"productId": "101",
				"quantity": 22.123,
				"unitPrice": 2.159,
				"originalAmount": 47.76
				}
				],
				"transactionType": "PREAUTH",
				"uuid": "a1f6920a99dc489cac2c85a9ba5c91fa"
			},
			{
				"loyaltyDetails": {
				"pointsBalance": 2.223,
				"issuedPoints": 5.223,
				"bonusPoints": 4.223,
				"cardNumber": "70838155117203375=5905201686053939"
				},
				"saleId": 10,
				"terminalId": 2,
				"mop": "708381",
				"cardType": "FLEET",
				"receipt": "Card Desc
				SmartPay\r\nTerminal
				FITID01\r\nSite Id
				␣
				˓ →
				FIMID01\r\nSite Tran No
				16\r\nPOS STAN
				000010\r\nInvoice␣
				˓ → No
				ddea92\r\n>>Ref
				000000000004\r\nCard
				70838153.....
				˓ → 9489\r\nCard Exp
				04/21\r\nB.Approval
				100016\r\nB.Trace No
				␣
				˓ → ROCNum01\r\nApp
				Smartpay.ICC\r\nApp ID
				A0534D415254504159CC\r\n------------------
				˓ → ------------\r\n{f12}APPROVED 00 App\r\n",
				"amount": 122,
				"originalAmount": 122,
				"transactionTime": "22/02/2019 09:54:16",
				"saleItems": [
				{
				"amount": 122,
				"productId": "401",
				"quantity": 10,
				"unitPrice": 12.2,
				"originalAmount": 122
				}
				],
				"transactionType": "PURCHASE",
				"uuid": "ddea920a99dc489cac2c85a9ba5c91f5"
			},
			{
				"loyaltyDetails": {
				"pointsBalance": 0,
				"issuedPoints": 0
				},
				"saleId": 10,
				"terminalId": 2,
				"mop": "708381",
				"cardType": "FLEET",
				"receipt": "Card Desc
				SmartPay\r\nTerminal
				FITID01\r\nSite Id
				␣
				˓ →
				FIMID01\r\nSite Tran No
				17\r\nInvoice No ddea92\r\n>>Ref
				␣
				˓ → 000000000005\r\nCard
				70838153.....9489\r\nCard Exp
				04/21\r\nB.Approval ␣
				˓ →
				100017\r\nB.Trace No
				ROCNum01\r\n------------------------------\r\n{f12}
				˓ → APPROVED 00 App\r\n",
				"amount": 122,
				"originalAmount": 122,
				"transactionTime": "22/02/2019 09:56:50",
				"saleItems": [
				{
				"amount": 122,
				"productId": "401",
				"quantity": 10,
				"unitPrice": 12.2,
				"originalAmount": 122
				}
				],
				"transactionType": "VOID",
				"uuid": " ddea920a99dc489cac2c85a9ba5c91f5"
			}
		]');
		*/

		$response = array(array(
			"saleId"=> 4,
			"terminalId"=> 2,
			"mop"=> "708381",
			"cardType"=> "FLEET",
			"receipt"=> "
			Card Desc        Mesra card
			Terminal           12345678
			Site Id     123456789012345
			Site Tran No         000006
			POS STAN             000004
			Invoice No           a1f692
			{n}>>Ref            000000000012
			Card      70838155.....3375
			Card Exp              05/59
			B.Approval           100005
			B.Trace No             6543
			---------------------------
			   {f1}{f12}{b}APPROVED 00 000{/b}",
			"amount"=> 169.76,
			"originalAmount"=> 169.76,
			"transactionTime"=> "21/02/2019 13:30:37",
			"saleItems"=> array(array(
				"amount"=> 47.76,
				"productId"=> "101",
				"quantity"=> 22.123,
				"unitPrice"=> 2.159,
				"originalAmount"=> 47.76
			)),
			"transactionType"=> "PREAUTH",
			"uuid"=> "a1f6920a99dc489cac2c85a9ba5c91fa"
		));

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}


	/*
	POST : http://<host>:8189/api/1.1/sales/processed/{saleId}
	*/
	public function process_sale($saleId) {
		$api_suffix = ":8189/api/1.1/sales/processed/".$saleId;

		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}


	public function close_channel() {
		if (!empty($this->ch)) {
			curl_close($this->ch);
		}
	}
}
?> 
