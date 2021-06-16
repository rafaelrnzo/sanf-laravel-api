<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
            $table->json('image')->nullable();
            $table->timestamps();
            $table->json('modified_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product');
    }
}
