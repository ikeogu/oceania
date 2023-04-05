<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenitemcostQtydistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/* Link table for openitem_cost, cstore_receipt and stockreport
		   This is for the Quantity Distribution function to link cost,
		   sales and stock movements */
        Schema::create('openitemcost_qtydist', function (Blueprint $table) {
            $table->id();
			// FK to openitem_cost.id
			$table->integer('openitemcost_id')->unsigned();
			// FK to cstore_receipt.id
			$table->integer('csreceipt_id')->unsigned();
			// FK to stockreport.id
			$table->integer('stockreport_id')->unsigned();
			$table->integer('qty_taken')->unsigned();
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
        Schema::dropIfExists('openitemcost_qtydist');
    }
}
