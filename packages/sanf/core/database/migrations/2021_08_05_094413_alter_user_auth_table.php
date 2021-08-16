<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->string('xid')->index()->unique()->nullable();
            $table->string('profile_type',1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn('xid');
            $table->dropColumn('profile_type');
        });
    }
}
