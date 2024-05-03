<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondDisbursementDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_disbursement_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->unsignedBigInteger('plafond_disbursement_id')->index();
            $table->unsignedBigInteger('submission_id')->index();
            $table->string('origin_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
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
        Schema::dropIfExists('plafond_disbursement_document');
    }
}
