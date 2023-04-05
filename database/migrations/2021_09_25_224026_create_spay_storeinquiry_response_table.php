<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpayStoreinquiryResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spay_storeinquiry_response', function (Blueprint $table) {
            $table->id();
            $table->boolean('ResStatus');
            $table->string('ResMsg' , 255);
            $table->string('ResCode' , 20);
            $table->string('merchantId' , 10);
            $table->string('merOrderNo' , 20);
            $table->string('orderNo' , 20);
            $table->double('orderAmt' , 18 , 2);
            $table->string('orderDate' , 14);
            $table->string('tranDate' , 14);
            $table->string('orderStatus' , 1);
            $table->text('sign');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('spay_storeinquiry_response');
    }
}
