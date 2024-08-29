<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptPaymentAccDocumentData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('payment_acc_document')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('payment_acc_document_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'company' => $encryption->encrypt($item->company),
                                'bowheer' => $encryption->encrypt($item->bowheer),
                                'bowheer_email' => $encryption->encrypt($item->bowheer_email),
                                'first_signer_company' => $encryption->encrypt($item->first_signer_company),
                                'first_signer_name' => $encryption->encrypt($item->first_signer_name),
                                'first_signer_position' => $encryption->encrypt($item->first_signer_position),
                                'second_signer_company' => $encryption->encrypt($item->second_signer_company),
                                'second_signer_name' => $encryption->encrypt($item->second_signer_name),
                                'second_signer_position' => $encryption->encrypt($item->second_signer_position),
                                'bowheer_address' => $encryption->encrypt($item->bowheer_address),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('payment_acc_document_encrypted');
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
