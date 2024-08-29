<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateUserTekenajaEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('user_tekenaja_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->binary('email')->nullable();
            $table->binary('msisdn')->nullable();
            $table->binary('nik')->nullable();
            $table->binary('full_name')->nullable();
            $table->binary('dob')->nullable();
            $table->binary('pob')->nullable();
            $table->binary('gender')->nullable()->comment('tinyInteger');
            $table->binary('address')->nullable();
            $table->binary('postal_code')->nullable()->comment('integer');
            $table->tinyInteger('province_id')->nullable();
            $table->tinyInteger('district_id')->nullable();
            $table->tinyInteger('sub_district_id')->nullable();
            $table->binary('selfie_file')->nullable()->comment('json');
            $table->binary('identity_file')->nullable()->comment('json');
            $table->tinyInteger('status_id');
            $table->tinyInteger('total_submit_registration');
            $table->timestamps();
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
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('user_tekenaja_encrypted');
    }
}
