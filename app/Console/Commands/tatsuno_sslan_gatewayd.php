<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Nelexa\Buffer\Buffer;
use Nelexa\Buffer\StringBuffer;
use SSLAN_definition;

class tatsuno_sslan_gatewayd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tatsuno_sslan:gateway';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Tatsuno SSLAN RS485 Gateway daemon';

	/* Serial communications specs */
	private $serial_dev = '/dev/ttyS2';
	/* 19200, 8e1 */
	private $stty_cfg = "406:0:8be:8a30:3:1c:7f:15:4:2:64:0:11:13:1a:0:12:f:17:16:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0:0";
	private $pumps = ['42','43'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$serial_dev = $this->serial_dev;
		$stty_cfg = $this->stty_cfg;
		$stty_cmd = "stty -F $serial_dev $stty_cfg";
		exec($stty_cmd);

		dump('Starting Tatsuno Tank Level Gauge Gateway ....');
		dump('Communicating via '.$serial_dev);

		$fd = dio_open($serial_dev, O_RDWR | O_NOCTTY | O_NONBLOCK);

		while(true) {
			// Initialize state variables
			$c = null;
			$isEOT  = false; $isSTX  = false; $isACK  = false;
			$isACK0 = false; $isACK1 = false; 

			// Intialize a packet buffer
			$buf = new StringBuffer();

			// Getting the packets out
			while(true) {
				$isBreak = false;
				$raw = dio_read($fd, 1);
				if ($raw) {
					$v = unpack('H*', $raw);
					$c = $v[1];
					//echo $c.' ';

					// Looking for start character
					if ($c == EOT || $isEOT) {
						// EOT-SA-UA-ENQ
						$isEOT = true;  $isSTX  = false;
						$isACK = false; $isACK0 = false; $isACK1 = false;
						$buf->insert($c);

						switch($c) {
							case EOT:
								echo 'EOT ';
								break;

							case ENQ:
								$isEOT =false;
								echo "ENQ\n";
								$isBreak = true;
								break;

							default:
								echo $c.' ';
						}

					} else if ($c == STX || $isSTX) {
						// STX-SA-UA-Data-ETX-BCC
						$isSTX = true;  $isEOT  = false;
						$isACK = false; $isACK0 = false; $isACK1 = false;

						$buf->insert($c);

						echo 'STX ';

						if ($c == ETX) {
							$isSTX = false;
							break;
						}

					} else if ($c == ACK || $isACK) {
						$isACK = true; $isEOT = false; $isSTX = false;
						if ($c == ACK0) {
							$isACK = false; $isACK0 = true; $isACK1 = false;
							break;
						} else if ($c == ACK1) {
							$isACK = false; $isACK0 = false; $isACK1 = true;

						} else {
						}


						break;

					} else {
						$isEOT = false; $isSTX = false;
						break;
					}
				}

				if ($isBreak) break;
			}

			dump("  Message: ".$buf->toString());
		}

		dio_close($fd);
        return 0;
	}

	public function send_message($msg) {
	}
}
?>
