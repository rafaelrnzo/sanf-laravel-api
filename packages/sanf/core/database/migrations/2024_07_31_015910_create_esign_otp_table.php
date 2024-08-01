<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esign_otp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sanf_id')->index();
            $table->timestamp('expired_at');
            $table->string('code')->nullable();
            $table->string('reference_no')->index();
            $table->string('transaction_no')->nullable();
            $table->string('email');
            $table->string('msisdn');
            $table->tinyInteger('attempt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esign_otp');
    }
}
