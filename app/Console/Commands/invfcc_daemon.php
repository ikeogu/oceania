<?php

namespace App\Console\Commands;

use \App\Classes\InvFCC;
use \App\Models\Event;
use \App\Http\Controllers\OposPetrolStationPumpController;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Nelexa\Buffer\Buffer;
use Nelexa\Buffer\StringBuffer;


class invfcc_daemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

	// Configuration parameters
    protected $signature = 'invfcc:daemon';
    protected $version = '1.0.0';
    protected $port = 6001;
	protected $host = '';
	protected $conn_backlog = 10;
	protected $reservation_id = 8;
	protected $stop_server = false;
	protected $max_child_processes = 2;

	private $prod = [];


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invenco FCC Pre-Forking Daemon';

    /*
     * Create a new command instance.
	 * This implements a classic pre-forking daemon to a maximum number
	 * of child server processes
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		$this->prod = $this->fetch_latest_price();

		ini_set('error_reporting', E_ALL ^ E_NOTICE);
		ini_set('display_errors', 1);
		set_time_limit(0);

		/* As of PHP 4.3.0 PCNTL uses ticks as the signal handle callback
		mechanism, which is much faster than the previous mechanism.  This
		change follows the same semantics as using "user ticks". You must use
		the declare() statement to specify the locations in your program, where
		callbacks are allowed to occur for the signal handler to function
		properly. */
		declare(ticks = 1);
	}


	public function fetch_latest_price() {
		// Squidster: supposed to fetch product prices
		// We hardcode temporarily
		$prod = array(
		  1 =>(object)array('name'=>'Diesel B20','price'=>'2.15','ogfuel_id'=>101),
		  2 =>(object)array('name'=>'Ron95',     'price'=>'2.05','ogfuel_id'=>102),
		  3 =>(object)array('name'=>'Ron97',     'price'=>'3.81','ogfuel_id'=>103),
		  4 =>(object)array('name'=>'Diesel B7', 'price'=>'2.35','ogfuel_id'=>104)
		);

		return $prod;
	}


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		/* Initialize variables */
		$mtype = null;
		$input = null;
		$output = null;
		$injson = null;
		$pump_no = null;
		$child_processes = array();

		$this->host = env('LOCAL_IPADDR', '127.0.0.1');
		$host = $this->host;
		$port = $this->port;

		dump('Starting '.$this->description.' '.$this->version.'....');
		dump('Listening at '.$host.':'.$port);

		// Clearing all pump reservations
		DB::table('opt_mtermsync')->truncate();

		// Fetch the latest product price
		//$prod = $this->fetch_latest_price();
		$prod = $this->prod;

		// Create a socket
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or
            dump("Error: could not create socket" . PHP_EOL);

		if (!is_resource($socket)) {
            dump('Error: Unable to create socket: '.
                socket_strerror(socket_last_error()) . PHP_EOL);
        }
			
		// Set socket to resuse address
        if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            dump('Error: Unable to set option on socket: '.
                socket_strerror(socket_last_error()));
        }

		// Bind socket to host and port
        if (!socket_bind($socket, $host, $port)) {
            dump("Error: Could not bind to socket".
                socket_strerror(socket_last_error()));
        }

        $rval = socket_get_option($socket, SOL_SOCKET, SO_REUSEADDR);
        if ($rval === false) {
            dump('Error: Unable to get socket option: '.
                socket_strerror(socket_last_error()));

        } else if ($rval !== 0) {
            //dump('SO_REUSEADDR is set on socket !');
        }

        // Start listening to socket
        if (!socket_listen($socket, $this->conn_backlog)) {
            dump("Error: Could not set up socket listener" .
                socket_strerror(socket_last_error()));
        }

		$stop_server = $this->stop_server;
		// Fork child servers first; pre-forking mode
		while (!$stop_server) {
			if (!$stop_server && (count($child_processes) < $this->max_child_processes)) {
				// TODO: acquire a task
				// create a child process
				$pid = pcntl_fork();
				if ($pid == -1) {
					// Error occurred - unable to create a process
					Log::error('ERROR handle: pnctl_fork: '.
						posix_strerror(posix_get_last_error()));

				} elseif ($pid) {
					// Daemon: A process have been succesfully created
					$child_processes[$pid] = true;
					dump($child_processes);

				} else {
					// Child: This serves clients
					$this->child_work($socket);
				}
			} else {
				// to prevent a loop idling
				sleep(1); 
			}

			// check whether one of the child's died or not
			while ($signaled_pid = pcntl_waitpid(-1, $status, WNOHANG)) {
				if ($signaled_pid == -1) {
					// no child's left
					$child_processes = array();
					break;
				} else {
					unset($child_processes[$signaled_pid]);
				}
			}
		}
	}


	// This function is the primary work handler for a child server process
	public function child_work($socket) {
		$pid = posix_getpid() or
			dump("Error: Could not create a process ID");
		if ($pid < 0) {
			// Error occurred - unable to create a process ID 
			Log::error('ERROR handle: posix_getpid: '.
				posix_strerror(posix_get_last_error()));
		}

		$sid = posix_setsid() or
			dump("Error: Could not create a session ID");
		if ($sid < 0) {
			// Error occurred - unable to create a session ID 
			Log::error('ERROR handle: posix_setsid: '.
				posix_strerror(posix_get_last_error()));
		}

		// Accept incoming connection
		$client = socket_accept($socket) or
			dump("Error: Could not accept incoming connection");
		if ($client < 0) {
			Log::error('ERROR handle: socket_accept: '.
				posix_strerror(posix_get_last_error()));
		}

		// Infinite process loop
		while(true) {
			try {
				// This is where child works with the client
				socket_write($client, "Child ".$pid." echo> ") or
					dump("Error 1: ".$pid." Could not write to socket");

				$input = socket_read($client, 2048) or 
					dump("Error 2: ".$pid." Could not write to socket");

				socket_write($client, $input) or
					dump("Error 3: ".$pid." Could not write to socket");

			} catch (\Exception $e) {
				Log::error([
					"Error"     => $e->getMessage(),
					"File"      => $e->getFile(),
					"Line No"   => $e->getLine()
				]);
				exit;
			}

			printf("Child %d echo'd: '%s'".PHP_EOL, $pid, trim($input));

			// TODO: a child process - some workload here...
			dump('BEFORE input='.$input);
			$input = substr($input, 4);	// Skip first 4 bytes

			$input = preg_replace('/[^A-Za-z0-9\{\}\-\"\[\]:,._]/', '',
				trim($input));

			dump('AFTER input='.$input);

			if (!empty($input) && $input != '\r\n') {
				$injson = json_decode($input);
				$mytype = null;
				$payload = null;
				if (!empty($injson)) {
					$mtype = $injson->Header->MessageType;
					$payload = $injson->Payload;
				}

				var_dump('handle: Received input = '.$input);
				dump($injson);
				dump('MessageType='.$mtype);
				
				/* Respond if there is input */
				if (!empty($mtype)) {

					/* Prepare output based on the MessageType */
					switch($mtype) {
						case "FCCGetConfigRequest":
							dump("Received FCCGetConfigRequest...");
							$output = $this->get_config_response($injson);
							break;

						case "FCCGetFuelModeRequest":
							dump("Received FCCGetFuelModeRequest...");
							$output = $this->get_fuelmode_response($injson);
							break;

						case "FCCReserveFuelPointRequest":
							dump("Received FCCReserveFuelPointRequest...");
							$this->reservation_id++;
							$pump_no = $payload->DeviceID;
							InvFCC::put_rsrv_id( $pump_no,
								$this->reservation_id);
							$reservation_id = InvFCC::get_rsrv_id($pump_no);

							dump('#### reservation_id='.$reservation_id);

							$out1 = $this->reserve_fuelpoint_response(
								$injson, $reservation_id);

							$ret1 = $this->sendEPS($out1, $client);
							dump('reserve_fuelpoint_response: return from sendEPS: ret='.$ret1);

							// After Reservation:
							// DevState=Idle,s Reserved=true
							$output = $this->fuelpoint_state_change_event(
								0, $pump_no, "Idle", "None", 0,
								$reservation_id, 'NozzleDown');
							dump("Sending fuelpoint_state_change AFTER reservation...");
							break;

						case "FCCFreeFuelPointRequest":
							dump("Received FCCFreeFuelPointRequest...");
							$output = $this->free_fuelpoint_response($injson);
							break;

						case "FCCAuthorizeFuelPointRequest":
							dump("Received FCCAuthorizeFuelPointRequest...");
							$pump_no = $payload->DeviceID;
							$reservation_id = InvFCC::get_rsrv_id($pump_no);
							$out1 = $this->authorize_fuelpoint_response(
								$injson, $client, $socket, $this->prod,
								$reservation_id);

							if ($out1 > 0) {
								$ret1 = $this->sendEPS($out1, $client);
								dump('reserve_fuelpoint_response: return from sendEPS: ret='.$ret1);
								$output = $ret1;
							} else {
								$output = null;
							}
							break;

						case "FCCTerminateFuelPointRequest":
							$pump_no = $payload->DeviceID;
							$trans_no = $payload->TransactionSeqNo;
							dump("Received FCCTerminateFuelPointRequest...");
							$output = $this->terminate_fuelpoint_response($injson);
							$ret1 = $this->sendEPS($output, $client);
							dump('terminate_fuelpoint_response: return from sendEPS: ret='.$ret1);

							// Send FCCDeliveryStateChangeEvent

							// Need to get the last transaction details from
							// getPumpStatus
							$pkt = $this->get_last_transaction_details($pump_no);
							// Fetch PTS data
							$type = $pkt['Type'];
							$data = $pkt['Data'];
							$pump = $data['Pump'];

							$nozzle_up = 0;
							$last_transaction = 0;
							$last_price = 0;
							$last_volume = 0;
							$last_amount = 0;

							if ($type == 'PumpIdleStatus') {
								$last_transaction = $data['LastTransaction'];
								$nozzle_up        = $data['NozzleUp'];
								$last_volume      = $data['LastVolume'];
								$last_price       = $data['LastPrice'];
								$last_amount      = $data['LastAmount'];
								$last_nozzle      = $data['LastNozzle'];
							}

							// Send FCCDeliveryCompleteEvent with the details
							$output = $this->delivery_complete_event(
								$pump, $last_nozzle, $trans_no,
								$last_price, 0, 0);
							break;

						case "FCCGetDeliveryDetailsRequest":
							dump("L256 Received FCCGetDeliveryDetailsRequest...");
							$pump_no = $payload->DeviceID;
							$trans_no = $payload->DeliveryID;
							$reservation_id = InvFCC::get_rsrv_id($pump_no);
							$output = $this->get_deliverydetails_response(
								$injson, $prod, $reservation_id, $client);
							$ret = $this->sendEPS($output, $client);

							dump('output from get_deliverydetails_response');
							$out1 = preg_replace('/[^A-Za-z0-9\{\}\-\"\[\]:,._]/', '',
								trim($output));

							// Have to see if Delivery State is "PaidOff"
							// If it is then we have to send Delivery
							// CompleteEvent
							$ary = str_getcsv($out1, ",");
							$found = array_filter($ary, function ($v) {
								if (strpos($v, 'PaidOff') !== false) {
									return $v;
								}
							});

							// DANGER: HARDCODING
							// TESTING ONLY
							/*
							$nozzle = 99;
							$price  = 51.11;
							$volume = 52.22;
							$amount = 53.33;

							+"Pump": 6
							+"Transaction": 75
							+"State": "Finished"
							+"DateTimeStart": "2000-07-23T00:04:41"
							+"DateTime": "2000-07-23T00:05:00"
							+"Nozzle": 1
							+"Volume": 30.03
							+"Amount": 200.0
							+"Price": 6.66
							+"UserId": 1
							*/

							// Initialization
							$nozzle = 0;
							$price  = 0;
							$volume = 0;
							$amount = 0;
							$output = null;

							// Get last transaction details from PTS2
							$data = $this->pts2_get_transaction_details(
								$pump_no, $trans_no);

							if (!empty($data)) {
								$nozzle = (!empty($data->Nozzle)?
									$data->Nozzle:0);
								$price  = (!empty($data->Price)?
									$data->Price:0);
								$volume = (!empty($data->Volume)?
									$data->Volume:0);
								$amount = (!empty($data->Amount)?
									$data->Amount:0);
							}

							// We have found "PaidOff"
							if (!empty($found)) {
								$val =  array_values($found)[0];
								if (!empty($val)) {
									$out2 = $this->delivery_complete_event(
										$pump_no, $nozzle, $trans_no, $price,
										$volume, $amount);
								}

								$output = $out2;
							}
							break;

						case "FCCLockFuelSaleRequest":
							dump("Received FCCLockFuelSaleRequest...");
							$output = $this->lock_fuelsale_response($injson);
							break;

						case "FCCClearFuelSaleRequest":
							dump("Received FCCClearFuelSaleRequest...");
							$out1 = $this->clear_fuelsale_response($injson);
							$ret1 = $this->sendEPS($out1, $client);
							dump('clear_fuelsale_response: return from sendEPS: ret='.$ret1);

							// Have to clear reservation after clearing sale
							$output = $this->free_fuelpoint_response($injson);
							break;

						case "tauth":
							$dose = $payload->dose;
							$pump_no = $payload->DeviceID;
							$output = $this->store_authdata($pump_no, $dose);
							break;

						case "end":
							$output = "baz:".$payload;
							break;

						default:
							$output = "dunno";
					}

					// Send JSON message to EPS via socket
					if (!empty($output)) {
						$ret = $this->sendEPS($output, $client);
						dump('return from sendEPS: ret='.$ret);
					}

					if ($mtype == "end") {
						socket_close($client);
					}
				}
			} 

			/* Release resources */
			$mtype = null;
			$input = null;
			$output = null;
			$injson = null;
		}

		socket_close($socket);
		exit($pid);
	}


	public function get_last_transaction_details($pump_no) {
		$pc = new OposPetrolStationPumpController;
		$ptsret = $pc->pumpGetStatus($pump_no, null);
		$pstat = json_decode($ptsret->getContent(), true);
		$packet = $pstat['data']['response']['Packets'][0];
		dump('get_last_transaction_details: packet=');
		dump($packet);

		// Fetch PTS data
		$type = $packet['Type'];
		$data = $packet['Data'];
		$pump = $data['Pump'];

		/*
		switch ($type) {
			case 'PumpIdleStatus':
				$last_transaction = $data['LastTransaction'];
				$pump             = $data['Pump'];
				$nozzle_up        = $data['NozzleUp'];
				$last_volume      = $data['LastVolume'];
				$last_price       = $data['LastPrice'];
				$last_amount      = $data['LastAmount'];
				$last_nozzle      = $data['LastNozzle'];
				break;
			
			default:
		}
		*/
		return $packet;
	}


	public function get_config_response($injson) {
		dump('get_config_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->get_config_response($injson, $this->prod);

		//dump($response);

		return $response;
	}


	public function get_fuelmode_response($injson) {
		dump('get_fuelmode_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->get_fuelmode_response($injson);

		//dump($response);

		return $response;
	}


	public function get_deliverydetails_response($injson, $prod,
		$reservation_id, $client) { 
		dump('get_deliverydetails_response()');

		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->get_deliverydetails_response($injson, $prod,
			$reservation_id, $client);

		//dump($response);
		return $response;
	}


	public function delivery_progress_event(
		$dev_id, $trans_no, $price, $volume, $amount) {

		dump('delivery_progress_event()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->delivery_progress_event(
			$dev_id, $trans_no, $price, $volume, $amount);

		//dump($response);

		return $response;
	}


	public function delivery_started_event(
		$dev_id, $trans, $prod_id, $price) {

		dump('delivery_started_event()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->delivery_started_event(
			$dev_id, $trans, $prod_id, $price);

		//dump($response);

		return $response;
	}



	public function delivery_complete_event(
		$pump, $last_nozzle, $trans,
		$price, $last_volume, $last_amount) {

		dump('delivery_complete_event()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->delivery_complete_event(
			$pump, $last_nozzle, $this->prod, $trans,
			$price,$last_volume, $last_amount);

		//dump($response);

		return $response;
	}


	public function reserve_fuelpoint_response($injson, $reservation_id) {
		dump('reserve_fuelpoint_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->reserve_fuelpoint_response($injson, $reservation_id);

		//dump($response);

		return $response;
	}


	public function free_fuelpoint_response($injson) {
		dump('free_fuelpoint_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->free_fuelpoint_response($injson);

		//dump($response);

		return $response;
	}


	public function fuelpoint_state_change_event($trans_no, $dev_id, $dev_state,
		$prd_selected, $ogfuel_id, $reservation_id, $nozzle) {

		dump('fuelpoint_state_change_event()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->fuelpoint_state_change_event($trans_no, $dev_id,
			$dev_state, $prd_selected, $ogfuel_id, $reservation_id, $nozzle);

		//dump($response);

		return $response;
	}


	public function terminate_fuelpoint_response($injson) {

		dump('terminate_fuelpoint_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->terminate_fuelpoint_response($injson);

		//dump($response);

		return $response;
	}


	public function authorize_fuelpoint_response($injson, $client, $socket,
		$ogfuel_id, $reservation_id) {

		dump('authorize_fuelpoint_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->authorize_fuelpoint_response(
			$injson, $client, $socket, $ogfuel_id, $reservation_id);

		//dump($response);

		return $response;
	}

	public function store_authdata($pump_no, $dose) {
		$data = [
			"pump_no" => $pump_no,
			"dose" => number_format($dose * 100),	// convert to cents
			"created_at" => Carbon::now(),
			"updated_at" => Carbon::now()
		];

		$ta = TestAuth::create($data);
		$ta->save();

		return $ta;
	}




	/* Function to send JSON data to EPS via a socket */
	public function sendEPS($json, $client) {

		$output = preg_replace('/[^A-Za-z0-9\{\}\-\"\[\]:,._]/', '',
			trim($json));

		$buffer = new StringBuffer($output);
		$buffer->setOrder(Buffer::LITTLE_ENDIAN);

		dump('***** '.Carbon::now().' sendEPS() *****');
		var_dump($buffer->toString());

		$nlen = pack('N', $buffer->size());

		// Hardcode length to 1227
		//$nlen = b"\x00\x00\x04\xcb";

		dump($buffer->size());
		dump($nlen);

		/* Respond to input */
		$ret = socket_write($client, $nlen.$buffer->toString(),
			$buffer->size()+4) or  
			dump("Error: Could not write output\n"); 

		/* Release buffer resources */
		$buffer->close();

		return $ret;
	}


	/* Function to receive binary data from EPS via a socket */
	public function recvEPS($extrans, $client, $socket, $packet) {

		dump('***** '.Carbon::now().' recvEPS() 1 *****');

		/*
		if (!socket_set_option($client, SOL_SOCKET, SO_RCVTIMEO,
		array('sec'=>1, 'usec'=>0))) {
		dump('Error: Unable to set option on socket: '.
			socket_strerror(socket_last_error()));
		}
		*/
		$pump = $nozzle = $volume = $price = 
			$amount = $trans = $user = null;

		$out = 1;

		// Fetch PTS data
		$type = $packet['Type'];
		$data = $packet['Data'];
		$pump = $data['Pump'];

		dump('***** '.Carbon::now().' recvEPS() 2 *****');

		switch ($type) {
		case 'PumpFillingStatus':

			dump('***** '.Carbon::now().' recvEPS() 3 *****');

			$nozzle = $data['Nozzle'];
			$volume = $data['Volume'];
			$price  = $data['Price'];
			$amount = $data['Amount'];
			$trans  = $data['Transaction'];
			$user   = $data['User'];

			// If Delivery status, then we'll send FCCDeliveryProgressEvent
			$dev_state = "Fueling";
			$ogfuel_id = $nozzle;

			dump('trans ='.$trans);
			dump('nozzle='.$nozzle);
			dump('price ='.$price);
			dump('volume='.$volume);
			dump('amount='.$amount);

			/*
			// Send FCCFuelPointStateChangeEvent
			$out2 = $this->fuelpoint_state_change_event(
				$trans, $pump, "Fueling", "Valid", $ogfuel_id, 0,
				"NozzleUp");

			$ret2 = $this->sendEPS($out2, $client);
			dump('recvEPS: fuelpoint_state_change_event from sendEPS: ret2='.$ret2);
			*/

			// Send DeliveryProgress event to EPS
			$output = $this->delivery_progress_event(
				$pump, $trans, $price, $volume, $amount);
			$ret = $this->sendEPS($output, $client);
			dump('recvEPS: return from sendEPS: ret='.$ret);

			break;

		case 'PumpIdleStatus':

			dump('***** '.Carbon::now().' recvEPS() 4 *****');

			//  EPS will send query
			$ogfuel_id = 0;
			$trans_no  = 0;
			$volume    = 0;
			$amount    = 0;

			$last_transaction = $data['LastTransaction'];
			$pump             = $data['Pump'];
			$nozzle_up        = $data['NozzleUp'];
			$last_volume      = $data['LastVolume'];
			$last_price       = $data['LastPrice'];
			$last_amount      = $data['LastAmount'];
			$last_nozzle      = $data['LastNozzle'];

			// Check if pump has NOZZLE UP!!
			if (!empty($nozzle_up)) {

				dump("***** recvEPS: NOZZLE UP detected! pump=".$pump.
				", nozzle_up=".$nozzle_up);

				/*
				// This hangs here after NOZZLE UP for approx 80-90s!!
				// Send FCCFuelPointStateChangeEvent
				$out2 = $this->fuelpoint_state_change_event(
					$trans, $pump "Fueling", "Valid", 0, 0, "NozzleUp");
				$ret2 = $this->sendEPS($out2, $client);
				dump('recvEPS: fuelpoint_state_change_event from sendEPS: ret2='.$ret2);
				*/

			// Check if pump has just NOZZLE DOWN!
			} else if ($extrans != $last_transaction) {

				dump('***** '.Carbon::now().' recvEPS() 5 *****');

				// Read data from the client's socket
				try {

					dump('***** '.Carbon::now().' recvEPS() 6 *****');

					if (!$input = socket_read($client, 1024)) { 
						dump("Error: Could not read input\n");

						// Accept incoming connection
						$client = socket_accept($socket) or
							dump("Error: Could not accept incoming connection");

						// Error condition! Have to recover!!
						// Have to clear reservation 
						dump("We have socket_read() failure!! Recovering...");
						$output = $this->free_fuelpoint_response(null, $pump);
						$ret1 = $this->sendEPS($output, $client);
						dump('free_fuelpoint_response: return from sendEPS: ret='.$ret1);
					} else {
						dump('socket_read: SUCCESS!');
					}
				} catch (\Exception $e) {
					// It' possible to get a:
					//  socket_read(): unable to read from socket [104]: Connection reset by peer
					dump('ERROR! recvEPS: socket_read() '.json_encode($e));
				}

				dump('***** '.Carbon::now().' recvEPS() 7 *****');

				$input = preg_replace('/[^A-Za-z0-9\{\}\-\"\[\]:,._]/', '',
					trim($input));


				if (!empty($input) && $input != '\r\n') {
					$injson = json_decode($input);
					$mytype = null;
					$payload = null;
					if (!empty($injson)) {
						$mtype = $injson->Header->MessageType;
						$payload = $injson->Payload;
					}

					dump('***** '.Carbon::now().' recvEPS() 8 *****');
					var_dump('recvEPS: Received input = '.$input);
					dump($injson);
					dump('MessageType='.$mtype);

					/* Respond if there is input */
					if (!empty($mtype)) {
						/* Prepare output based on the MessageType */
						switch($mtype) {
							case "FCCGetDeliveryDetailsRequest":
								dump("L707 Received FCCGetDeliveryDetailsRequest...");
								$output = $this->get_deliverydetails_response(
									$injson, $this->prod, $this->reservation_id, $client);
								break;

							case "FCCTerminateFuelPointRequest":
								$pump_no = $payload->DeviceID;
								$trans_no = $payload->TransactionSeqNo;
								dump("Received FCCTerminateFuelPointRequest...");
								$output = $this->terminate_fuelpoint_response($injson);
								$ret1 = $this->sendEPS($output, $client);
								dump('terminate_fuelpoint_response: return from sendEPS: ret='.$ret1);

								// Send FCCDeliveryStateChangeEvent

								// Need to get the last transaction details from
								// getPumpStatus
								$pkt = $this->get_last_transaction_details($pump_no);
								// Fetch PTS data
								$type = $pkt['Type'];
								$data = $pkt['Data'];
								$pump = $data['Pump'];

								$nozzle_up = 0;
								$last_transaction = 0;
								$last_price = 0;
								$last_volume = 0;
								$last_amount = 0;

								if ($type == 'PumpIdleStatus') {
									$last_transaction = $data['LastTransaction'];
									$nozzle_up        = $data['NozzleUp'];
									$last_volume      = $data['LastVolume'];
									$last_price       = $data['LastPrice'];
									$last_amount      = $data['LastAmount'];
									$last_nozzle      = $data['LastNozzle'];
								}

								// Send FCCDeliveryCompleteEvent with the details
								// Using the $trans_no from the Terminate
								// Request is critical
								/*
								$output = $this->delivery_complete_event(
									$pump, $last_nozzle, $trans_no,
									$last_price, $last_volume, $last_amount);
								*/
								$output = $this->delivery_complete_event(
									$pump, $last_nozzle, $trans_no,
									$last_price, 0, 0);

								// So that we'll break out of delivery loop
								$out = -1;
								break;

							case "FCCFreeFuelPointRequest":
								$output = $this->free_fuelpoint_response($injson);
								break;

							/*
							case "FCCLockFuelSaleRequest":
								$output = $this->lock_fuelsale_response($injson);
								break;

							case "FCCClearFuelSaleRequest":
								$out1 = $this->clear_fuelsale_response($injson);
								$ret1 = $this->sendEPS($out1, $client);
								dump('clear_fuelsale_response: return from sendEPS: ret='.$ret1);

								// Have to clear reservation after clearing sale
								$output = $this->free_fuelpoint_response($injson);
								break;
							*/


							default:
								dump('recvEPS wa tak tau: mtype='.$mtype);
								$output = "wa tak tau";
						}

						// Send JSON message to EPS via socket
						$ret = $this->sendEPS($output, $client);
						dump('return from sendEPS: ret='.$ret);

						if ($mtype == "end") {
							socket_close($client);
						}
					}
				}

			} else {
				// Detected Nozzle Down event from pump!
				dump('***** NOZZLE DOWN is detected! *****');

				// Send FCCDeliveryComplete
				$out1 = $this->delivery_complete_event(
					$pump, $last_nozzle, $last_transaction,
					$last_price, $last_volume, $last_amount);
				$ret1 = $this->sendEPS($out1, $client);
				dump('recvEPS: delivery_complete_event from sendEPS: ret1='.$ret1);

				// Send FCCFuelPointStateChangeEvent
				$out2 = $this->fuelpoint_state_change_event(
					$last_transaction, $pump, "Idle", "None", $ogfuel_id, 0,
					'NozzleDown');

				$ret2 = $this->sendEPS($out2, $client);
				dump('recvEPS: fuelpoint_state_change_event from sendEPS: ret2='.$ret2);
				// Set $out = -1 to break out of the execution loop
				$out = -1;
			}

			default:
		}

		return $out;
	} 


	public function lock_fuelsale_response($injson) {
		dump('lock_fuelsale_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->lock_fuelsale_response($injson);

		//dump($response);

		return $response;
	}


	public function clear_fuelsale_response($injson) {
		dump('clear_fuelsale_response()');
		//dump($injson);

		$invfcc = new InvFcc();
        $response = $invfcc->clear_fuelsale_response($injson);

		//dump($response);

		return $response;
	}


	public function pts2_get_transaction_details($pump_no, $trans_no) {
		dump('pts2_get_transaction_details()');
		$invfcc = new InvFcc();
		$response = $invfcc->pts2_get_transaction_details($pump_no, $trans_no);

		return $response;
	}
}
