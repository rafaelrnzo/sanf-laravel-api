<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandbyFinancingPlafondLocksTable extends Migration
{
    public function up()
    {
        Schema::create('standby_financing_plafond_locks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index();
            $table->string('no_plafond', 64)->index();
            $table->decimal('locked_amount', 20, 2);
            $table->string('lock_status', 32)->index();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standby_financing_plafond_locks');
    }
}
