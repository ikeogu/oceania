<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditedreportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditedreport', function (Blueprint $table) {
            $table->id();
            $table->string('systemid');
			// FK to product.id
            $table->integer('product_id')->unsigned();
            $table->integer('qty')->unsigned()->nullable();
            $table->integer('audited_qty')->unsigned()->nullable();
            $table->integer('difference')->nullable();

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
        Schema::dropIfExists('auditedreport');
    }
}
