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
        Schema::create('financing_prerequisite', function (Blueprint $table) {
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

        $prerequisites = [
            [
                'title' => 'Dokumen Perorangan',
                'description' => 'Berikut dokumen yang harus dipersiapkan untuk pengajuan pembiayaan personal :',
                'items' => [
                    ['title' => 'Kartu Identitas'],
                    ['title' => 'NPWP'],
                    ['title' => 'Kartu Keluarga'],
                    ['title' => 'Kartu Identitas Pasangan'],
                    ['title' => 'Akta Pisah Harta'],
                    ['title' => 'Rekening Koran 3 s/d 6 bulan terakhir'],
                    ['title' => 'Izin Kegiatan Usaha/Surat Perintah Kerja'],
                    ['title' => 'Holding Unit(s)'],
                    ['title' => 'Perjanjian Jual Beli/Surat Pemesanan Kendaraan'],
                    ['title' => 'Laporan Produksi 3 s/d 6 bulan terakhir'],
                ],
            ],
            [
                'title' => 'Dokumen PT',
                'description' => 'Berikut dokumen yang harus dipersiapkan untuk pengajuan pembiayaan PT :',
                'items' => [
                    ['title' => 'NPWP Company (PT/CV/dll)'],
                    ['title' => 'NPWP Pemegang Saham'],
                    ['title' => 'Akta Pendirian + SK Pengesahan Akta'],
                    ['title' => 'Akta Akta Perubahan + SK Pengesahan Akta'],
                    ['title' => 'Kartu Identitas Pengurus'],
                    ['title' => 'NIB/TDP'],
                    ['title' => 'SKDP/SITU/ITU/HO/SIG/Izin Lokasi'],
                    ['title' => 'Akta Penyesuaian UU No 40/2007 + SK Pengesaha Akta'],
                    ['title' => 'Kartu Identitas Direksi & Dewan Komisaris'],
                    ['title' => 'Kartu Identitas Pemegang Saham (Pemegang Saham Individu)'],
                    ['title' => 'Rekening Koran 3 s/d 6 bulan terakhir'],
                    ['title' => 'Izin Kegiatan Usaha/Surat Perintah Kerja'],
                    ['title' => 'Holding Unit(s)'],
                    ['title' => 'Perjanjian Jual Beli/Surat Pemesanan Kendaraan'],
                    ['title' => 'Laporan Keuangan Audited / Non-Audited 2 tahun terakhir (Exposure >50M wajib audited)'],
                    ['title' => 'SIUP/IUT/SP BKPM/Izin Usaha/Izin lain'],
                    ['title' => 'Laporan Produksi 3 s/d 6 bulan terakhir'],
                ],
            ],
            [
                'title' => 'Dokumen CV',
                'description' => 'Berikut dokumen yang harus dipersiapkan untuk pengajuan pembiayaan CV :',
                'items' => [
                    ['title' => 'NPWP Company (PT/CV/dll)'],
                    ['title' => 'Akta Pendirian + SK Pengesahan Akta'],
                    ['title' => 'Akta Akta Perubahan + SK Pengesahan Akta'],
                    ['title' => 'Kartu Identitas Pengurus'],
                    ['title' => 'NIB/TDP'],
                    ['title' => 'SKDP/SITU/ITU/HO/SIG/Izin Lokasi'],
                    ['title' => 'Rekening Koran 3 s/d 6 bulan terakhir'],
                    ['title' => 'Izin Kegiatan Usaha/Surat Perintah Kerja'],
                    ['title' => 'Holding Unit(s)'],
                    ['title' => 'Perjanjian Jual Beli/Surat Pemesanan Kendaraan'],
                    ['title' => 'Laporan Keuangan Audited / Non-Audited 2 tahun terakhir (Exposure >50M wajib audited)'],
                    ['title' => 'SIUP/IUT/SP BKPM/Izin Usaha/Izin lain'],
                    ['title' => 'Laporan Produksi 3 s/d 6 bulan terakhir'],
                ],
            ],
            [
                'title' => 'Dokumen CV',
                'description' => 'Berikut dokumen yang harus di persiapkan untuk pengajuan pembiayaan sesuai dengan bidang usaha anda :',
                'items' => [
                    [
                        'title' => 'A. Pertambangan',
                        'items' => [
                            ['title' => 'IUP Eksplorasi'],
                            ['title' => 'IUP Operasi Produksi'],
                            ['title' => 'IUP Pengangkutan dan Penjualan'],
                            ['title' => 'Persetujuan Ekspor'],
                            ['title' => 'PKP2B'],
                            ['title' => 'Kontrak Penjualan dengan Buyer'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                    [
                        'title' => 'B. Kehutanan',
                        'items' => [
                            ['title' => 'RKT/BKT'],
                            ['title' => 'Izin Konsesi Logging'],
                            ['title' => 'IUPHHK'],
                            ['title' => 'Izin Pemanfaatan Kayu'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                    [
                        'title' => 'C. Perkebunan',
                        'items' => [
                            ['title' => 'Izin Lokasi'],
                            ['title' => 'IUP/ STD'],
                            ['title' => 'Izin Pinjam Pakai'],
                            ['title' => 'Hak Guna Usaha'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                    [
                        'title' => 'D. Trasportasi & Logistik',
                        'items' => [
                            ['title' => 'PJB/SPK Unit'],
                            ['title' => 'PJB/SPK Karoseri'],
                            ['title' => 'Rekening Koran 3 bulan terakhir (Untuk PO)'],
                            ['title' => 'Rekening Koran 6 Bulan Terakhir  (Untuk SPK)'],
                            ['title' => 'PO 6 Bulan Terakhir'],
                            ['title' => 'Izin Trayek (Khusus Bus)'],
                        ],
                    ],
                    [
                        'title' => 'E. Perindustrian',
                        'items' => [
                            ['title' => 'Izin Usaha Industri'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                    [
                        'title' => 'F. Konstruksi',
                        'items' => [
                            ['title' => 'Izin Usaha Jasa Konstruksi (IUJK)'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                    [
                        'title' => 'G. Mesin Printing',
                        'items' => [
                            ['title' => 'Bukti Bayar PPB 2 tahun terakhir'],
                            ['title' => 'Rekening Listrik 6 bulan terkhir'],
                            ['title' => 'Laporan Produksi/PO/Kontrak kerja 6 bulan terakhir'],
                            ['title' => 'Document Kepemilikan/Pengusaha'],
                            ['title' => 'Tempat Usaha'],
                            ['title' => 'Feasibility Study'],
                            ['title' => 'Holding Unit'],
                            ['title' => 'Perjanjian dengan supplier'],
                            ['title' => 'Riwayat Pembayaran Supplier'],
                            ['title' => 'Store Mapping'],
                            ['title' => 'Lain Lain'],
                        ],
                    ],
                ],
            ],
        ];

        $prerequisitesFields = [];
        $id = 1;
        foreach ($prerequisites as $level1) {
            $parentLevel1Id = $id;
            $prerequisitesFields[] = [
                'id' => $id,
                'parent_id' => null,
                'title' => $level1['title'],
                'description' => $level1['description'] ?? null,
                'level' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => '{"id":"0","role":"SEEDERS"}',
                'version' => 1,
            ];
            $id++;
            foreach ($level1['items'] ?? [] as $level2) {
                $prerequisitesFields[] = [
                    'id' => $id,
                    'parent_id' => $parentLevel1Id,
                    'title' => $level2['title'],
                    'description' => $level2['description'] ?? null,
                    'level' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'modified_by' => '{"id":"0","role":"SEEDERS"}',
                    'version' => 1,
                ];
                $parentLevel2Id = $id;
                $id++;
                foreach ($level2['items'] ?? [] as $level3) {
                    $prerequisitesFields[] = [
                        'id' => $id,
                        'parent_id' => $parentLevel2Id,
                        'title' => $level3['title'],
                        'description' => $level3['description'] ?? null,
                        'level' => 2,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'modified_by' => '{"id":"0","role":"SEEDERS"}',
                        'version' => 1,
                    ];
                    $id++;
                }
            }
        }

        DB::table('financing_prerequisite')->insertOrIgnore($prerequisitesFields);

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
