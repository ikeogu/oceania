<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvreceiptlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evreceiptlist', function (Blueprint $table) {
            $table->id();
			$table->timestamp('evreceipt_tstamp');
			$table->integer('evreceipt_id')->unsigned();
			$table->string('evreceipt_systemid');
			// FK to carparklot.id
			$table->integer('carpark_oper_id')->unsigned();
			// Hours that had been parked 
			$table->integer('total')->unsigned();
			// Store user_id of cashier
			$table->integer('staff_user_id')->unsigned()->nullable();
			// Whether user had clicked on blue crab button
			$table->enum('status', ['active','completed','refunded','voided'])
                ->default('active');
			$table->softDeletes();
            $table->timestamps();
            $table->engine = "Aria";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evreceiptlist');
    }
}
