<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpayCreateorderRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spay_createorder_request', function (Blueprint $table) {
            $table->id();
            $table->string('merchantId' , 10);
            $table->string('qrCode' , 100);
            $table->string('curType' , 3);
            $table->string('notifyURL' , 255)->nullable();
            $table->string('merOrderNo' , 20);
            $table->double('orderAmt', 18, 2);
            $table->string('goodsName' , 255);
            $table->string('transactionType' , 1);
            $table->string('detailURL' , 255)->nullable();
            $table->string('remark' , 255)->nullable();
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
        Schema::dropIfExists('spay_createorder_request');
    }
}
