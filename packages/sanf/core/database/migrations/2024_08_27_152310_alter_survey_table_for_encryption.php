<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterSurveyTableForEncryption extends Migration
{
    protected $tableName = 'survey';
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
        $this->util->changeTypeToText($this->tableName, 'customer_name');
        $this->util->changeTypeToText($this->tableName, 'pic_name');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'customer_name');
        $this->util->changeComment($this->tableName, 'customer_name', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'pic_name');
        $this->util->changeComment($this->tableName, 'pic_name', 'max length: 255');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'customer_name', 255);
        $this->util->removeComment($this->tableName, 'customer_name');

        $this->util->changeTypeToVarchar($this->tableName, 'pic_name', 255);
        $this->util->removeComment($this->tableName, 'pic_name');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'customer_name' => $encryption->encrypt($item->customer_name),
                        'pic_name' => $encryption->encrypt($item->pic_name),
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
                        'customer_name' => $encryption->decryptFromHex($item->customer_name),
                        'pic_name' => $encryption->decryptFromHex($item->pic_name),
                    ]);
            }
        });
    }
}
