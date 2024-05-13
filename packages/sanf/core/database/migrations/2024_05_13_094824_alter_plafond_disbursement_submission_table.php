<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPlafondDisbursementSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement_submission', function (Blueprint $table) {
            $table->string('payment_ins_web_doc_origin_name')->nullable();
            $table->string('payment_ins_web_doc_file_name')->nullable();
            $table->string('payment_ins_web_doc_path')->nullable();
            $table->json('payment_ins_web_doc_metadata')->nullable();
            $table->string('payment_acc_web_doc_origin_name')->nullable();
            $table->string('payment_acc_web_doc_file_name')->nullable();
            $table->string('payment_acc_web_doc_path')->nullable();
            $table->json('payment_acc_web_doc_metadata')->nullable();
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
            //
        });
    }
}
