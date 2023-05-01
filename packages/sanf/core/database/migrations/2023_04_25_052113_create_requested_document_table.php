<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestedDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requested_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('request_no');
            $table->timestamp('request_at');
            $table->string('document_no');
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('total_item');
            $table->unsignedInteger('total_uploaded');
            $table->unsignedTinyInteger('status');
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
        Schema::dropIfExists('requested_document');
    }
}
