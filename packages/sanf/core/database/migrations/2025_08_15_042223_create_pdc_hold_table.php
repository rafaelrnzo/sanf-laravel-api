<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @since CR2025
 */
class CreatePdcHoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdc_hold', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->unsignedBigInteger('user_id')->index()->comment('user_auth_encrypted.id');
            $table->string('customer_id')->index();
            $table->tinyInteger('type')->comment('1 = Multi Giro; 2 = Multi Contract; 3 = Resume');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->bigInteger('reason_id')->index()->nullable();
            $table->text('reason_value')->nullable();
            $table->smallInteger('status_id')->index()->comment('10 = Process; 20 = Approved; 30 = Rejected');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('reason_id')
                ->references('id')
                ->on('pdc_hold_reasons')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdc_hold');
    }
}
