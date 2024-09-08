<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class RemovePaymentAccDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SQL)->drop('payment_acc_document');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SQL)->create('payment_acc_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index();
            $table->string('plafond_id', 64)->index();
            $table->string('company')->index();
            $table->string('bowheer')->index();
            $table->string('bowheer_email')->index();
            $table->string('document_no')->index();
            $table->date('document_date')->index();
            $table->string('first_signer_company')->index();
            $table->string('first_signer_name')->index();
            $table->string('first_signer_position')->index();
            $table->string('second_signer_company')->index();
            $table->string('second_signer_name')->index();
            $table->string('second_signer_position')->index();
            $table->string('origin')->nullable();
            $table->string('filename')->nullable();
            $table->string('path')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->json('invoices')->nullable();
            $table->string('bowheer_address')->index()->nullable();
        });

        DB::connection(ConnectionDB::PG_SODIUM)->table('payment_acc_document_encrypted')->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                $itemArr = (array) $item;
                unset($itemArr['nonce']);

                DB::connection(ConnectionDB::PG_SQL)->table('payment_acc_document')
                    ->insert(
                        array_merge(
                            $itemArr,
                            [
                                'company' => $encryption->decrypt($item->company),
                                'bowheer' => $encryption->decrypt($item->bowheer),
                                'bowheer_email' => $encryption->decrypt($item->bowheer_email),
                                'first_signer_company' => $encryption->decrypt($item->first_signer_company),
                                'first_signer_name' => $encryption->decrypt($item->first_signer_name),
                                'first_signer_position' => $encryption->decrypt($item->first_signer_position),
                                'second_signer_company' => $encryption->decrypt($item->second_signer_company),
                                'second_signer_name' => $encryption->decrypt($item->second_signer_name),
                                'second_signer_position' => $encryption->decrypt($item->second_signer_position),
                                'bowheer_address' => $encryption->decrypt($item->bowheer_address),
                            ]
                        )
                    );
            }
        });

        MigrationPostgreSqlUtil::make(ConnectionDB::PG_SQL)->rearrangeSequence('payment_acc_document');
    }
}
