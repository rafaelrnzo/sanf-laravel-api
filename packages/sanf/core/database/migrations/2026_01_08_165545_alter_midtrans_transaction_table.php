<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMidtransTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('midtrans_transaction', function (Blueprint $table) {
            $table->binary('raw_payload')->nullable();
            $table->binary('nonce')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('midtrans_transaction', function (Blueprint $table) {
            $table->dropColumn(['raw_payload', 'nonce']);
        });
    }
}
