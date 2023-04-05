<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpayStoreinquiryRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
		Note:
		i.   Merchant system only need to pass in either one of the option
		     fields: merOrderNo or orderNo.
		ii.  Transaction timeout is set at 2 minutes. Merchant is advice
		     to check transaction status via this API if did not received
		     callback after transaction timeout.
		iii. The message is in JSON request and response format.
		*/

        Schema::create('spay_storeinquiry_request', function (Blueprint $table) {
            $table->id();
            $table->string('merchantId' , 10);
			// Any one of merOrderNo or orderNo will be accepted
            $table->string('merOrderNo' , 20)->nullable();
            $table->string('orderNo' , 20)->nullable();
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
        Schema::dropIfExists('spay_storeinquiry_request');
    }
}
