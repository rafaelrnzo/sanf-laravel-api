<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPreviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_preview', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_profile_xid');
            $table->json('installments');
            $table->enum('platform', ['WEB_PARTNER', 'MOBILE']);
            $table->timestamps();

            $table->unique(['user_profile_xid', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_preview');
    }
}
