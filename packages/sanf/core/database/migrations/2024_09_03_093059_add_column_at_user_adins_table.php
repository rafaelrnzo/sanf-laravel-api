<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAtUserAdinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_adins', function (Blueprint $table) {
            $table->string('province_id')->nullable();
            $table->string('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_adins', function (Blueprint $table) {
            $table->dropColumn('province_id');
            $table->dropColumn('city_id');
        });
    }
}
