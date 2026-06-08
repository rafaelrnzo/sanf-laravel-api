<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->string('bank_id', 64)->index();
            $table->string('owner')->nullable();
            $table->string('provider')->nullable();
            $table->string('account_number')->nullable();
            $table->decimal('total_amount', 20, 2);
            $table->string('is_default', 1)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_bank_accounts');
    }
}
