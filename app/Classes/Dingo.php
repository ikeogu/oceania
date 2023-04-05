<?php
namespace App\Classes;
use Log;
use GuzzleHttp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \App\Console\Commands\dingo_gatewayd;

class Dingo
{
	private $url;
	private $ipaddr;
    private $username;
    private $password;
	private $ch = null;

    public function __construct($ipaddr=null) {
		$this->url = env('PTS_URL');
		$this->ipaddr = env('PTS_IPADDR');
		$this->username = (!empty($username)) ? $username : env('PTS_USER');
		$this->password = (!empty($password)) ? $password : env('PTS_PASSWD');

		Log::debug('url='.$this->url);
		Log::debug('ipaddr='.$this->ipaddr);
		Log::debug('username='.$this->username);
		Log::debug('password='.$this->password);
    }


	public function set_channel() {
		$url = $this->url;
		$this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_USERPWD,"$this->username:$this->password");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}


	public function pts2_pump_get_transaction_details($pump, $transaction_no) {
        $data = array(
            'Protocol' => 'jsonPTS',
            'Packets' => array(array (
                'Id' => 1,
                'Type' => 'PumpGetTransactionInformation',
                'Data' => array(
                    'Pump' => $pump,
                    'Transaction' => $transaction_no
                )
            ))
        );

        $this->set_channel();
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = json_decode(curl_exec($this->ch));
        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        $ret['response'] = $response;
        $ret['http_code'] = $http_code;

        return $ret;
	}


    public function pts2_pump_get_totals($pump, $nozzle=1) {
        $data = array(
            'Protocol' => 'jsonPTS',
            'Packets' => array(array (
                'Id' => 1,
                'Type' => 'PumpGetTotals',
                'Data' => array(
                    'Pump' => $pump,
                    'Nozzle' => $nozzle,
                )
            ))
        );

        $this->set_channel();
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = json_decode(curl_exec($this->ch));
        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        $ret['response'] = $response;
        $ret['http_code'] = $http_code;

        return $ret;
    }


	public function pts2_pump_get_status($pump)
	{
        $data = array(
            'Protocol' => 'jsonPTS',
            'Packets' => array(array (
                'Id' => 1,
                'Type' => 'PumpGetStatus',
                'Data' => array(
                    'Pump' => $pump,
                )
            ))
        );

        $this->set_channel();

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = json_decode(curl_exec($this->ch));

        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $ret['response'] = $response;
        $ret['http_code'] = $http_code;

        return $ret;
	}


	public function pts2_authorize_pump($pump)
	{
		$result = null;
		Log::info('***** pts2_authorize_pump: pump='.$pump);

		// Setting parameters for FullTank
		$dataDetails = [
			'Pump' => $pump,
			'Type' => 'FullTank',
			'Dose' => null,
			'AutoCloseTransaction' => true
		];

		$ptsdata = array(
			'Protocol' => 'jsonPTS',
			'Packets' => array(array (
				'Id' => 1,
				'Type' => 'PumpAuthorize',
				'Data' => $dataDetails
			))
		);

		$this->set_channel();
		Log::info(json_encode($ptsdata));

		curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($ptsdata));
		$pts_response = json_decode(curl_exec($this->ch));

		Log::info('pts_response='.json_encode($pts_response));

		$result = $pts_response;

		return $result;
	}


	public function validate_access_code($code)
	{
		Log::info('***** validate_access_code: code='.$code);

		$result = false;
		try {
			$res= DB::table('users')->
				where('access_code', trim($code))->
				first();

			Log::info('res='.json_encode($res));
			if(!empty($res)) $result = true;

		} catch (\Exception $e) {
			dump("Error! 'users' query kaput!");
            \Log::error([
                "Mesg"   => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);
		}

		Log::info('result='.json_encode($result));
		return $result;
	}


	public function poll_scanner_via_serial_byid($pump, $pump_dev)
	{
		Log::info('***** poll_scanner: pump='.$pump);
		$pdev = $pump_dev[$pump];
		Log::info('poll_scanner: pdev='.$pdev);

		$scanned = null; $s = null;
		try {
			$fp = fopen($pdev, 'rb');

		} catch (\Exception $e) {
			dump('Error: fopen() failed!');
            \Log::error([
                "Mesg"   => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);

			return -1;
		}

		while ($bin = fread($fp, 24)) {
			$u = unpack(sprintf('C%d', 24), $bin);
			$u = array_values($u);

			$value = $u[23]*4096 + $u[22]*256 + $u[21]*16 + $u[20]; 
			$code  = $u[19]*16 + $u[18]; 
			$type  = $u[17]*16 + $u[16]; 

			if ($type==0 && $code==0 && $value==0) continue;

			if ($value==1) {
				switch($code) {
					case 2:
						$s .= "1";
						break;
					case 3:
						$s .= "2";
						break;
					case 4:
						$s .= "3";
						break;
					case 5:
						$s .= "4";
						break;
					case 6:
						$s .= "5";
						break;
					case 7:
						$s .= "6";
						break;
					case 8:
						$s .= "7";
						break;
					case 9:
						$s .= "8";
						break;
					case 10:
						$s .= "9";
						break;
					case 11:
						$s .= "0";
						break;
					case 28:
						$scanned = $s;
						$s = null;
					default:
				}
			}

			if (!empty($scanned)) break;
		}

		fclose($fp);
		return $scanned;
	}


	public function poll_scanner_by_serialdev($pump, $pump_dev)
	{
		Log::info('***** poll_scanner: pump='.$pump);
		$pdev = $pump_dev[$pump];
		Log::info('poll_scanner: pdev='.$pdev);

		$scanned = null; $s = null;

		/*
		try {
			$fp = fopen($pdev, 'rb');

		} catch (\Exception $e) {
			dump('Error: fopen() failed!');
            \Log::error([
                "Mesg"   => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);

			return -1;
		}
		*/

		dump('AFTER fopen()');

		$stream = stream_socket_client("udg:$pdev", $errno, $errstr, 30);

		while(true){
			$line = stream_get_contents( $stream );
			dump($line);

			if($line == '\n') break;
		}

		/*
		while ($c = fread($fp, 1)) {
			if ($value==1) {
				switch($code) {
					case 2:
						$s .= "1";
						break;
					case 3:
						$s .= "2";
						break;
					case 4:
						$s .= "3";
						break;
					case 5:
						$s .= "4";
						break;
					case 6:
						$s .= "5";
						break;
					case 7:
						$s .= "6";
						break;
					case 8:
						$s .= "7";
						break;
					case 9:
						$s .= "8";
						break;
					case 10:
						$s .= "9";
						break;
					case 11:
						$s .= "0";
						break;
					case 28:
						$scanned = $s;
						$s = null;
					default:
				}
			}

			if (!empty($scanned)) break;
		}
		*/

		fclose($fp);
		return $scanned;
	}


	public function poll_scanner_by_inputev($pump, $pump_dev)
	{
		Log::info('***** poll_scanner: pump='.$pump);
		$pdev = $pump_dev[$pump];
		Log::info('poll_scanner: pdev='.$pdev);

		$scanned = null; $s = null;
		try {
			$fp = fopen($pdev, 'rb');

		} catch (\Exception $e) {
			dump('Error: fopen() failed!');
            \Log::error([
                "Mesg"   => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);

			return -1;
		}

		while ($bin = fread($fp, 24)) {
			$u = unpack(sprintf('C%d', 24), $bin);
			$u = array_values($u);

			$value = $u[23]*4096 + $u[22]*256 + $u[21]*16 + $u[20]; 
			$code  = $u[19]*16 + $u[18]; 
			$type  = $u[17]*16 + $u[16]; 

			if ($type==0 && $code==0 && $value==0) continue;

			if ($value==1) {
				switch($code) {
					case 2:
						$s .= "1";
						break;
					case 3:
						$s .= "2";
						break;
					case 4:
						$s .= "3";
						break;
					case 5:
						$s .= "4";
						break;
					case 6:
						$s .= "5";
						break;
					case 7:
						$s .= "6";
						break;
					case 8:
						$s .= "7";
						break;
					case 9:
						$s .= "8";
						break;
					case 10:
						$s .= "9";
						break;
					case 11:
						$s .= "0";
						break;
					case 28:
						$scanned = $s;
						$s = null;
					default:
				}
			}

			if (!empty($scanned)) break;
		}

		fclose($fp);
		return $scanned;
	}


	public function close_channel() {
		if (!empty($this->ch)) {
			curl_close($this->ch);
		}
	}
}
?> 
