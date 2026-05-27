<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGpsFieldsToSurveyItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('survey_item', function (Blueprint $table) {
            $table->decimal('gps_lat', 10, 7)->nullable()->after('image_path');
            $table->decimal('gps_lng', 10, 7)->nullable()->after('gps_lat');
            $table->text('gps_address')->nullable()->after('gps_lng');
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
            $table->dropColumn(['gps_lat', 'gps_lng', 'gps_address']);
        });
    }
}

