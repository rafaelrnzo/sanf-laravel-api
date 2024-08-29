<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptPlafondDisbursementData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('plafond_disbursement')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('plafond_disbursement_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'client_name' => $encryption->encrypt($item->client_name),
                                'client_mail' => $encryption->encrypt($item->client_mail),
                                'customer_mail' => $encryption->encrypt($item->customer_mail),
                                'customer_name' => $encryption->encrypt($item->customer_name),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('plafond_disbursement_encrypted');
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
