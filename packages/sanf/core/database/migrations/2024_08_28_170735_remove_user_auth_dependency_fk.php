<?php

use Illuminate\Database\Migrations\Migration;
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
        // TODO: recreate foreign key
    }
}
