<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvConnectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ev_connector', function (Blueprint $table) {
            $table->id();
            $table->integer('connector_no')->unsigned();
            $table->integer('connector_pk')->unsigned();
			// FK to ev_chargepoint.id
            $table->integer('chargepoint_id')->unsigned();
            $table->string('ocpp_version')->nullable();
            $table->string('rfid_tag')->nullable();
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
        Schema::dropIfExists('ev_connector');
    }
}
