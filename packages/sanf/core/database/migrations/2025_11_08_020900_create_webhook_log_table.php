<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->unique();
            $table->string('key', 30);
            $table->string('reference_id', 255);
            $table->jsonb('payload');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_log');
    }
}
