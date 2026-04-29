<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationOtpEncryptedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_otp_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('email')->index();
            $table->string('code');
            $table->string('purpose')->default('registration');
            $table->timestamp('expired_at')->index();
            $table->timestamp('cooldown_end_at')->nullable();
            $table->timestamp('suspend_end_at')->nullable();
            $table->tinyInteger('send_attempt')->default(0);
            $table->tinyInteger('verify_attempt')->default(0);
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'purpose', 'is_used', 'expired_at'], 'idx_user_purpose_active_enc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_otp_encrypted');
    }
}
