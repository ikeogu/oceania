<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarparkOperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Transaction table to capture the operation and usage of the
        // car park lot
        Schema::create('carpark_oper', function (Blueprint $table) {
            $table->id();
            // FK to carparklot.id
            $table->integer('carparklot_id')->unsigned();
            // Timestamp IN
            $table->datetime('in')->nullable();
            // Timestamp OUT
            $table->datetime('out')->nullable();
            // Status of this car park lot
            $table->enum('status',
                ['active','inactive','unpaid','paid'])->default('inactive');
            // Transaction ID from Thunder
            $table->integer('transaction_id')->unsigned();
            // How much was this parking session?
            $table->integer('amount')->unsigned()->nullable();
            $table->integer('payment')->unsigned()->nullable();
            $table->integer('start_meter')->unsigned()->nullable();
            $table->integer('stop_meter')->unsigned()->nullable();
            $table->datetime('start_timestamp')->nullable();
            $table->datetime('stop_timestamp')->nullable();
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
        Schema::dropIfExists('carpark_oper');
    }
}
