<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondDisbursementAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_disbursement_allocation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->unsignedBigInteger('plafond_disbursement_id')->index();
            $table->unsignedBigInteger('submission_id')->index();
            $table->string('bank_id')->index()->nullable();
            $table->string('owner')->index()->nullable();
            $table->string('provider')->index()->nullable();
            $table->string('account_no')->index()->nullable();
            $table->boolean('is_default');
            $table->double('amount')->index()->unsigned();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('plafond_disbursement_allocation');
    }
}
