<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptEsignDocumentData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('esign_document')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('esign_document_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'document_id' => $encryption->encrypt($item->document_id),
                                'document_name' => $encryption->encrypt($item->document_name),
                                'document_file' => $encryption->encrypt($item->document_file),
                                'modified_by' => $encryption->encrypt($item->modified_by),
                                'reference_no' => $encryption->encrypt($item->reference_no),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('esign_document_encrypted');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
