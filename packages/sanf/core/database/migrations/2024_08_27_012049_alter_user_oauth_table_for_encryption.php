<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterUserOauthTableForEncryption extends Migration
{
    protected $tableName = 'user_oauth';
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
        $this->util->changeTypeToText($this->tableName, 'avatar');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'name');
        $this->util->changeComment($this->tableName, 'name', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'avatar');
        $this->util->changeComment($this->tableName, 'avatar', 'max length: 255');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'name', 255);
        $this->util->removeComment($this->tableName, 'name');

        $this->util->changeTypeToVarchar($this->tableName, 'avatar', 255);
        $this->util->removeComment($this->tableName, 'avatar');
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
                        'avatar' => $encryption->encrypt($item->avatar),
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
                        'avatar' => $encryption->decryptFromHex($item->avatar),
                    ]);
            }
        });
    }
}
