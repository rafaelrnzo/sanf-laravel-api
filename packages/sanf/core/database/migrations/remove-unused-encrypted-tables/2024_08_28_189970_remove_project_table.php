<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('project');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('project', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('xid', 21)->unique()->index();
            $table->string('title');
            $table->longText('description');
            $table->json('image_file')->nullable();
            $table->string('location_id');
            $table->json('location_metadata');
            $table->string('phone_number', 20);
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('business_email')->nullable();
            $table->timestamp('submission_limit_at');
            $table->timestamp('published_at')->nullable();
            $table->smallInteger('status_id');
            $table->timestamps();
            $table->json('modified_by')->nullable();

            $table->string('city_name')->nullable();
            $table->string('province_name')->nullable();
            $table->text('image_path')->nullable();

            $table->foreign('status_id')->references('id')
                ->on('project_status')
                ->onDelete('RESTRICT');
            $table->foreign('user_id')->references('id')
                ->on('user_auth')
                ->onDelete('RESTRICT');
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('project_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('project')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'location_metadata' => $encryption->decrypt($item->location_metadata),
                                'phone_number' => $encryption->decrypt($item->phone_number),
                                'whatsapp_number' => $encryption->decrypt($item->whatsapp_number),
                                'business_email' => $encryption->decrypt($item->business_email),
                                'modified_by' => $encryption->decrypt($item->modified_by),
                                'city_name' => $encryption->decrypt($item->city_name),
                                'province_name' => $encryption->decrypt($item->province_name),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('project');
    }
}
