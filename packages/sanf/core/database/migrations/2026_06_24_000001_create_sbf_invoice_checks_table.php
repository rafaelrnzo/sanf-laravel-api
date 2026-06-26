<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbfInvoiceChecksTable extends Migration
{
    public function up()
    {
        Schema::create('sbf_invoice_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cust_id', 50);
            $table->string('no_plafond', 50);
            $table->string('nomor_invoice', 100);
            $table->unsignedBigInteger('total_invoice');
            $table->string('core_status', 20)->default('pending');
            $table->text('core_message')->nullable();
            $table->json('core_response')->nullable();
            $table->timestamps();

            $table->index(['cust_id', 'no_plafond']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sbf_invoice_checks');
    }
}
