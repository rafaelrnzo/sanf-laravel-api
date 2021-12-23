<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceClaimSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_claim_submission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('status_id')->index();
            $table->string('profile_xid')->index();
            $table->string('contract_no')->index();
            $table->string('serial_no');
            $table->string('polis_no');
            $table->string('brand_type_model');
            $table->string('year');
            $table->json('location_metadata');
            $table->string('city_id');
            $table->date('incident_date');
            $table->json('image_files');
            $table->text('image_path');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_claim_submission');
    }
}
