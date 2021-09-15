<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_location', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('administrative_area_id')->index();
            $table->string('name')->index();
            $table->bigInteger('parent_id')->nullable();
            $table->string('location_code', 50)->index();
            $table->string('postal_code', 50)->nullable()->index();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->smallInteger('level');
            $table->smallInteger('sort')->default(0);
            $table->json('metadata');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->json('modified_by');
            $table->bigInteger('version')->default(1);

            $table->foreign('administrative_area_id')
                ->references('id')
                ->on('m_administrative_area')
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
        Schema::dropIfExists('m_location');
    }
}
