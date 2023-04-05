<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelsalesSummaryDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// Fuelsales summary detail by product
        Schema::create('fuelsales_summary_detail', function (Blueprint $table) {
            $table->id();
			// FK to fuelsales summary
			$table->integer('fuelsales_summary_id')->unsigned();
            $table->string('product_name')->nullable();
            $table->integer('qty')->unsigned()->nullable();
            $table->integer('sales')->nullable();
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
        Schema::dropIfExists('fuelsales_summary_detail');
    }
}
