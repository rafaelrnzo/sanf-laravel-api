<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptUserAdinsData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('user_adins')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('user_adins_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'email' => $encryption->encrypt($item->email),
                                'msisdn' => $encryption->encrypt($item->msisdn),
                                'identity_no' => $encryption->encrypt($item->identity_no),
                                'full_name' => $encryption->encrypt($item->full_name),
                                'date_of_birth' => $encryption->encrypt($item->date_of_birth),
                                'place_of_birth' => $encryption->encrypt($item->place_of_birth),
                                'gender' => $encryption->encrypt($item->gender),
                                'address' => $encryption->encrypt($item->address),
                                'postal_code' => $encryption->encrypt($item->postal_code),
                                'province' => $encryption->encrypt($item->province),
                                'city' => $encryption->encrypt($item->city),
                                'district' => $encryption->encrypt($item->district),
                                'sub_district' => $encryption->encrypt($item->sub_district),
                                'selfie_file' => $encryption->encrypt($item->selfie_file),
                                'identity_file' => $encryption->encrypt($item->identity_file),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('user_adins_encrypted');
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
