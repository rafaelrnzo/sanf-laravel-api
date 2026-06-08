<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingStateHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_state_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->string('from_state', 64)->nullable();
            $table->string('to_state', 64)->index();
            $table->string('action', 64)->index();
            $table->text('notes')->nullable();
            $table->string('actor_type', 32)->nullable();
            $table->string('actor_id', 64)->nullable();
            $table->string('actor_name')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_state_histories');
    }
}
