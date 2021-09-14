<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('xid')->unique()->index();
            $table->string('title');
            $table->longText('description');
            $table->json('image_file');
            $table->string('location_id');
            $table->json('location_metadata');
            $table->string('phone_number', 20);
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('business_email')->nullable();
            $table->timestamp('submission_limit_at');
            $table->timestamp('published_at')->nullable();
            $table->smallInteger('status_id');
            $table->timestamps();
            $table->json('modified_by')->nullable();

            $table->foreign('status_id')->references('id')
                ->on('project_status')
                ->onDelete('RESTRICT');
            $table->foreign('user_id')->references('id')
                ->on('user_auth')
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
        Schema::dropIfExists('project');
    }
}
