<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAuthLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_auth_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->bigInteger('user_id')->unsigned();
            $table->string('email');
            $table->string('personal_xid')->index()->nullable();
            $table->string('notes')->nullable();
            $table->tinyInteger('status_id');
            $table->timestamp('restore_expired_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->json('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['xid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_auth_log');
    }
}
