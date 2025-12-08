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
            $table->unsignedBigInteger('disbursement_id')->index()->comment('spare_part_disbursement.id');
            $table->string('disbursement_xid', 32)->index()->comment('spare_part_disbursement.xid');
            $table->dateTime('due_date');
            $table->decimal('amount', 20, 2);
            $table->enum('status', ['ACTIVE', 'WAITING_PAYMENT', 'PAID'])->default('ACTIVE');
            $table->unsignedInteger('sequence_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['disbursement_id', 'sequence_number']);
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
