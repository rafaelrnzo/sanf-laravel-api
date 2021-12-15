<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepaymentSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepayment_submission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->unsignedBigInteger('status_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('profile_xid')->index();
            $table->string('contract_no')->index();
            $table->date('prepayment_date');
            $table->decimal('total_prepayment', 19, 4);
            $table->string('currency_type', 3);
            $table->json('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepayment_submission');
    }
}
