<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationproductCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locationproduct_cost', function (Blueprint $table) {
            $table->id();
			// FK to locprod_productledger.id
            $table->integer('locprodprodledger_id')->unsigned();
            $table->integer('cost')->unsigned()->nullable();
            $table->integer('qty_in')->nullable();
            $table->integer('qty_out')->nullable();
            $table->integer('balance')->nullable();

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
        Schema::dropIfExists('locationproduct_cost');
    }
}
