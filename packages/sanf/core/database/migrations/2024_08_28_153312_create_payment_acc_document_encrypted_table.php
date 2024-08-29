<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreatePaymentAccDocumentEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('payment_acc_document_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index();
            $table->string('plafond_id', 64)->index();
            $table->binary('company')->index();
            $table->binary('bowheer')->index();
            $table->binary('bowheer_email')->index();
            $table->string('document_no')->index();
            $table->date('document_date')->index();
            $table->binary('first_signer_company')->index();
            $table->binary('first_signer_name')->index();
            $table->binary('first_signer_position')->index();
            $table->binary('second_signer_company')->index();
            $table->binary('second_signer_name')->index();
            $table->binary('second_signer_position')->index();
            $table->string('origin')->nullable();
            $table->string('filename')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->json('invoices')->nullable();
            $table->binary('bowheer_address')->index()->nullable();
            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('payment_acc_document_encrypted');
    }
}
