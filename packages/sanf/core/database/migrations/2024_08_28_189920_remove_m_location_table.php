<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveMLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('m_location');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('m_location', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->string('name', 128);
            $table->smallInteger('level');
            $table->smallInteger('depth');
            $table->smallInteger('parent_id')->nullable();
            $table->smallInteger('postal_code')->nullable();
            $table->integer('sort')->default(1);
            $table->json('metadata');
            $table->timestampTz('created_at', 6)->nullable()->default((DB::raw('CURRENT_TIMESTAMP')));
            $table->timestampTz('updated_at', 6)->nullable()->default((DB::raw('CURRENT_TIMESTAMP')));
            $table->json('modified_by');
            $table->bigInteger('version')->unsigned()->default(0);
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('m_location_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('m_location')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'name' => $encryption->decrypt($item->name),
                                'metadata' => $encryption->decrypt($item->metadata),
                                'modified_by' => $encryption->decrypt($item->modified_by),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('m_location');
    }
}
