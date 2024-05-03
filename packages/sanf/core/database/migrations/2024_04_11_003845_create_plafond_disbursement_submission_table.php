<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondDisbursementSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_disbursement_submission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->unsignedBigInteger('plafond_disbursement_id')->index();
            $table->double('client_amount')->index()->unsigned();
            $table->double('customer_amount')->index()->unsigned();
            $table->json('invoice_snapshot')->nullable();
            $table->json('allocation_snapshot')->nullable();
            $table->json('other_doc_snapshot')->nullable();
            $table->string('payment_acc_doc_origin_name')->nullable();
            $table->string('payment_acc_doc_file_name')->nullable();
            $table->string('payment_acc_doc_path')->nullable();
            $table->json('payment_acc_doc_metadata');
            $table->tinyInteger('status_id')->index()->unsigned();
            $table->string('status', 64);
            $table->timestamps();
            $table->integer('version')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plafond_disbursement_submission');
    }
}
