<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPinColumnAtUserAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->string('pin')->nullable();
            $table->timestamp('pin_updated_at')->nullable();
            $table->string('reset_pin_code')->nullable();
            $table->timestamp('exp_reset_pin_at')->nullable();
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
            $table->dropColumn('pin');
            $table->dropColumn('pin_updated_at');
            $table->dropColumn('reset_pin_code');
            $table->dropColumn('exp_reset_pin_at');
        });
    }
}
