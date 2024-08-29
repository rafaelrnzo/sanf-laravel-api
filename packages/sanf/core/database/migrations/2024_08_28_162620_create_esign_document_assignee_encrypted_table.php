<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateEsignDocumentAssigneeEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('esign_document_assignee_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->binary('email');
            $table->binary('document_id');
            $table->binary('document_sign_url')->nullable();
            $table->timestamps();
            $table->tinyInteger('status_id')->default(10);
            $table->binary('reference_no')->nullable();
            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('esign_document_assignee_encrypted');
    }
}
