<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterAskUsQuestionerTableForEncryption extends Migration
{
    private $tableName = 'ask_us_questioner';
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
        $this->util->changeTypeToText($this->tableName, 'name');
        $this->util->changeTypeToText($this->tableName, 'phone_number');
        $this->util->changeTypeToText($this->tableName, 'email');
        $this->util->changeTypeToText($this->tableName, 'modified_by');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'name');
        $this->util->changeComment($this->tableName, 'name', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'phone_number');
        $this->util->changeComment($this->tableName, 'phone_number', 'max length: 20');

        $this->util->changeTypeToBytea($this->tableName, 'email');
        $this->util->changeComment($this->tableName, 'email', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'modified_by');
        $this->util->changeComment($this->tableName, 'modified_by', 'json');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'name', 255);
        $this->util->removeComment($this->tableName, 'name');

        $this->util->changeTypeToVarchar($this->tableName, 'phone_number', 20);
        $this->util->removeComment($this->tableName, 'phone_number');

        $this->util->changeTypeToVarchar($this->tableName, 'email', 255);
        $this->util->removeComment($this->tableName, 'email');

        $this->util->changeTypeToJson($this->tableName, 'modified_by');
        $this->util->removeComment($this->tableName, 'modified_by');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'name' => $encryption->encrypt($item->name),
                        'phone_number' => $encryption->encrypt($item->phone_number),
                        'email' => $encryption->encrypt($item->email),
                        'modified_by' => $encryption->encrypt($item->modified_by),
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
                        'name' => $encryption->decryptFromHex($item->name),
                        'phone_number' => $encryption->decryptFromHex($item->phone_number),
                        'email' => $encryption->decryptFromHex($item->email),
                        'modified_by' => $encryption->decryptFromHex($item->modified_by),
                    ]);
            }
        });
    }
}
