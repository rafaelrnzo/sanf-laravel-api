<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->string('recap_id', 32)->nullable()->index();
            $table->string('invoice_number', 128)->index();
            $table->date('invoice_date');
            $table->string('currency', 8)->default('IDR');
            $table->decimal('amount', 20, 2);
            $table->string('document_id', 128)->nullable();
            $table->string('invoice_status', 64)->index();
            $table->unsignedInteger('order_no')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_invoices');
    }
}
