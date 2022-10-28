<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueColumnAtUserAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropUnique('user_auth_username_unique');
            $table->dropUnique('user_auth_username_entity_type_id_unique');
            $table->unique(['username', 'entity_type_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropUnique('user_auth_username_entity_type_id_status_id_unique');
            $table->unique(['username']);
            $table->unique(['username', 'entity_type_id']);
        });
    }
}
