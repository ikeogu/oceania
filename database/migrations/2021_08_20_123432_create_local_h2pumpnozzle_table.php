<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalH2pumpnozzleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_h2pumpnozzle', function (Blueprint $table) {
            $table->id();
            // FK to og_pump.id
            $table->integer('pump_id');
            // From PTS2 configuration
            $table->integer('nozzle_no');
            // FK to prd_ogfuel.id, but prd_ogfuel.product_id = product.id
            // This prd_ogfuel should have been synchronized with cloud
            $table->integer('h2fuel_id');
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
        Schema::dropIfExists('local_h2pumpnozzle');
    }
}
