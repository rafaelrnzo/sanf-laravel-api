<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptUserTekenajaData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('user_tekenaja')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('user_tekenaja_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'email' => $encryption->encrypt($item->email),
                                'msisdn' => $encryption->encrypt($item->msisdn),
                                'nik' => $encryption->encrypt($item->nik),
                                'full_name' => $encryption->encrypt($item->full_name),
                                'dob' => $encryption->encrypt($item->dob),
                                'pob' => $encryption->encrypt($item->pob),
                                'gender' => $encryption->encrypt($item->gender),
                                'address' => $encryption->encrypt($item->address),
                                'postal_code' => $encryption->encrypt($item->postal_code),
                                'selfie_file' => $encryption->encrypt($item->selfie_file),
                                'identity_file' => $encryption->encrypt($item->identity_file),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('user_tekenaja_encrypted');
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
