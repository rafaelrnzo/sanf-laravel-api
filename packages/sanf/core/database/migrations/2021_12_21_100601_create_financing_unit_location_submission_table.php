<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingUnitLocationSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('financing_unit_location_submission', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('status_id')->index();
            //TODO HERE
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
        Schema::dropIfExists('financing_unit_location_submission');
    }
}
