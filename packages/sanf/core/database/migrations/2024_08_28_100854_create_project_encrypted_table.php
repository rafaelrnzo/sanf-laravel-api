<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreateProjectEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('project_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('xid', 21)->unique()->index();
            $table->string('title');
            $table->longText('description');
            $table->json('image_file')->nullable();
            $table->string('location_id');
            $table->binary('location_metadata')->comment('json');
            $table->binary('phone_number')->comment('max length: 20');
            $table->binary('whatsapp_number')->nullable()->comment('max length: 20');
            $table->binary('business_email')->nullable();
            $table->timestamp('submission_limit_at');
            $table->timestamp('published_at')->nullable();
            $table->smallInteger('status_id');
            $table->timestamps();
            $table->binary('modified_by')->nullable()->comment('json');

            $table->binary('city_name')->nullable();
            $table->binary('province_name')->nullable();
            $table->text('image_path')->nullable();

            $table->binary('nonce');

            $table->foreign('user_id')->references('id')
                ->on('user_auth_encrypted')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('project_encrypted');
    }
}
