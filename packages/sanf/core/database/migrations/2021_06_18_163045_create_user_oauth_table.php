<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_oauth', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('user_id')->index();
            $table->string('provider_id')->index();
            $table->string('provider')->index();
            $table->string('provider_token');
            $table->string('avatar')->nullable();
            $table->unique(['provider_id', 'provider']);
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
        Schema::dropIfExists('user_oauth');
    }
}
