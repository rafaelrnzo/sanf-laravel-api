<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('recap_id_b2b', 32)->unique();
            $table->string('cust_id', 64)->index();
            $table->string('no_plafond', 64)->index();
            $table->string('p_code', 16)->index();
            $table->string('supplier_id', 64)->index();
            $table->string('supplier_name')->nullable();
            $table->unsignedInteger('total_invoice');
            $table->decimal('total_amount', 20, 2);
            $table->string('currency', 8)->default('IDR');
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('tenor');
            $table->string('tenor_type', 32)->nullable();
            $table->string('payment_method', 32)->nullable();
            $table->string('state', 64)->index();
            $table->string('state_code', 8)->index();
            $table->string('source_channel', 64)->nullable();
            $table->boolean('agreement_checkbox')->default(false);
            $table->string('request_id', 64)->nullable()->index();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_applications');
    }
}
