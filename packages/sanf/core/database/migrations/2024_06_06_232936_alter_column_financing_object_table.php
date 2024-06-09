<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnFinancingObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financing_object', function (Blueprint $table) {
            $table->string('brand_id')->nullable()->change();
            $table->string('type_id')->nullable()->change();
            $table->string('model_id')->nullable()->change();
            $table->string('provider_name')->index()->change();
            $table->string('brand_name')->index()->change();
            $table->string('type_name')->index()->change();

            $table->string('category_name')->nullable()->index();
            $table->string('description')->nullable();
            $table->double('price_per_unit')->nullable()->index()->unsigned();
            $table->string('client')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financing_object', function (Blueprint $table) {
            $table->dropColumn('category_name');
            $table->dropColumn('description');
            $table->dropColumn('price_per_unit');
            $table->dropColumn('client');
        });
    }
}
