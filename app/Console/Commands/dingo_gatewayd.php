<?php

namespace App\Console\Commands;

use \Carbon\Carbon;
use App\Classes\Dingo;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Nelexa\Buffer\Buffer;
use Nelexa\Buffer\StringBuffer;
use Nelexa\Buffer\ResourceBuffer;

class dingo_gatewayd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dingo:gateway {--pump=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dingo FMS Gateway daemon';
    protected $version = '1.0.0';

    protected $inputev_dev = [
		 3 => '/dev/input/event11',
		 4 => '/dev/input/event12'];

	protected $serial_dev = [
		 3 => '/dev/serial/by-id/',
		 4 => '/dev/serial/by-id/usb-1a86_USB2.0-Ser_-if00-port0'];

	protected $transaction_no = 0;
	protected $init_totalizer = 0;
	protected $final_totalizer = 0;
	protected $start_time = null;
	protected $end_time = null;
	protected $filled = 0;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$scanned = null;
		$pump = $this->option('pump'); 
		if ($pump != 3 && $pump != 4) {
			dump('ERROR: pump = '.$pump.' is NOT supported!');
			return -1;
		}

		dump('Starting '.$this->description.' ('.$this->version.') '.
			'for Pump'.$pump);
		dump("    via ".$this->serial_dev[$pump].'...');

		// Instantiate new Dingo and PTS2 instances
		$dg = new Dingo();

		// Endless daemonic loop
		while (true) {
			// Scanning access code from RFID access card
			$scanned = $dg->poll_scanner_by_serialdev($pump, $this->serial_dev);
			Log::info('scanned = '.$scanned);

			// Validate access code against registered users
			if (!empty($scanned)) {
				if ($dg->validate_access_code($scanned)) {
					Log::info('YAY!!! '.$scanned.' IS GOOD!!');
				} else {
					Log::info('BOO!!! '.$scanned.' SUCKS!!');
					continue;
				}
			} else {
				Log::error('Error! Scanning failed! Found NULL!');
				continue;
			}

			/*
			// Get initial totals from Pump
			$resp = (object) $dg->pts2_pump_get_totals($pump);
			dump($resp);

			$i = 0;
			while (true) {
				$stat = (object) $dg->pts2_pump_get_status($pump);
				// Looking for PumpTotals packet
				if ($stat->response->Packets[0]->Type == 'PumpTotals') {
					Log::info('handle: stat = '.json_encode($stat));
					break;
				}
				sleep(1);
				$i++;

				// We give up after 20 tries! Normally less than 3 will do.
				if ($i > 20) {
					Log::error('handle: init_totalizer HIT over 20 cycle!!');
					break;		
				}
			}
			dump('PumpTotals count='.$i);
			dump($stat);
			$this->init_totalizer = $stat->response->Packets[0]->Data->Volume;

			$this->start_time = Carbon::now()->
				setTimezone('+8')->format('dMy H:i:s');

			dump('init_totalizer='.$this->init_totalizer);
			dump('start_time='.$this->start_time);
			*/

			// Now we can authorize pump for full tank
			$resp = (object) $dg->pts2_authorize_pump($pump);
			dump($resp);

			if ($resp->Packets[0]->Type == 'PumpAuthorizeConfirmation') {
				$stat = null; $xdet = null;
				$this->transaction_no = $resp->Packets[0]->Data->Transaction;		
				Log::info('handle: transaction_no = '.$this->transaction_no);
				Log::info('handle: resp = '.json_encode($resp));

				// Now we go into delivering loop
				while (true) {
					// Look out for PumpTotals for cycle end
					$stat = (object) $dg->pts2_pump_get_status($pump);
					Log::info('handle: stat = '.json_encode($stat));
					if ($stat->response->Packets[0]->Type == 'PumpTotals') {
						Log::info('handle: stat = '.json_encode($stat));
						break;

					} else {
						// Look out for Finished for cycle end
						$xdet = (object) $dg->pts2_pump_get_transaction_details(
							$pump, $this->transaction_no);
						Log::info('handle: xdet = '.json_encode($xdet));
						if ($xdet->response->Packets[0]->Data->State == 'Finished') {
							Log::info('handle: xdet = '.json_encode($xdet));
							break;
						}
					}
					sleep(1);
				}

				$this->start_time = $xdet->response->Packets[0]->Data->DateTimeStart;
				$this->end_time = $xdet->response->Packets[0]->Data->DateTime;

				if (!empty($stat->response->Packets[0]->Data->Volume)) {
					$this->final_totalizer =
						$stat->response->Packets[0]->Data->Volume;
				}

				dump('final_totalizer='.$this->final_totalizer);
				dump('start_time='.$this->start_time);
				dump('end_time='.$this->end_time);

				// Now to get the final filled amount
				$stat = (object) $dg->pts2_pump_get_status($pump);
				// Looking for PumpIdleStatus packet
				if ($stat->response->Packets[0]->Type == 'PumpIdleStatus') {
					Log::info('handle: stat = '.json_encode($stat));

					$this->filled = $stat->response->Packets[0]->Data->LastVolume;
					$this->init_totalizer = $this->final_totalizer - $this->filled;
				}

				dump('init_totalizer='.$this->init_totalizer);
				dump('filled ='.$this->filled);
			}

			// Something is horribly wrong!
			if ($scanned < 0) break;
		}

        return 0;
    }
}
