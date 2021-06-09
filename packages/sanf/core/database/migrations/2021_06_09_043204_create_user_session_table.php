<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSessionTable extends Migration
{
    public function up()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['user_session'], function (Blueprint $table) use ($tableNames) {
            $table->bigInteger('id', true, true);
            $table->bigInteger('user_id')->unsigned();
            $table->smallInteger('auth_provider_id')->unsigned()->index();
            $table->smallInteger('device_platform_id')->unsigned()->index();
            $table->string('device_id', 64)->nullable();
            $table->string('device_manufacturer', 128)->nullable();
            $table->string('device_model', 128)->nullable();
            $table->text('device_user_agent')->nullable();
            $table->string('signature')->nullable();
            $table->timestamp('expired_at');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on($tableNames['user_auth'])
                ->onDelete('RESTRICT');

            $table->foreign('auth_provider_id')
                ->references('id')
                ->on($tableNames['auth_provider'])
                ->onDelete('RESTRICT');
        });
    }

    public function down()
    {
        $tableNames = config('auth.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/auth.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::dropIfExists($tableNames['user_session']);
    }
}
