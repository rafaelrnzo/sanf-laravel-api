<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnAtEsignOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esign_otp', function (Blueprint $table) {
            $table->timestamp('cooldown_end_at')->nullable();
            $table->timestamp('suspend_end_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esign_otp', function (Blueprint $table) {
            $table->removeColumn('cooldown_end_at');
            $table->removeColumn('suspend_end_at');
        });
    }
}
