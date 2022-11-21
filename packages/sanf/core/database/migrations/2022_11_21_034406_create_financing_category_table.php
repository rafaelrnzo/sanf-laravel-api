<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFinancingCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('button_label')->nullable();
            $table->timestamp('created_at');
        });

        DB::table('financing_category')->insertOrIgnore([
            ['id' => 1, 'xid' => 'jtl-CKbzYXXYollyRGekv', 'title' => 'Lorem ipsum', 'description' => '<h2>Lorem ipsum..</h2>', 'image_url' => 'https://via.placeholder.com/400x400.png?text=Image', 'button_label' => 'Lorem Ipsum', 'created_at' => '2022-01-01 00:00:00',],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financing_category');
    }
}
