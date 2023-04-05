<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePshiftfuelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pshiftfuel', function (Blueprint $table) {
            $table->id();
            // FK to pshift.id
            $table->integer('pshift_id')->unsigned();
            // FK to prd_ogfuel.id
            $table->integer('ogfuel_id')->unsigned();
            $table->integer('sales')->unsigned()->nullable();
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
        Schema::dropIfExists('pshiftfuel');
    }
}
