<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @since CR2025
 */
class CreatePdcHoldGirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdc_hold_giros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->bigInteger('pdc_hold_id')->index();
            $table->bigInteger('pdc_resume_id')->nullable()->index();
            $table->string('customer_id')->index();
            $table->string('contract_no')->index();
            $table->string('pdc_no')->index();
            $table->double('amount');
            $table->string('currency_type', 3);
            $table->date('giro_date');
            $table->string('pdc_type')->nullable();
            $table->timestamps();

            $table->foreign('pdc_hold_id')
                ->references('id')
                ->on('pdc_hold')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdc_hold_giros');
    }
}
