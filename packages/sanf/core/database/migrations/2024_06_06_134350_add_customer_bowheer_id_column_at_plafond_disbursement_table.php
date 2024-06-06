<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerBowheerIdColumnAtPlafondDisbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement', function (Blueprint $table) {
            $table->string('customer_bowheer_id')->index()->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plafond_disbursement', function (Blueprint $table) {
            $table->dropColumn('customer_bowheer_id');
            $table->dropColumn('deleted_at');
        });
    }
}
