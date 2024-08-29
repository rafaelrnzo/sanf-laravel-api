<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Migrations\MigrationPostgreSqlUtil;

class AlterFinancingUnitLocationSubmissionTableForEncryption extends Migration
{
    private $tableName = 'financing_unit_location_submission';
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
        $this->util->changeTypeToText($this->tableName, 'city_name');
        $this->util->changeTypeToText($this->tableName, 'submitted_location_metadata');
    }

    private function changeFieldsToBytea(): void
    {
        $this->util->changeTypeToBytea($this->tableName, 'city_name');
        $this->util->changeComment($this->tableName, 'city_name', 'max length: 255');

        $this->util->changeTypeToBytea($this->tableName, 'submitted_location_metadata');
        $this->util->changeComment($this->tableName, 'submitted_location_metadata', 'json');
    }

    private function changeBackFieldsType(): void
    {
        $this->util->changeTypeToVarchar($this->tableName, 'city_name');
        $this->util->removeComment($this->tableName, 'city_name');

        $this->util->changeTypeToJson($this->tableName, 'submitted_location_metadata');
        $this->util->removeComment($this->tableName, 'submitted_location_metadata');
    }

    private function encryptData(): void
    {
        DB::table($this->tableName)->chunkById(500, function ($data) {
            foreach ($data as $item) {
                $encryption = SodiumEncryption::encryptor();

                DB::table($this->tableName)
                    ->where('id', $item->id)
                    ->update([
                        'city_name' => $encryption->encrypt($item->city_name),
                        'submitted_location_metadata' => $encryption->encrypt($item->submitted_location_metadata),
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
                        'city_name' => $encryption->decryptFromHex($item->city_name),
                        'submitted_location_metadata' => $encryption->decryptFromHex($item->submitted_location_metadata),
                    ]);
            }
        });
    }
}
