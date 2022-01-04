<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('survey_id');
            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('image_files')->nullable();
            $table->text('image_path');
            $table->timestamps();

            $table->foreign('survey_id')
                ->references('id')
                ->on('survey')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_item');
    }
}
