<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveyDateToSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('survey', 'survey_date')) {
            Schema::table('survey', function (Blueprint $table) {
                $table->date('survey_date')->nullable()->after('segment');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('survey', 'survey_date')) {
            Schema::table('survey', function (Blueprint $table) {
                $table->dropColumn(['survey_date']);
            });
        }
    }
}
