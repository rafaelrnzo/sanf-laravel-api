<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPlafondTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_plafond_type', function (Blueprint $table) {
            $table->string('id', 8);
            $table->string('title', 64);
            $table->string('alias', 32);
            $table->timestamp('updated_at');
        });

        \DB::table('m_plafond_type')->insert([
            ['id' => '001', 'title' => 'Plafon Pembiayaan Unit', 'alias' => 'Unit', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '002', 'title' => 'Plafon Pembiayaan Spare Part', 'alias' => 'Sparepart', 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_plafond_type');
    }
}
