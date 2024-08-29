<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class EncryptProjectData extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection(ConnectionDB::PG_SQL)->table('project')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::connection(ConnectionDB::PG_SODIUM)->table('project_encrypted')
                    ->insert(
                        array_merge(
                            (array) $item,
                            [
                                'location_metadata' => $encryption->encrypt($item->location_metadata),
                                'phone_number' => $encryption->encrypt($item->phone_number),
                                'whatsapp_number' => $encryption->encrypt($item->whatsapp_number),
                                'business_email' => $encryption->encrypt($item->business_email),
                                'modified_by' => $encryption->encrypt($item->modified_by),
                                'city_name' => $encryption->encrypt($item->city_name),
                                'province_name' => $encryption->encrypt($item->province_name),
                                'nonce' => $encryption->nonce()->getNonceHex(),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SODIUM)->rearrangeSequence('project_encrypted');
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
