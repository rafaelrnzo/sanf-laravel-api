<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->unique();
            $table->unsignedBigInteger('user_auth_id');
            $table->unsignedBigInteger('disbursement_id')->index()->comment('spare_part_disbursement.id');
            $table->string('disbursement_xid', 32)->index()->comment('spare_part_disbursement.xid');
            $table->decimal('amount', 20, 2);
            $table->string('currency', 10)->default('IDR');
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED', 'EXPIRED', 'CANCELLED'])->default('PENDING');
            $table->enum('category', ['INSTALLMENT_BILL', 'DOWN_PAYMENT_BILL']);
            $table->jsonb('payment_detail');
            $table->dateTime('expired_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
