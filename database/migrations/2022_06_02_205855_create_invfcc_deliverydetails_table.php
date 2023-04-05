<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvfccDeliverydetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invfcc_deliverydetails', function (Blueprint $table) {
            $table->id();
			$table->integer('DeviceID')->unsigned()->nullable();
			$table->enum('DeliveryState',
				['New','Fueling','ReadyToPayOff','PaidOff','Dead'])->
				nullable();
			$table->integer('TransactionSeqNo')->unsigned()->nullable();
			$table->integer('ProductID')->unsigned()->nullable();
			$table->string('UnitPrice')->nullable();
			$table->string('Volume')->nullable();
			$table->string('Amount')->nullable();
			$table->enum('AuthorizationType', ['Prepay','Postpay'])->
				default('Prepay')->nullable();
			$table->boolean('Locked')->default(true);
			$table->string('PaymentData')->nullable();
			$table->enum('ConsentState',
				['Not_required','Approved','Declined','Pending','Undefined'])->
				default('Not_required')->nullable();
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
        Schema::dropIfExists('invfcc_deliverydetails');
    }
}
