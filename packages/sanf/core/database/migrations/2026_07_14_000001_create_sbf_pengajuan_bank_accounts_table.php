<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbfPengajuanBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('sbf_pengajuan_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengajuan_id');
            $table->string('bank_id', 20)->default('');
            $table->string('bank_owner', 100)->default('');
            $table->string('bank_provider', 100)->default('');
            $table->string('bank_account_number', 50)->default('');
            $table->string('source', 20)->default('webhook');
            $table->timestamps();

            $table->foreign('pengajuan_id')->references('id')->on('sbf_pengajuan')->onDelete('cascade');
            $table->unique(['pengajuan_id', 'bank_id', 'bank_account_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sbf_pengajuan_bank_accounts');
    }
}
