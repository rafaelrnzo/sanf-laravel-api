<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFinancingPrerequisites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financing_prerequisites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->bigInteger('level');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->json('modified_by');
            $table->bigInteger('version')->default(1);
        });

        DB::table('financing_prerequisites')->insert([
            [
                'id' => 1,
                'parent_id' => null,
                'title' => 'Dokumen Perorangan',
                'description' => 'Berikut dokumen yang harus dipersiapkan untuk pengajuan pembiayaan personal:',
                'level' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'title' => 'NPWP',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'title' => 'Kartu Keluarga',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'title' => 'Kartu Identitas Pasangan',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'title' => 'Akte Pisah Harta',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 6,
                'parent_id' => 1,
                'title' => 'Rekening Koran 3 s/d 6 bulan terakhir',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 7,
                'parent_id' => 1,
                'title' => 'Izin Kegiatan Usaha/Surat Perintah Kerja',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 8,
                'parent_id' => 1,
                'title' => 'Holdings Unit',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 9,
                'parent_id' => 1,
                'title' => 'Perjanjian Jual Beli/Surat Pemesanan Kendaraan',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 10,
                'parent_id' => 1,
                'title' => 'Laporan Produksi 3 s/d 6 bulan terakhir',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 11,
                'parent_id' => null,
                'title' => 'Dokumen PT',
                'level' => 1,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 12,
                'parent_id' => null,
                'title' => 'Dokumen CV',
                'level' => 1,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 13,
                'parent_id' => null,
                'title' => 'Dokumen Sesuai Bidang Usaha',
                'level' => 1,
                'description' => 'Berikut dokumen yang harus dipersiapkan untuk pengajuan
                pembiayaan sesuai dengan bidang usaha anda :',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 14,
                'parent_id' => 13,
                'title' => 'A.Pertambangan',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 15,
                'parent_id' => 14,
                'title' => 'IUP Eksplorasi',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 16,
                'parent_id' => 14,
                'title' => 'IUP Operasi Produksi',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 17,
                'parent_id' => 14,
                'title' => 'IUP Pengangkutan dan Penjualan',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 18,
                'parent_id' => 14,
                'title' => 'PKP2B',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 19,
                'parent_id' => 14,
                'title' => 'Kontrak Pengajuan dengan Buyer',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 20,
                'parent_id' => 14,
                'title' => 'Lain Lain',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 21,
                'parent_id' => 13,
                'title' => 'B.Kehutanan',
                'level' => 2,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
            ],
            [
                'id' => 22,
                'parent_id' => 21,
                'title' => 'RKT/BKT',
                'level' => 3,
                'description' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1
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
        Schema::dropIfExists('financing_prerequisites');
    }
}
