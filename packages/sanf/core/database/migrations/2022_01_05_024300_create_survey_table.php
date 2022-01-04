<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 21)->unique();
            $table->string('branch_id')->nullable();
            $table->string('contract_no')->nullable();
            $table->string('profile_xid')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_location')->nullable();
            $table->string('segment')->nullable();
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
        Schema::dropIfExists('survey');
    }
}
