<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvssiDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invssi_discount', function (Blueprint $table) {
            $table->id();
			// FK to a invssi_saleitems.id
			$table->integer('item_id')->unsigned();

			$table->integer('discounTransactionId')->unsigned()->nullable();
			$table->string('discountName')->nullable();
			$table->string('discountToken')->nullable();
			$table->string('discountType')->nullable();
			$table->integer('discountValue')->unsigned()->nullable();
			$table->float('discountAmount')->unsigned()->nullable();
			$table->float('discountQty')->unsigned()->nullable();
			$table->string('promotionId')->nullable();
			$table->string('redemptionId')->nullable();
			$table->boolean('applyDiscount')->nullable();
			$table->boolean('processedOffline')->nullable();

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
        Schema::dropIfExists('invssi_discount');
    }
}
