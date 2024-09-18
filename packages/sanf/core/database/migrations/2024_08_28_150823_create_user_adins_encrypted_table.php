<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateUserAdinsEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('user_adins_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->unsignedBigInteger('user_id')->index();
            $table->string('sanf_id')->index();
            $table->binary('email');
            $table->binary('msisdn');
            $table->binary('identity_no');
            $table->binary('full_name');
            $table->binary('date_of_birth');
            $table->binary('place_of_birth');
            $table->binary('gender');
            $table->binary('address');
            $table->binary('postal_code')->comment('integer');
            $table->binary('province');
            $table->binary('city');
            $table->binary('district');
            $table->binary('sub_district');
            $table->binary('selfie_file')->comment('json');
            $table->binary('identity_file')->comment('json');
            $table->tinyInteger('status_id');
            $table->string('password');
            $table->string('transaction_no')->nullable();
            $table->timestamps();
            $table->binary('nonce');
            $table->string('province_id')->nullable();
            $table->string('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('user_adins_encrypted');
    }
}
