<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvssiSaleitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invssi_saleitems', function (Blueprint $table) {
            $table->id();
			// Primary key for this sale item [THIS DOES NOT EXIST]
			//$table->integer('itemId')->unsigned();

			// FK to invssi_sale.saleId
			$table->integer('saleId')->unsigned();
			$table->float('amount')->unsigned()->nullable();
			$table->string('productId')->nullable();
			$table->integer('pumpId')->unsigned()->nullable();
			$table->float('quantity')->unsigned()->nullable();
			$table->float('unitPrice')->unsigned()->nullable();
			$table->float('taxAmount')->unsigned()->nullable();
			$table->float('originalAmount')->unsigned()->nullable();

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
        Schema::dropIfExists('invssi_saleitems');
    }
}
