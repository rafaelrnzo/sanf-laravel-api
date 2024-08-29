<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;

class EncryptCacheData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('cache')->orderBy('expiration')->chunk(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('cache_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'key' => $encryption->encrypt($item->key),
                                'key_hash' => $encryption->hash($item->key),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });
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
