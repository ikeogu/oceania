<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class tune_db_type extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oi_prodledger:tune_db_type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Openitem ledger type to cash_sales if they have record in Cstore_receipt';

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
        $query = "
            SELECT
                opl.id
            FROM
                cstore_receiptproduct crp,
                cstore_receiptdetails crd,
                product p,
                openitem_productledger opl
            WHERE
                crd.receipt_id = crp.receipt_id AND
                crp.product_id = p.id AND
                opl.product_systemid = p.systemid  AND
                opl.type = 'stockout'
            GROUP BY
				opl.id
            ";

        $result = collect(DB::select(DB::raw($query)))->map(function($f) {
			//dump( $f->id);
			return $f->id;
		});

		$ids = array_values((array)$result)[0];

        $opl_id = implode(',',$ids);

        $query2 = "
            UPDATE
                openitem_productledger opl
            SET
                opl.type = 'cash_sales'
            WHERE
                opl.id IN ($opl_id)
            ;
        ";

        DB::update($query2);

    }
}
