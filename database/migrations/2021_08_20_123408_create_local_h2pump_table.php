<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalH2pumpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_h2pump', function (Blueprint $table) {
            $table->id();
            $table->string('systemid');
            // FK to opos_controller.id
            $table->integer('controller_id');
            $table->string('dispenser_serial_no')->nullable();
            // From PTS2 configuration
            $table->integer('pump_no')->unsigned();
            // FK to local_pts2_protocol.id
            $table->integer('pts2_protocol_id')->unsigned();
            $table->integer('baudrate')->unsigned();
            $table->integer('pump_port')->unsigned();
            $table->integer('comm_address')->unsigned();
            $table->boolean('delivered')->default(false);
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
        Schema::dropIfExists('local_h2pump');
    }
}
