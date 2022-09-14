<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTekenajaNikEmailColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tekenaja', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('nik')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tekenaja', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->change();
            $table->string('nik')->nullable()->unique()->change();
        });
    }
}
