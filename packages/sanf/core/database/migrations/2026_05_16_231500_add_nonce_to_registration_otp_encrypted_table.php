<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNonceToRegistrationOtpEncryptedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_otp_encrypted', function (Blueprint $table) {
            $table->binary('nonce')->nullable();
        });

        DB::statement('TRUNCATE TABLE registration_otp_encrypted RESTART IDENTITY CASCADE');
        DB::statement('ALTER TABLE registration_otp_encrypted ALTER COLUMN email TYPE bytea USING NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registration_otp_encrypted', function (Blueprint $table) {
            $table->dropColumn('nonce');
        });
        
        DB::statement('ALTER TABLE registration_otp_encrypted ALTER COLUMN email TYPE varchar(255) USING email::text');
    }
}
