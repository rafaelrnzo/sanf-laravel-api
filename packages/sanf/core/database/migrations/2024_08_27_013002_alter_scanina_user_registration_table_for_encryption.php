<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterScaninaUserRegistrationTableForEncryption extends Migration
{
    protected $tableName = 'scanina_user_registration';
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
        $this->util->changeTypeToText($this->tableName, 'email');
        $this->util->changeTypeToText($this->tableName, 'snapshot_request_body');
        $this->util->changeTypeToText($this->tableName, 'snapshot_response_body');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'email');
        $this->util->changeComment($this->tableName, 'email', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'snapshot_request_body');
        $this->util->changeComment($this->tableName, 'snapshot_request_body', 'jsonb');

        $this->util->changeTypeToBytea($this->tableName, 'snapshot_response_body');
        $this->util->changeComment($this->tableName, 'snapshot_response_body', 'jsonb');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'email', 255);
        $this->util->removeComment($this->tableName, 'email');

        $this->util->changeTypeToJsonb($this->tableName, 'snapshot_request_body');
        $this->util->removeComment($this->tableName, 'snapshot_request_body');

        $this->util->changeTypeToJsonb($this->tableName, 'snapshot_response_body');
        $this->util->removeComment($this->tableName, 'snapshot_response_body');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'email' => $encryption->encrypt($item->email),
                        'snapshot_request_body' => $encryption->encrypt($item->snapshot_request_body),
                        'snapshot_response_body' => $encryption->encrypt($item->snapshot_response_body),
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
                        'email' => $encryption->decryptFromHex($item->email),
                        'snapshot_request_body' => $encryption->decryptFromHex($item->snapshot_request_body),
                        'snapshot_response_body' => $encryption->decryptFromHex($item->snapshot_response_body),
                    ]);
            }
        });
    }
}
