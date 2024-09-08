<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateEsignDocumentEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('esign_document_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid')->nullable();
            $table->string('document_id')->unique();
            $table->binary('document_name')->nullable();
            $table->binary('document_file')->nullable()->comment('json');
            $table->timestamp('expired_at');
            $table->tinyInteger('status_id');
            $table->integer('version');
            $table->timestamps();
            $table->binary('modified_by')->nullable()->comment('json');
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
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('esign_document_encrypted');
    }
}
