<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class AddUsernameIndexToUserAuthEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection($this->connection)->hasColumn('user_auth_encrypted', 'username_index')) {
            Schema::connection($this->connection)->table('user_auth_encrypted', function (Blueprint $table) {
                $table->binary('username_index')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::connection($this->connection)->hasColumn('user_auth_encrypted', 'username_index')) {
            Schema::connection($this->connection)->table('user_auth_encrypted', function (Blueprint $table) {
                $table->dropColumn('username_index');
            });
        }
    }
}
