<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelFulltankReceiptproductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuelfulltank_receiptproduct', function (Blueprint $table) {
            $table->id();
            // FK to fuel_fulltank_receipt.id
            $table->integer('fulltank_receipt_id')->unsigned();
            // FK to product.id
            $table->integer('product_id')->unsigned();

            /* This is a local copy of the product */
            $table->string('name'); // Name of product
            $table->decimal('quantity',8,4)->unsigned();
            $table->integer('price')->unsigned();
            $table->integer('discount_pct')->unsigned()->
                default(0)->nullable();
            $table->integer('discount')->unsigned()->
                default(0)->nullable();
            $table->integer('filled')->unsigned()->
                default(0)->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('receipt_id');
            
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
        Schema::dropIfExists('fuelfulltank_receiptproduct');
    }
}
