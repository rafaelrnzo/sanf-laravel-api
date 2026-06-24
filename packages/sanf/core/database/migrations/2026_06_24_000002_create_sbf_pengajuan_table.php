<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbfPengajuanTable extends Migration
{
    public function up()
    {
        Schema::create('sbf_pengajuan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_plafond', 50);
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('tenor');
            $table->unsignedInteger('total_invoice_count');
            $table->unsignedBigInteger('total_amount');
            $table->string('bank_id', 20);
            $table->string('bank_owner', 100);
            $table->string('bank_provider', 100);
            $table->string('bank_account_number', 50);
            $table->json('supplier_payload');
            $table->json('invoice_document');
            $table->json('spt_dokument');
            $table->json('supporting_dokuments')->nullable();
            $table->string('local_status', 20)->default('pending');
            $table->string('core_recap_id', 50)->nullable();
            $table->string('core_status', 20)->default('pending');
            $table->text('core_message')->nullable();
            $table->json('core_response')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['no_plafond', 'local_status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sbf_pengajuan');
    }
}
