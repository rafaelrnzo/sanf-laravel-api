<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateEsignOtpEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('esign_otp_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sanf_id')->index();
            $table->timestamp('expired_at');
            $table->string('code')->nullable();
            $table->binary('reference_no')->index();
            $table->string('transaction_no')->nullable();
            $table->binary('email');
            $table->binary('msisdn');
            $table->tinyInteger('attempt');
            $table->timestamps();
            $table->timestamp('cooldown_end_at')->nullable();
            $table->timestamp('suspend_end_at')->nullable();
            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('esign_otp_encrypted');
    }
}
