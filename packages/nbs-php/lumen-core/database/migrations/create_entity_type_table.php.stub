<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityTypeTable extends Migration
{
    public function up()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['entity_type'], function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->timestamp('updated_at');
        });
    }

    public function down()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::dropIfExists($tableNames['entity_type']);
    }
}
