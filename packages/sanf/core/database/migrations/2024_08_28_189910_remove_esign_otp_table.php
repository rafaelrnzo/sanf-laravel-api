<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveEsignOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('esign_otp');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('esign_otp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sanf_id')->index();
            $table->timestamp('expired_at');
            $table->string('code')->nullable();
            $table->string('reference_no')->index();
            $table->string('transaction_no')->nullable();
            $table->string('email');
            $table->string('msisdn');
            $table->tinyInteger('attempt');
            $table->timestamps();
            $table->timestamp('cooldown_end_at')->nullable();
            $table->timestamp('suspend_end_at')->nullable();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('esign_otp_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('esign_otp')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'reference_no' => $encryption->decrypt($item->reference_no),
                                'email' => $encryption->decrypt($item->email),
                                'msisdn' => $encryption->decrypt($item->msisdn),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('esign_otp');
    }
}
