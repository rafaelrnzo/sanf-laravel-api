<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdColumnAtEsignDocumentAssigneeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esign_document_assignee', function (Blueprint $table) {
            $table->tinyInteger('status_id')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esign_document_assignee', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
}
