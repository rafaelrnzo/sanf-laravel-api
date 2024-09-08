<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveEsignDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('esign_document');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('esign_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid')->nullable();
            $table->string('document_id')->unique();
            $table->string('document_name')->nullable();
            $table->json('document_file')->nullable();
            $table->timestamp('expired_at');
            $table->tinyInteger('status_id');
            $table->integer('version');
            $table->timestamps();
            $table->json('modified_by')->nullable();
            $table->string('reference_no')->nullable();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('esign_document_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('esign_document')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'document_name' => $encryption->decrypt($item->document_name),
                                'document_file' => $encryption->decrypt($item->document_file),
                                'modified_by' => $encryption->decrypt($item->modified_by),
                                'reference_no' => $encryption->decrypt($item->reference_no),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('esign_document');
    }
}
