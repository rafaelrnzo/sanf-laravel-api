<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafondTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafond_type', function (Blueprint $table) {
            $table->string('id', 8)->index()->unique();
            $table->string('title', 64);
            $table->string('name', 32);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        \DB::table('plafond_type')->insert([
            ['id' => '001', 'title' => 'Plafon Pembiayaan Unit', 'name' => 'UNIT', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '002', 'title' => 'Plafon Pembiayaan Spare Part', 'name' => 'SPAREPART', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plafond_type');
    }
}
