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
            $table->string('xid', 32);
            $table->string('name', 128);
            $table->smallInteger('level');
            $table->smallInteger('depth');
            $table->smallInteger('parent_id')->nullable();
            $table->smallInteger('postal_code')->nullable();
            $table->integer('sort')->default(1);
            $table->json('metadata');
            $table->timestampTz('created_at', 6)->nullable()->default((DB::raw('CURRENT_TIMESTAMP')));
            $table->timestampTz('updated_at', 6)->nullable()->default((DB::raw('CURRENT_TIMESTAMP')));
            $table->json('modified_by');
            $table->bigInteger('version')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('m_location');
    }
}
