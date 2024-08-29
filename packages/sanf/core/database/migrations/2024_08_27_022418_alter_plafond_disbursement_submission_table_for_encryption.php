<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterPlafondDisbursementSubmissionTableForEncryption extends Migration
{
    protected $tableName = 'plafond_disbursement_submission';
    protected $util;

    public function __construct()
    {
        $this->util = MigrationPostgreSqlUtil::make();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addNonce();

        $this->changeFieldsToText();

        $this->encryptData();

        $this->changeFieldsToBytea();

        $this->alterNonceNotNull();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->changeFieldsToText();

        $this->decryptData();

        $this->changeBackFieldsType();

        $this->removeNonce();
    }

    private function addNonce(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->binary('nonce')->nullable();
        });
    }

    private function alterNonceNotNull(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->binary('nonce')->nullable(false)->change();
        });
    }

    private function removeNonce(): void
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('nonce');
        });
    }

    private function changeFieldsToText(): void
    {
        $this->util->changeTypeToText($this->tableName, 'allocation_snapshot');
        $this->util->changeTypeToText($this->tableName, 'user_updated_by');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'allocation_snapshot');
        $this->util->changeComment($this->tableName, 'allocation_snapshot', 'json');

        $this->util->changeTypeToBytea($this->tableName, 'user_updated_by');
        $this->util->changeComment($this->tableName, 'user_updated_by', 'json');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToJson($this->tableName, 'allocation_snapshot');
        $this->util->removeComment($this->tableName, 'allocation_snapshot');

        $this->util->changeTypeToJson($this->tableName, 'user_updated_by');
        $this->util->removeComment($this->tableName, 'user_updated_by');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'allocation_snapshot' => $encryption->encrypt($item->allocation_snapshot),
                        'user_updated_by' => $encryption->encrypt($item->user_updated_by),
                        'nonce' => $encryption->nonce()->getNonceHex(),
                    ]);
            }
        });
    }

    private function decryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::decryptor($item->nonce);

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'allocation_snapshot' => $encryption->decryptFromHex($item->allocation_snapshot),
                        'user_updated_by' => $encryption->decryptFromHex($item->user_updated_by),
                    ]);
            }
        });
    }
}
