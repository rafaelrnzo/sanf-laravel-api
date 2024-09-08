<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveUserTekenajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('user_tekenaja');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('user_tekenaja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('nik')->nullable();
            $table->string('full_name')->nullable();
            $table->string('dob')->nullable();
            $table->string('pob')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->string('address')->nullable();
            $table->integer('postal_code')->nullable();
            $table->tinyInteger('province_id')->nullable();
            $table->tinyInteger('district_id')->nullable();
            $table->tinyInteger('sub_district_id')->nullable();
            $table->json('selfie_file')->nullable();
            $table->json('identity_file')->nullable();
            $table->tinyInteger('status_id');
            $table->tinyInteger('total_submit_registration');
            $table->timestamps();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('user_tekenaja_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('user_tekenaja')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'email' => $encryption->decrypt($item->email),
                                'msisdn' => $encryption->decrypt($item->msisdn),
                                'nik' => $encryption->decrypt($item->nik),
                                'full_name' => $encryption->decrypt($item->full_name),
                                'dob' => $encryption->decrypt($item->dob),
                                'pob' => $encryption->decrypt($item->pob),
                                'gender' => $encryption->decrypt($item->gender),
                                'address' => $encryption->decrypt($item->address),
                                'postal_code' => $encryption->decrypt($item->postal_code),
                                'selfie_file' => $encryption->decrypt($item->selfie_file),
                                'identity_file' => $encryption->decrypt($item->identity_file),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('user_tekenaja');
    }
}
