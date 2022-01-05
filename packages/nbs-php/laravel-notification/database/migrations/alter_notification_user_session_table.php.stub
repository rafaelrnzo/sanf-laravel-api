<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationUserSessionTable extends Migration
{
    public function up()
    {
        Schema::table('user_session', function (Blueprint $table) {
            $table->smallInteger('notification_channel_id')->nullable();
            $table->string('notification_token')->nullable();

            $table->foreign('notification_channel_id')
                ->references('id')
                ->on('user_session')
                ->onDelete('RESTRICT');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_session');
    }
}
