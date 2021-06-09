<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicePlatformTable extends Migration
{
    public function up()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['device_platform'], function (Blueprint $table) use ($tableNames) {
            $table->smallIncrements('id');
            $table->string('name', 64);
            $table->timestamp('updated_at');
        });

        DB::table($tableNames['device_platform'])->insertOrIgnore([
            [
                'id' => '10',
                'name' => 'Mobile Android',
                'updated_at' => '2020-01-01 00:00:00',
            ],
            [
                'id' => '20',
                'name' => 'Mobile iOS',
                'updated_at' => '2020-01-01 00:00:00',
            ],
            [
                'id' => '30',
                'name' => 'Web Browser',
                'updated_at' => '2020-01-01 00:00:00',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::dropIfExists($tableNames['device_platform']);
    }
}
