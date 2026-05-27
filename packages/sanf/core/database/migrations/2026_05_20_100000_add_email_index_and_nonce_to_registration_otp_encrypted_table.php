<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;

class AddEmailIndexAndNonceToRegistrationOtpEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->table('registration_otp_encrypted', function (Blueprint $table) {
            if (!Schema::connection($this->connection)->hasColumn('registration_otp_encrypted', 'nonce')) {
                $table->binary('nonce')->nullable();
            }
            if (!Schema::connection($this->connection)->hasColumn('registration_otp_encrypted', 'email_index')) {
                $table->binary('email_index')->nullable()->index();
            }
        });

        DB::connection($this->connection)->statement('TRUNCATE TABLE registration_otp_encrypted RESTART IDENTITY CASCADE');
        DB::connection($this->connection)->statement('ALTER TABLE registration_otp_encrypted ALTER COLUMN email TYPE bytea USING NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->table('registration_otp_encrypted', function (Blueprint $table) {
            if (Schema::connection($this->connection)->hasColumn('registration_otp_encrypted', 'email_index')) {
                $table->dropColumn('email_index');
            }
            if (Schema::connection($this->connection)->hasColumn('registration_otp_encrypted', 'nonce')) {
                $table->dropColumn('nonce');
            }
        });

        DB::connection($this->connection)->statement('ALTER TABLE registration_otp_encrypted ALTER COLUMN email TYPE varchar(255) USING email::text');
    }
}
