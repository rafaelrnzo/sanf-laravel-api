<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAskUsQuestionerTable extends Migration
{

    public function up()
    {
        Schema::create('ask_us_questioner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('topic_id');
            $table->string('title');
            $table->text('message');
            $table->string('name', 128);
            $table->string('msisdn', 32);
            $table->string('contract_no', 64);
            $table->json('images')->nullable();
            $table->string('contact_media', 8);
            $table->string('contact_time', 16);
            $table->timestamps();
            $table->json('modified_by');

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
