<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptUserAuthData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('user_auth')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('user_auth_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'username' => $encryption->encrypt($item->username),
                                'full_name' => $encryption->encrypt($item->full_name),
                                'landline_number' => $encryption->encrypt($item->landline_number),
                                'phone_number' => $encryption->encrypt($item->phone_number),
                                'company_name' => $encryption->encrypt($item->company_name),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('user_auth_encrypted');
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
