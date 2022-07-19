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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('msisdn')->nullable();
            $table->string('nik')->nullable()->unique();
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
