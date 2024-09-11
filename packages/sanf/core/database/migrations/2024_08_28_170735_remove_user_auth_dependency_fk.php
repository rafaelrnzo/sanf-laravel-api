<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Migrations\MigrationForeignKeyUtil;

class RemoveUserAuthDependencyFk extends Migration
{
    protected $connection = ConnectionDB::PG_SQL_CMS;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('UserAuth', 'api_user_auth_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema = config('database.connections')[ConnectionDB::PG_SQL]['schema'];

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('UserAuth', function (Blueprint $table) use ($schema) {
            $table->foreign('api_user_auth_id')
                ->references('id')
                ->on("$schema.user_auth")
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }
}
