<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPdcHoldGirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdc_hold_giros', function (Blueprint $table) {
            $table->dropForeign(['pdc_hold_id']);

            $table->bigInteger('pdc_hold_id')->nullable()->change();

            $table->foreign('pdc_hold_id')
                ->references('id')
                ->on('pdc_hold')
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
        Schema::table('pdc_hold_giros', function (Blueprint $table) {
            $table->dropForeign(['pdc_hold_id']);

            $table->bigInteger('pdc_hold_id')->nullable(false)->change();

            $table->foreign('pdc_hold_id')
                ->references('id')
                ->on('pdc_hold')
                ->onDelete('RESTRICT');
        });
    }
}
