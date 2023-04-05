<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturningnoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returningnote', function (Blueprint $table) {
            $table->id();
			// new SystemID('returningnote')
            $table->string('systemid');
			// FK to product.id
            $table->integer('product_id')->nullable();
			// The qty which is being returned
            $table->integer('qty')->nullable();
			// Display the latest cost
            $table->integer('cost')->nullable();
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
        Schema::dropIfExists('returningnote');
    }
}
