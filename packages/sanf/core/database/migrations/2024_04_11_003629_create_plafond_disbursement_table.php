<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondDisbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_disbursement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index()->nullable();
            $table->string('client_name')->index()->nullable();
            $table->string('client_mail')->index()->nullable();
            $table->string('customer_id')->index()->nullable();
            $table->string('customer_mail')->index()->nullable();
            $table->string('customer_code')->index()->nullable();
            $table->boolean('customer_review');
            $table->tinyInteger('status_id')->index()->unsigned();
            $table->string('status', 64);
            $table->double('client_amount')->index()->unsigned();
            $table->double('customer_amount')->index()->unsigned();
            $table->double('admin_amount')->index()->unsigned();
            $table->string('plafond_submission_xid', 32)->index();
            $table->timestamps();
            $table->timestamp('customer_updated_at')->nullable();
            $table->timestamp('admin_updated_at')->nullable();
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
        Schema::dropIfExists('plafond_disbursement');
    }
}
