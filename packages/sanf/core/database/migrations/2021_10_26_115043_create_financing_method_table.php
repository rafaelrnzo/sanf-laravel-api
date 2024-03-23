<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFinancingMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_method', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('interest_rate', 5, 4);
            $table->decimal('priority', 5, 4)->index();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->json('modified_by');
            $table->bigInteger('version')->default(1);
        });

        DB::table('financing_method')->insert([
            [
                'id' => '1',
                'name' => 'Sewa Pembiayaan',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
            [
                'id' => '2',
                'name' => 'Pembelian dengan Pembayaran secara Angsuran',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
            [
                'id' => '3',
                'name' => 'Jual dan Sewa Balik',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
            [
                'id' => '4',
                'name' => 'Anjak Piutang dengan Pemberian Jaminan dari Penjual Piutang',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
            [
                'id' => '5',
                'name' => 'Fasilitas Modal Usaha',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
            [
                'id' => '6',
                'name' => 'Anjak Piutang Tanpa Pemberian Jaminan dari Penjual Piutang',
                'interest_rate' => 0.14,
                'priority' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financing_method');
    }
}
