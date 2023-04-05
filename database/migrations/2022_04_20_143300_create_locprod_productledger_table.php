<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocprodProductledgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locprod_productledger', function (Blueprint $table) {
            $table->id();
			// FK to stockreport.id
			$table->integer('stockreport_id')->unsigned()->nullable();
			// FK to cstore_receipt.id
			$table->integer('csreceipt_id')->unsigned()->nullable();
			$table->string('product_systemid');
			$table->integer('qty')->nullable();
			$table->integer('cost')->unsigned()->nullable();

			// Moved to locationproduct_cost
			/*
			$table->integer('cost')->unsigned()->nullable();
            $table->integer('qty_in')->nullable();
            $table->integer('qty_out')->nullable();
			$table->integer('balance')->nullable();
			*/

            $table->timestamp('last_update')->nullable();
			$table->enum('status',[
				'pending','active','confirmed','in_progress','cancelled','received'
			])->default('active');
			$table->enum('type',[
				'transfer','stockin','stockout','stocktake',
				'cforward','refundcp','daily_variance',
				'received','returned','cash_sales'
			])->nullable();
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
        Schema::dropIfExists('locprod_productledger');
    }
}
