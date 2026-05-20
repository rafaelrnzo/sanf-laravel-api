<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeGpsAddressNonNullableOnSurveyItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('survey_item')
            ->whereNull('gps_address')
            ->update(['gps_address' => '']);

        Schema::table('survey_item', function (Blueprint $table) {
            $table->text('gps_address')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('survey_item', function (Blueprint $table) {
            $table->text('gps_address')->nullable()->change();
        });
    }
}

