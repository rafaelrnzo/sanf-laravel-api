<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepaymentSubmissionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepayment_submission_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('submission_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamp('created_at');
            $table->json('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepayment_submission_history');
    }
}
