<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInstallmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_installment', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('installment_id');
            $table->jsonb('installment_snapshot');
            $table->timestamps();

            $table->primary(['payment_id', 'installment_id']);

            $table->foreign('payment_id')
                ->references('id')
                ->on('payment')
                ->onDelete('cascade');
            $table->foreign('installment_id')
                ->references('id')
                ->on('installment')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_installment');
    }
}
