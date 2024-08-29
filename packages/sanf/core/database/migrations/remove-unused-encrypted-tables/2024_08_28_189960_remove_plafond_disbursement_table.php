<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemovePlafondDisbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('plafond_disbursement');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('plafond_disbursement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index()->nullable();
            $table->string('client_name')->index()->nullable();
            $table->string('client_mail')->index()->nullable();
            $table->string('customer_id')->index()->nullable();
            $table->string('customer_mail')->index()->nullable();
            $table->string('customer_code')->index()->nullable();
            $table->boolean('customer_review');
            $table->tinyInteger('status_id')->index()->unsigned();
            $table->string('status', 64);
            $table->double('client_amount')->index()->unsigned();
            $table->double('customer_amount')->index()->unsigned();
            $table->double('admin_amount')->index()->unsigned();
            $table->string('plafond_submission_xid', 32)->index();
            $table->timestamps();
            $table->timestamp('customer_updated_at')->nullable();
            $table->timestamp('admin_updated_at')->nullable();
            $table->integer('version')->index();
            $table->string('disbursement_no')->index();
            $table->string('plafond_id', 64)->index();
            $table->string('customer_name')->index()->nullable();
            $table->string('customer_bowheer_id')->index()->nullable();
            $table->softDeletes();
            $table->bigInteger('user_id')->unsigned()->nullable();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('plafond_disbursement_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('plafond_disbursement')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'client_name' => $encryption->decrypt($item->client_name),
                                'client_mail' => $encryption->decrypt($item->client_mail),
                                'customer_mail' => $encryption->decrypt($item->customer_mail),
                                'customer_name' => $encryption->decrypt($item->customer_name),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('plafond_disbursement');
    }
}
