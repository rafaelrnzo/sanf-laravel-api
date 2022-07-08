<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTekenajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tekenaja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid');
            $table->string('registration_id');
            $table->string('email')->unique();
            $table->string('msisdn');
            $table->string('nik');
            $table->string('full_name');
            $table->string('dob');
            $table->string('pob');
            $table->tinyInteger('gender');
            $table->string('address')->nullable();
            $table->integer('postal_code')->nullable();
            $table->tinyInteger('province_id');
            $table->tinyInteger('district_id');
            $table->tinyInteger('subdistrict_id');
            $table->string('selfie_file');
            $table->string('identity_file');
            $table->tinyInteger('status_id');
            $table->tinyInteger('total_submit_registration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tekenaja');
    }
}
