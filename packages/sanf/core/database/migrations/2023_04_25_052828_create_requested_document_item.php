<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestedDocumentItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requested_document_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('requested_document_id')->index();
            $table->string('document_id');
            $table->string('document_name');
            $table->json('document_file');
            $table->boolean('is_submitted');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requested_document_item');
    }
}
