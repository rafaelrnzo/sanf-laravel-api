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
            $table->string('personal_xid')->index()->unique()->nullable();
            $table->string('profile_type',1)->nullable();
            $table->string('company_name')->nullable();
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
            $table->dropColumn('personal_xid');
            $table->dropColumn('profile_type');
            $table->dropColumn('company_name');
        });
    }
}
