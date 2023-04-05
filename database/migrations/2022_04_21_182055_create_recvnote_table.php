<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecvnoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recvnote', function (Blueprint $table) {
            $table->id();
			// new SystemID('recvnote')
            $table->string('systemid');
			// FK to product.id
            $table->integer('product_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->integer('price')->unsigned()->nullable();
            $table->integer('qty')->nullable();
            $table->integer('cost')->unsigned()->nullable();
            $table->integer('costvalue')->unsigned()->nullable();
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
        Schema::dropIfExists('recvnote');
    }
}
