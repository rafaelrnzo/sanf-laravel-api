<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateMLocationEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('m_location_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32);
            $table->binary('name')->comment('max length: 128');
            $table->smallInteger('level');
            $table->smallInteger('depth');
            $table->smallInteger('parent_id')->nullable();
            $table->smallInteger('postal_code')->nullable();
            $table->integer('sort')->default(1);
            $table->binary('metadata')->comment('json');
            $table->timestampTz('created_at', 6)->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestampTz('updated_at', 6)->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->binary('modified_by')->comment('json');
            $table->bigInteger('version')->unsigned()->default(0);
            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('m_location_encrypted');
    }
}
