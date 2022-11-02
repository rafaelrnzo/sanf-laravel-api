<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_content', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->string('heading')->nullable();
            $table->string('sub_heading')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_header')->nullable()->comment('image url');
            $table->string('image_footer')->nullable()->comment('image url');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('static_content');
    }
}
