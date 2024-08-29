<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterAuditHttpLogTableForEncryption extends Migration
{
    private $tableName = 'audit_http_log';
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
        $this->util->changeTypeToText($this->tableName, 'query');
        $this->util->changeTypeToText($this->tableName, 'body');
        $this->util->changeTypeToText($this->tableName, 'response');
        $this->util->changeTypeToText($this->tableName, 'ip_address');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'query');
        $this->util->changeComment($this->tableName, 'query', 'json');

        $this->util->changeTypeToBytea($this->tableName, 'body');
        $this->util->changeComment($this->tableName, 'body', 'json');

        $this->util->changeTypeToBytea($this->tableName, 'response');
        $this->util->changeComment($this->tableName, 'response', 'json');

        $this->util->changeTypeToBytea($this->tableName, 'ip_address');
        $this->util->changeComment($this->tableName, 'ip_address', 'inet');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToJson($this->tableName, 'query');
        $this->util->removeComment($this->tableName, 'query');

        $this->util->changeTypeToJson($this->tableName, 'body');
        $this->util->removeComment($this->tableName, 'body');

        $this->util->changeTypeToJson($this->tableName, 'response');
        $this->util->removeComment($this->tableName, 'response');

        $this->util->changeTypeToInet($this->tableName, 'ip_address');
        $this->util->removeComment($this->tableName, 'ip_address');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'query' => $encryption->encrypt($item->query),
                        'body' => $encryption->encrypt($item->body),
                        'response' => $encryption->encrypt($item->response),
                        'ip_address' => $encryption->encrypt($item->ip_address),
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
                        'query' => $encryption->decryptFromHex($item->query),
                        'body' => $encryption->decryptFromHex($item->body),
                        'response' => $encryption->decryptFromHex($item->response),
                        'ip_address' => $encryption->decryptFromHex($item->ip_address),
                    ]);
            }
        });
    }
}
