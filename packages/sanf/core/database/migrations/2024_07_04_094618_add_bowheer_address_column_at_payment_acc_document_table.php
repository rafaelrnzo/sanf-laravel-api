<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBowheerAddressColumnAtPaymentaccDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_acc_document', function (Blueprint $table) {
            $table->string('bowheer_address')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_acc_document', function (Blueprint $table) {
            $table->dropColumn('bowheer_address');
        });
    }
}
