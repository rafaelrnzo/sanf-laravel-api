<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentAccDocNoDateToPlafondDisbursementSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement_submission', function (Blueprint $table) {
            $table->string('payment_acc_doc_no')->nullable();
            $table->date('payment_acc_doc_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plafond_disbursement_submission', function (Blueprint $table) {
            $table->dropColumn(['payment_acc_doc_no', 'payment_acc_doc_date']);
        });
    }
}
