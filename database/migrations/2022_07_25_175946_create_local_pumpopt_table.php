<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalPumpoptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_pumpopt', function (Blueprint $table) {
            $table->id();
			// FK to local_pump.id
            $table->integer('pump_id')->unsigned();
			// FK to invssi_sale.terminalId
            $table->integer('opt_terminal_id')->unsigned();
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
        Schema::dropIfExists('local_pumpopt');
    }
}
