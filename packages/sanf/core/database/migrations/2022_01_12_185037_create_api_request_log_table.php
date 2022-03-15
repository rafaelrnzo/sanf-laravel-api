<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiRequestLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_request_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->string('request_id', '32')->nullable();
            $table->tinyInteger('status_code');
            $table->string('host');
            $table->string('method', 7);
            $table->text('path');
            $table->json('header')->nullable();
            $table->json('query')->nullable();
            $table->json('body')->nullable();
            $table->longText('response')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_request_log');
    }
}
