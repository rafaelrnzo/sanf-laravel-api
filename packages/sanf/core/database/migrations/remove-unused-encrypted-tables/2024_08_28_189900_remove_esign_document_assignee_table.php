<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemoveEsignDocumentAssigneeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('esign_document_assignee');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('esign_document_assignee', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('email');
            $table->string('document_id');
            $table->string('document_sign_url')->nullable();
            $table->timestamps();
            $table->tinyInteger('status_id')->default(10);
            $table->string('reference_no')->nullable();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('esign_document_assignee_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('esign_document_assignee')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'email' => $encryption->decrypt($item->email),
                                'document_id' => $encryption->decrypt($item->document_id),
                                'document_sign_url' => $encryption->decrypt($item->document_sign_url),
                                'reference_no' => $encryption->decrypt($item->reference_no),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('esign_document_assignee');
    }
}
