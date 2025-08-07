<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @since CR2025
 */
class AddPicNamePhoneNumberToInsuranceClaimSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_claim_submission', function (Blueprint $table) {
            $table->binary('pic_name')->nullable();
            $table->binary('pic_phone_number')->nullable();
            $table->text('completeness_documents')->nullable()->comment('Comma separated value for document names');
            $table->text('completeness_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_claim_submission', function (Blueprint $table) {
            $table->dropColumn([
                'pic_name',
                'pic_phone_number',
                'completeness_documents',
                'completeness_note',
            ]);
        });
    }
}
