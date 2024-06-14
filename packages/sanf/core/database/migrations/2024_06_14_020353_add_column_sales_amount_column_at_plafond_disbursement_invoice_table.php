<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSalesAmountColumnAtPlafondDisbursementInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement_invoice', function (Blueprint $table) {
            $table->double('sales_amount')->nullable()->index()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plafond_disbursement_invoice', function (Blueprint $table) {
            $table->dropColumn('sales_amount');
        });
    }
}
