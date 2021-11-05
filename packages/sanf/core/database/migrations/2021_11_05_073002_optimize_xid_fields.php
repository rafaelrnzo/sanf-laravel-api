<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptimizeXidFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->string('xid', 21)->index()->change();
        });
        Schema::table('news', function (Blueprint $table) {
            $table->string('xid', 21)->index()->unique()->change();
        });
        Schema::table('commodity', function (Blueprint $table) {
            $table->string('xid', 21)->change();
        });
        Schema::table('promo_sanf', function (Blueprint $table) {
            $table->string('xid', 21)->index()->unique()->change();
        });
        Schema::table('project', function (Blueprint $table) {
            $table->string('xid', 21)->index()->change();
        });
        Schema::table('promo_astra', function (Blueprint $table) {
            $table->string('xid', 21)->index()->unique()->change();
        });
        Schema::table('financing_application', function (Blueprint $table) {
            $table->string('xid', 21)->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
