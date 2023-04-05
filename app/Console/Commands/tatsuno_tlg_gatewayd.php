<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class tatsuno_tlg_gatewayd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tatsuno_tlg:gateway';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Tatsuno Tank Level Gauge Gateway daemon';

	/* Serial communications specs */
	private $serial_dev = '/dev/ttyS1';
	private $baud_rate = 19200;
	private $comms = '8e1';

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

		dump('Starting Tatsuno Tank Level Gauge Gateway ....');
		dump('Communicating at '.$serial_dev);

		while(true) {


		}
        return 0;
    }
}
