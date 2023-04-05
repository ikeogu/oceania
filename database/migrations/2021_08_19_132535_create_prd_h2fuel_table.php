<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrdH2fuelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prd_h2fuel', function (Blueprint $table) {
            $table->id();
            // FK to product.id
            $table->integer('product_id')->unsigned();
            $table->integer('kg')->unsigned()->nullable();
            $table->integer('price')->unsigned()->nullable();
            $table->integer('upper_price')->unsigned()->nullable();
            $table->integer('lower_price')->unsigned()->nullable();
            $table->integer('wholesale_price')->unsigned()->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->integer('loyalty')->unsigned()->nullable();

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
        Schema::dropIfExists('prd_h2fuel');
    }
}
