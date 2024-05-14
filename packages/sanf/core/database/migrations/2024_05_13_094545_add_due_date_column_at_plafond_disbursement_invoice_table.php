<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDueDateColumnAtPlafondDisbursementInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement_invoice', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('plafond_disbursement_invoice', function (Blueprint $table) {
            //
        });
    }
}
