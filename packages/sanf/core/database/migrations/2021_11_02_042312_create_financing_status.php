<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamp('updated_at')->nullable();
        });
        DB::table('financing_status')->insert([
            ['id' => '10', 'name' => 'Diproses', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '20', 'name' => 'Disetujui', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '30', 'name' => 'Ditolak', 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financing_status');
    }
}
