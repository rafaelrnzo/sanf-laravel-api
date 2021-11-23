<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_application', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index()->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('profile_xid')->index();
            $table->json('profile_snapshot');
            $table->unsignedBigInteger('status_id')->index();
            $table->unsignedBigInteger('facility_id')->index();
            $table->unsignedBigInteger('method_id')->index();
            $table->unsignedBigInteger('type_id')->index()->comment('10: Personal, 20: Company');
            $table->string('application_code')->unique()->index()->comment('MMYY-XXXXXX (x = counter number)');
            $table->string('registration_code')->unique()->index()->nullable();
            $table->boolean('is_receive_offer');
            $table->text('project_location')->nullable();
            $table->json('segment');
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
        Schema::dropIfExists('financing_application');
    }
}
