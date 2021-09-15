<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAdministrativeAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_administrative_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->bigInteger('location_id')->index();
            $table->smallInteger('level')->index();
            $table->smallInteger('sort')->index();
            $table->json('metadata');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->json('modified_by');
            $table->bigInteger('version')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_administrative_area');
    }
}
