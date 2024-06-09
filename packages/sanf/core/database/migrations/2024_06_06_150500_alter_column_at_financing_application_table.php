<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnAtFinancingApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financing_application', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->json('segment')->nullable()->change();
            $table->tinyInteger('total_object')->nullable();
            $table->string('client')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financing_application', function (Blueprint $table) {
            $table->dropColumn('total_object');
            $table->dropColumn('client');
        });
    }
}
