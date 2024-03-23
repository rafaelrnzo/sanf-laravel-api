<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAskUsQuestionerTable extends Migration
{
    public function up()
    {
        Schema::create('ask_us_questioner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('topic_id');
            $table->string('topic', 50);
            $table->string('title', 100);
            $table->string('message', 500);
            $table->string('name');
            $table->string('phone_number', 20);
            $table->string('email');
            $table->string('contract_no', 50)->nullable();
            $table->json('images')->nullable();
            $table->string('contact_media', 20);
            $table->string('contact_time', 20);
            $table->boolean('is_followed_up')->default(false);
            $table->timestamps();
            $table->json('modified_by')->nullable();

            $table->foreign('topic_id')
                ->references('id')
                ->on('m_ask_us_topic')
                ->onDelete('RESTRICT');
        });

    }

    public function down()
    {
        Schema::dropIfExists('ask_us_questioner');
    }
}
