<?php

use Illuminate\Database\Migrations\Migration;
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
        // TODO: recreate foreign key
    }
}
