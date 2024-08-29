<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sanf\Core\Constants\ConnectionDB;

class CreatePlafondDisbursementEncryptedTable extends Migration
{
    protected $connection = ConnectionDB::PG_SODIUM;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->create('plafond_disbursement_encrypted', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('xid', 32)->index();
            $table->string('client_id')->index()->nullable();
            $table->binary('client_name')->index()->nullable();
            $table->binary('client_mail')->index()->nullable();
            $table->string('customer_id')->index()->nullable();
            $table->binary('customer_mail')->index()->nullable();
            $table->string('customer_code')->index()->nullable();
            $table->boolean('customer_review');
            $table->tinyInteger('status_id')->index()->unsigned();
            $table->string('status', 64);
            $table->double('client_amount')->index()->unsigned();
            $table->double('customer_amount')->index()->unsigned();
            $table->double('admin_amount')->index()->unsigned();
            $table->string('plafond_submission_xid', 32)->index();
            $table->timestamps();
            $table->timestamp('customer_updated_at')->nullable();
            $table->timestamp('admin_updated_at')->nullable();
            $table->integer('version')->index();
            $table->string('disbursement_no')->index();
            $table->string('plafond_id', 64)->index();
            $table->binary('customer_name')->index()->nullable();
            $table->string('customer_bowheer_id')->index()->nullable();
            $table->softDeletes();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->binary('nonce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConnectionDB::PG_SODIUM)->dropIfExists('plafond_disbursement_encrypted');
    }
}
