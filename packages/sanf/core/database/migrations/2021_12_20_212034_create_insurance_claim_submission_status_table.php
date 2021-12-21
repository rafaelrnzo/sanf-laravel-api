<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceClaimSubmissionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('insurance_claim_submission_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
        DB::table('insurance_claim_submission_status')->insert([
            ['id' => '10', 'name' => 'Diproses', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '20', 'name' => 'Disetujui', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '30', 'name' => 'Ditolak', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_claim_submission_status');
    }
}
