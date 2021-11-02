<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingObject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_object', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->integer('amount')->index();
            $table->string('provider_name');
            $table->string('brand_id')->index();
            $table->string('brand_name');
            $table->string('type_id')->index();
            $table->string('type_name');
            $table->string('model_id')->index();
            $table->string('model_name')->index();
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
        Schema::dropIfExists('financing_object');
    }
}
