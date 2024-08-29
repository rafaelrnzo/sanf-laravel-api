<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;

class RemoveCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('cache');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('cache', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('cache_encrypted')->orderBy('expiration')->chunk(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce'], $itemArr['key_hash']);

                DB::connection(ConnectionDB::PG_SQL)->table('cache')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'key' => $encryption->decrypt($item->key),
                            ]
                        )
                    );
            }
        });
    }
}
