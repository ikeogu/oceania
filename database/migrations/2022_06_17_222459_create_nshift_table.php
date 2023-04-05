<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNshiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nshift', function (Blueprint $table) {
            $table->id();
            $table->timestamp('in')->nullable();
            $table->timestamp('out')->nullable();
            // FK to users.systemid
            $table->string('staff_systemid')->nullable();
            $table->string('staff_name')->nullable();
            $table->integer('cash_in')->unsigned()->nullable();
            $table->integer('cash_out')->unsigned()->nullable();
            $table->integer('sales_drop')->unsigned()->nullable();
            $table->integer('actual')->unsigned()->nullable();
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
        Schema::dropIfExists('nshift');
    }
}
