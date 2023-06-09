<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationproductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locationproduct', function (Blueprint $table) {
            $table->id();
            // FK to location.id
            $table->integer('location_id')->unsigned();
            // FK to product.id
            $table->integer('product_id')->unsigned();
            // FK to company.id; Need to track Franchisee's products
            $table->integer('franchisee_company_id')->unsigned()->nullable();
            // Need to make sure we support negative.
            // Float is for fuel products.
            $table->float('quantity');

			$table->integer('cost')->unsigned()->nullable();
			$table->bigInteger('costvalue')->nullable();
			$table->integer('value')->nullable();
			$table->integer('profitloss')->unsigned()->nullable();
			$table->integer('loyalty')->unsigned()->nullable();
			$table->integer('royalty')->unsigned()->nullable();

            // Need to keep track of damaged quantity
            $table->integer('damaged_quantity')->unsigned()->nullable();
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
        Schema::dropIfExists('locationproduct');
    }
}
