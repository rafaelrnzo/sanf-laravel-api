<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSanfAccountToPlafondDisbursementAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafond_disbursement_allocation', function (Blueprint $table) {
            $table->boolean('is_sanf_account')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plafond_disbursement_allocation', function (Blueprint $table) {
            $table->dropColumn(['is_sanf_account']);
        });
    }
}
