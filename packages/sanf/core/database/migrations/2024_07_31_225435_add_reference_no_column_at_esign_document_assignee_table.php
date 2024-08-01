<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceNoColumnAtEsignDocumentAssigneeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $hasColumn = Schema::hasColumn('esign_document_assignee', 'reference_no');
        if ($hasColumn === false) {
            Schema::table('esign_document_assignee', function (Blueprint $table) {
                $table->string('reference_no')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $hasColumn = Schema::hasColumn('esign_document_assignee', 'reference_no');
        if ($hasColumn === true) {
            Schema::table('esign_document_assignee', function (Blueprint $table) {
                $table->dropColumn('reference_no');
            });
        }
    }
}
