<?php

namespace App\Listeners;

use App\Events\PumpAuthorized;
use App\Http\Controllers\OposPetrolStationPumpController as OPSPC;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class SendPumpAuthorizedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PumpAuthorized $event)
    {
		$pump_no = $event->testAuth->pump_no;
		$dose = $event->testAuth->dose/100;

        Log::info('handle: pump_no = '.$pump_no);
        Log::info('handle: dose = '.$dose);

		/* Authorize pump with only $dose, to trigger product detection */
		$o = new OPSPC;
		$o->pumpAuthorize($pump_no, null, $dose, null);
    }
}
