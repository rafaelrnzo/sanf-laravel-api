<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveUserAdinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('user_adins');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('user_adins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sanf_id')->index();
            $table->string('email');
            $table->string('msisdn');
            $table->string('identity_no');
            $table->string('full_name');
            $table->string('date_of_birth');
            $table->string('place_of_birth');
            $table->string('gender');
            $table->string('address');
            $table->integer('postal_code');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('sub_district');
            $table->json('selfie_file');
            $table->json('identity_file');
            $table->tinyInteger('status_id');
            $table->string('password');
            $table->string('transaction_no')->nullable();
            $table->timestamps();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('user_adins_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('user_adins')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'email' => $encryption->decrypt($item->email),
                                'msisdn' => $encryption->decrypt($item->msisdn),
                                'identity_no' => $encryption->decrypt($item->identity_no),
                                'full_name' => $encryption->decrypt($item->full_name),
                                'date_of_birth' => $encryption->decrypt($item->date_of_birth),
                                'place_of_birth' => $encryption->decrypt($item->place_of_birth),
                                'gender' => $encryption->decrypt($item->gender),
                                'address' => $encryption->decrypt($item->address),
                                'postal_code' => $encryption->decrypt($item->postal_code),
                                'province' => $encryption->decrypt($item->province),
                                'city' => $encryption->decrypt($item->city),
                                'district' => $encryption->decrypt($item->district),
                                'sub_district' => $encryption->decrypt($item->sub_district),
                                'selfie_file' => $encryption->decrypt($item->selfie_file),
                                'identity_file' => $encryption->decrypt($item->identity_file),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('user_adins');
    }
}
