<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditactLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditact_ledger', function (Blueprint $table) {
            $table->id();
			// This can be either fuel_receipt_id or fuelfulltank_receipt_id
			$table->string('document_no');
			// FK to creditact
			$table->integer('creditact_id')->unsigned();
			$table->datetime('last_update');
			$table->integer('amount')->nullable();
			$table->enum('source',['fuel','fulltank','payment','refunded'])->
				default('fuel');
            $table->softDeletes();
            $table->timestamps();
            $table->engine = "ARIA";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creditact_ledger');
    }
}
