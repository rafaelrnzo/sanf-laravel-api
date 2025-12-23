<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->unique();
            $table->string('contract_no', 100)->index();
            $table->timestampTz('due_date')->index();
            $table->decimal('amount', 20, 2);
            $table->enum('status', ['ACTIVE', 'WAITING_PAYMENT', 'PAID'])->default('ACTIVE')->index();
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contract_no', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installment');
    }
}
