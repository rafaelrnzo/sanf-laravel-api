<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateUserAuthEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('user_auth_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('entity_type_id')->unsigned()->index()->comment('entity_type.id');
            $table->binary('username');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->binary('full_name')->nullable();
            $table->binary('landline_number')->nullable()->comment('max length: 20');
            $table->binary('phone_number')->nullable()->comment('max length: 20');
            $table->tinyInteger('status_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();
            $table->timestamps();

            $table->string('xid', 21)->index()->nullable();
            $table->string('personal_xid')->index()->nullable();
            $table->string('profile_type', 1)->nullable();
            $table->binary('company_name')->nullable();

            $table->string('pin')->nullable();
            $table->timestamp('pin_updated_at')->nullable();
            $table->string('reset_pin_code')->nullable();
            $table->timestamp('reset_pin_expired_at')->nullable();

            $table->softDeletes();

            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('user_auth_encrypted');
    }
}
