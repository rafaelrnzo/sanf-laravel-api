<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterPlafondDisbursementAllocationTableForEncryption extends Migration
{
    protected $tableName = 'plafond_disbursement_allocation';
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
        $this->util->changeTypeToText($this->tableName, 'owner');
        $this->util->changeTypeToText($this->tableName, 'provider');
        $this->util->changeTypeToText($this->tableName, 'account_no');
        $this->util->changeTypeToText($this->tableName, 'notes');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'owner');
        $this->util->changeComment($this->tableName, 'owner', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'provider');
        $this->util->changeComment($this->tableName, 'provider', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'account_no');
        $this->util->changeComment($this->tableName, 'account_no', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'notes');
        $this->util->changeComment($this->tableName, 'notes', 'max length: 255');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'owner', 255);
        $this->util->removeComment($this->tableName, 'owner');

        $this->util->changeTypeToVarchar($this->tableName, 'provider', 255);
        $this->util->removeComment($this->tableName, 'provider');

        $this->util->changeTypeToVarchar($this->tableName, 'account_no', 255);
        $this->util->removeComment($this->tableName, 'account_no');

        $this->util->changeTypeToVarchar($this->tableName, 'notes', 255);
        $this->util->removeComment($this->tableName, 'notes');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'owner' => $encryption->encrypt($item->owner),
                        'provider' => $encryption->encrypt($item->provider),
                        'account_no' => $encryption->encrypt($item->account_no),
                        'notes' => $encryption->encrypt($item->notes),
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
                        'owner' => $encryption->decryptFromHex($item->owner),
                        'provider' => $encryption->decryptFromHex($item->provider),
                        'account_no' => $encryption->decryptFromHex($item->account_no),
                        'notes' => $encryption->decryptFromHex($item->notes),
                    ]);
            }
        });
    }
}
