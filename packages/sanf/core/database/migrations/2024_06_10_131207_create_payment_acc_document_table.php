<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentAccDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_acc_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index();
            $table->string('plafond_id', 64)->index();
            $table->string('company')->index();
            $table->string('bowheer')->index();
            $table->string('document_no')->index();
            $table->date('document_date')->index();
            $table->string('first_signer_company')->index();
            $table->string('first_signer_name')->index();
            $table->string('first_signer_position')->index();
            $table->string('second_signer_company')->index();
            $table->string('second_signer_name')->index();
            $table->string('second_signer_position')->index();
            $table->string('origin')->nullable();
            $table->string('filename')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
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
        Schema::dropIfExists('payment_acc_document');
    }
}
