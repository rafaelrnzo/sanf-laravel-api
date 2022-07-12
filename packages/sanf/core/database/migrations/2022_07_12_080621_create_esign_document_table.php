<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esign_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('email');
            $table->string('document_id')->unique();
            $table->string('document_name')->nullable();
            $table->json('document_file')->nullable();
            $table->timestamp('expired_at');
            $table->tinyInteger('status_id');
            $table->integer('version');
            $table->timestamps();
            $table->json('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esign_document');
    }
}
