<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAdinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_adins', function (Blueprint $table) {
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
        Schema::dropIfExists('user_adins');
    }
}
