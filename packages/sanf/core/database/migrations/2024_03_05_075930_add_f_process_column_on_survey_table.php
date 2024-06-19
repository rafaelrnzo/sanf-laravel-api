<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFProcessColumnOnSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('survey', 'f_process')) {
            Schema::table('survey', function (Blueprint $table) {
                $table->string('f_process', 1)->nullable();
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
        if (Schema::hasColumn('survey', 'f_process')) {
            Schema::table('survey', function (Blueprint $table) {
                $table->dropColumn('f_process');
            });
        }
    }
}
