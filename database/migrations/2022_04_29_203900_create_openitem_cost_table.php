<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenitemCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openitem_cost', function (Blueprint $table) {
            $table->id();
			// FK to openitem_productledger.id
            $table->integer('openitemprodledger_id')->unsigned();
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
        Schema::dropIfExists('openitem_cost');
    }
}
