<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvssiGiftcardbalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invssi_giftcardbalance', function (Blueprint $table) {
            $table->id();
			// FK to invssi_sale.saleId
			$table->integer('saleId')->unsigned();
			$table->float('beforeTransBalance')->unsigned()->nullable();
			$table->float('afterTransBalance')->unsigned()->nullable();

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
        Schema::dropIfExists('invssi_giftcardbalance');
    }
}
