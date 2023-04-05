<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvssiSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invssi_sale', function (Blueprint $table) {
            $table->id();

			/* Primary key for the INVSSI transactions. Below are the
			 * three link tables that uses the salesId as FK:
			 * invssi_saleitems
			 * invssi_loyaltydetails
			 * giftcardbalance */
            $table->integer('saleId')->unsigned();

            $table->integer('terminalId')->unsigned()->nullable();
            $table->string('mop')->nullable();
            $table->string('cardType')->nullable();
            $table->text('receipt')->nullable();
            $table->float('amount')->unsigned()->nullable();
            $table->float('originalAmount')->unsigned()->nullable();
            $table->float('cardBalance')->unsigned()->nullable();
            $table->string('loyaltyId')->nullable();
            $table->string('loyaltyAccountCode')->nullable();
            $table->float('pumpedBalance')->unsigned()->nullable();
            $table->float('pointsEarned')->unsigned()->nullable();
            $table->float('pointsRedeemed')->unsigned()->nullable();
            $table->float('pointsBalanced')->unsigned()->nullable();
            $table->string('mainStatus')->nullable();
            $table->string('subStatus')->nullable();
            $table->string('storeId')->nullable();
            $table->string('transactionTime')->nullable();
            $table->string('transactionType')->nullable();
            $table->integer('originalSaleId')->unsigned()->nullable();
            $table->string('uuid')->nullable();
            $table->integer('batchId')->unsigned()->nullable();
            $table->boolean('digitalReceipt')->nullable();

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
        Schema::dropIfExists('invssi_sale');
    }
}
