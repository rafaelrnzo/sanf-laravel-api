<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMAskUsTopicTable extends Migration
{

    public function up()
    {
        Schema::create('m_ask_us_topic', function (Blueprint $table) {
            $table->smallInteger('id')->primary()->unsigned();
            $table->string('name', 50);
            $table->timestamp('updated_at')->default((DB::raw('CURRENT_TIMESTAMP')));
        });

        DB::table('m_ask_us_topic')->insert([
            ['id' => '1', 'name' => 'Saran', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '2', 'name' => 'Kritik', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '3', 'name' => 'Keluhan', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => '4', 'name' => 'Pertanyaan', 'updated_at' => date('Y-m-d H:i:s')]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('m_ask_us_topic');
    }

}
