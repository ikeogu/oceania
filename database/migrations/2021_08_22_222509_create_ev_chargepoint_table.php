<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvChargepointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ev_chargepoint', function (Blueprint $table) {
            $table->id();
            $table->string('systemid');
			// We can store the external chargepoint_id here: eg "CP01"
            $table->string('name');
            $table->string('ocpp_protocol')->nullable();
            $table->string('vendor')->nullable();
            $table->string('chargepoint_serial_no')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('meter_type')->nullable();
            $table->string('meter_serial_no')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

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
        Schema::dropIfExists('ev_chargepoint');
    }
}
