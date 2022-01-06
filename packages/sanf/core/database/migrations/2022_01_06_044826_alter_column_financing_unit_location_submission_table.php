<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnFinancingUnitLocationSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financing_unit_location_submission', function (Blueprint $table) {
            $table->string('city_id')->nullable();
            $table->string('city_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financing_unit_location_submission', function (Blueprint $table) {
            $table->dropColumn('city_id');
            $table->dropColumn('city_name');
        });
    }
}
