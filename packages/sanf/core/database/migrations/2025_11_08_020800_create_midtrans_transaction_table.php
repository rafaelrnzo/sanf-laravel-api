<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMidtransTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('midtrans_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('midtrans_order_id', 100)->unique();
            $table->string('midtrans_transaction_id', 100)->nullable();
            $table->string('midtrans_snap_token', 100)->nullable();
            $table->unsignedBigInteger('payment_id');
            $table->string('payment_xid', 32)->index();
            $table->decimal('gross_amount', 20, 2)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->string('transaction_status', 50)->nullable();
            $table->timestampTz('transaction_time')->nullable();
            $table->string('fraud_status', 50)->nullable();
            $table->jsonb('raw_response')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_id')
                ->references('id')
                ->on('payment')
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
        Schema::dropIfExists('midtrans_transaction');
    }
}
