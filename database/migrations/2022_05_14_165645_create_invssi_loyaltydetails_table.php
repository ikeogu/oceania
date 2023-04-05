<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvssiLoyaltydetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invssi_loyaltydetails', function (Blueprint $table) {
            $table->id();
			// FK to invssi_sale.saleId
			$table->integer('saleId')->unsigned();
			$table->float('pointsBalance')->unsigned()->nullable();
			$table->float('issuedPoints')->unsigned()->nullable();
			$table->float('bonusPoints')->unsigned()->nullable();
			$table->string('cardNumber')->nullable();

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
        Schema::dropIfExists('invssi_loyaltydetails');
    }
}
