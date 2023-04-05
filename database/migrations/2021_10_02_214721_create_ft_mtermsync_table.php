<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFtMtermsyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ft_mtermsync', function (Blueprint $table) {
            $table->id();
			// FK to master's terminal.id
            $table->integer('master_terminal_id')->unsigned();
			// FK to product.id
            $table->integer('product_id')->unsigned();
			// FK to receipt.id
            $table->integer('ft_receipt_id')->unsigned()->nullable();
            $table->integer('pump_no')->unsigned();
            $table->string('payment_status')->nullable();
            $table->float('dose')->unsigned();
            $table->float('price')->unsigned();
            $table->boolean('litre')->default(false);
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
        Schema::dropIfExists('ft_mtermsync');
    }
}
