<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingFacilityMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_facility_method', function (Blueprint $table) {
            $table->unsignedBigInteger('facility_id')->index();
            $table->unsignedBigInteger('method_id')->index();
            $table->timestamps();

            $table->unique(['facility_id', 'method_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financing_facility_method');
    }
}
