<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondDisbursementInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_disbursement_invoice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->unsignedBigInteger('plafond_disbursement_id')->index();
            $table->unsignedBigInteger('submission_id')->index();
            $table->string('origin_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
            $table->string('document_no')->index();
            $table->string('document_date', 16)->index();
            $table->double('invoice_amount')->index()->unsigned();
            $table->double('tax_amount')->index()->unsigned();
            $table->double('vat_amount')->index()->unsigned();
            $table->double('backharge_amount')->index()->unsigned();
            $table->double('other_amount')->index()->unsigned();
            $table->double('total_amount')->index()->unsigned();
            $table->tinyInteger('order_no')->index();
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
        Schema::dropIfExists('plafond_disbursement_invoice');
    }
}
