<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveUserAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('user_auth');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('user_auth', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('entity_type_id')->unsigned()->index();
            $table->string('username');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('full_name')->nullable();
            $table->string('landline_number', 20)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->tinyInteger('status_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->timestamps();

            $table->string('xid', 21)->index()->nullable();
            $table->string('personal_xid')->index()->nullable();
            $table->string('profile_type', 1)->nullable();
            $table->string('company_name')->nullable();

            $table->string('pin')->nullable();
            $table->timestamp('pin_updated_at')->nullable();
            $table->string('reset_pin_code')->nullable();
            $table->timestamp('reset_pin_expired_at')->nullable();

            $table->softDeletes();

            $table->foreign('entity_type_id')
                ->references('id')
                ->on('user_entity_type')
                ->onDelete('RESTRICT');
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('user_auth_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('user_auth')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'username' => $encryption->decrypt($item->username),
                                'full_name' => $encryption->decrypt($item->full_name),
                                'landline_number' => $encryption->decrypt($item->landline_number),
                                'phone_number' => $encryption->decrypt($item->phone_number),
                                'company_name' => $encryption->decrypt($item->company_name),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('user_auth');
    }
}
