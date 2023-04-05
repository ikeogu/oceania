<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilledrecoveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filled_recovery', function (Blueprint $table) {
            $table->id();

            $table->integer('pump_no')->unsigned();	// from blade UI
            $table->datetime('date'); // fuel_receipt.created_at
			// FK to fuel_receipt.id
            $table->integer('receipt_id')->unsigned();
			// Amount that had been authorized
            $table->integer('fuel')->unsigned()->nullable();	// from receipt

			// Difference between fuel and filled (money)
            $table->integer('refund_amt')->unsigned()->nullable();
			// Refunded qty in litres
            $table->float('refund_qty')->unsigned()->nullable();

			/*----------------- Fetched from PTS2 controller --------------*/
			// Amount that had been actually filed
            $table->integer('filled')->unsigned()->nullable();	// LastAmount
            $table->integer('price')->unsigned()->nullable();	// LastPrice
            $table->integer('trans_no')->unsigned()->nullable();// LastTransaction
            $table->integer('volume')->unsigned()->nullable();	// LastVolume
            $table->integer('nozzle')->unsigned()->nullable();	// LastNozzle
            $table->string('type')->nullable();					// type
			/*----------------- Fetched from PTS2 controller --------------*/

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
        Schema::dropIfExists('filled_recovery');
    }
}
