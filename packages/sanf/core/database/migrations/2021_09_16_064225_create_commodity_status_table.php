<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommodityStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamp('updated_at')->nullable();
        });

        DB::table('commodity_status')->insert([
            ['id' => '10', 'name' => 'Menunggu Approval', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '20', 'name' => 'Ditolak', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '30', 'name' => 'Published', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '40', 'name' => 'Unpublished', 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_status');
    }
}
