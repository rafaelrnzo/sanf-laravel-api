<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterFinancingApplicationTableForEncryption extends Migration
{
    private $tableName = 'financing_application';
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
        $this->util->changeTypeToText($this->tableName, 'profile_snapshot');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'profile_snapshot');
        $this->util->changeComment($this->tableName, 'profile_snapshot', 'json');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToJson($this->tableName, 'profile_snapshot');
        $this->util->removeComment($this->tableName, 'profile_snapshot');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'profile_snapshot' => $encryption->encrypt($item->profile_snapshot),
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
                        'profile_snapshot' => $encryption->decryptFromHex($item->profile_snapshot),
                    ]);
            }
        });
    }
}
