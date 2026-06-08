<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->string('doc_id', 32)->index();
            $table->string('doc_desc')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('required')->default(false);
            $table->string('status', 64)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_documents');
    }
}
