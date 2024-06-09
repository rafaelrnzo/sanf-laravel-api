<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnAtFinancingApplicationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financing_application_history', function (Blueprint $table) {
            $table->unsignedBigInteger('method_id')->nullable()->index();
            $table->unsignedBigInteger('facility_id')->nullable()->index();
            $table->double('amount')->nullable()->index();
            $table->double('down_payment_amount')->nullable()->index();
            $table->double('down_payment_percentage')->nullable()->index();
            $table->double('tax_amount')->index()->unsigned();
            $table->double('vat_amount')->index()->unsigned();
            $table->double('backharge_amount')->index()->unsigned();
            $table->double('other_amount')->index()->unsigned();
            $table->double('total_amount')->index()->unsigned();
            $table->double('tenor')->nullable()->index();
            $table->json('request_snapshot')->nullable();
            $table->string('client')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financing_application_history', function (Blueprint $table) {
            $table->dropColumn('method_id');
            $table->dropColumn('facility_id');
            $table->dropColumn('amount');
            $table->dropColumn('down_payment_amount');
            $table->dropColumn('down_payment_percentage');
            $table->dropColumn('tax_amount');
            $table->dropColumn('vat_amount');
            $table->dropColumn('backharge_amount');
            $table->dropColumn('other_amount');
            $table->dropColumn('total_amount');
            $table->dropColumn('tenor');
            $table->dropColumn('request_snapshot');
            $table->dropColumn('client');
        });
    }
}
