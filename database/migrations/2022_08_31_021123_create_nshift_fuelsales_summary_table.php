<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNshiftFuelsalesSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nshift_fuelsales_summary', function (Blueprint $table) {
            $table->id();
			$table->integer('shift_no');
			$table->datetime('shift_in');
			$table->datetime('shift_out');
			$table->string('staff_id_systemid');
			$table->string('staff_name');
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
        Schema::dropIfExists('nshift_fuelsales_summary');
    }
}
