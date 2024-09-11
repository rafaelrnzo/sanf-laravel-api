<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Migrations\MigrationForeignKeyUtil;

class RemovePlafondDisbursementDependencyFk extends Migration
{
    protected $connection = ConnectionDB::PG_SQL_CMS;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('plafond_disbursement_document_draft', 'plafond_disbursement_id');
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('plafond_disbursement_invoice_draft', 'plafond_disbursement_id');
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('plafond_disbursement_invoice_photo_draft', 'plafond_disbursement_id');
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('PlafondPaymentInsDocumentTemplate', 'plafond_disbursement_id');
        MigrationForeignKeyUtil::make(ConnectionDB::PG_SQL_CMS)->removeForeignKeyIfExists('plafond_disbursement_allocation_draft', 'plafond_disbursement_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema = config('database.connections')[ConnectionDB::PG_SQL]['schema'];

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('plafond_disbursement_invoice_draft', function (Blueprint $table) use ($schema) {
            $table->foreign('plafond_disbursement_id')
                ->references('id')
                ->on("$schema.plafond_disbursement")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('plafond_disbursement_invoice_photo_draft', function (Blueprint $table) use ($schema) {
            $table->foreign('plafond_disbursement_id')
                ->references('id')
                ->on("$schema.plafond_disbursement")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('plafond_disbursement_allocation_draft', function (Blueprint $table) use ($schema) {
            $table->foreign('plafond_disbursement_id')
                ->references('id')
                ->on("$schema.plafond_disbursement")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('plafond_disbursement_document_draft', function (Blueprint $table) use ($schema) {
            $table->foreign('plafond_disbursement_id')
                ->references('id')
                ->on("$schema.plafond_disbursement")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::connection(ConnectionDB::PG_SQL_CMS)->table('PlafondPaymentInsDocumentTemplate', function (Blueprint $table) use ($schema) {
            $table->foreign('plafond_disbursement_id')
                ->references('id')
                ->on("$schema.plafond_disbursement")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
}
