<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insertOrIgnore([
            [
                'id' => '1',
                'title' => 'Sewa Guna Usaha',
                'description' => '<p>Harga alat berat terasa mahal namun tetap ingin memenuhi kebutuhan perusahaan? Bersama SANF yuk cicil dan miliki alat berat Anda sekarang!</p><p></p><p>SANF memberikan kemudahan persyaratan kredit dengan tenor hingga 24 bulan. Yuk ajukan kredit alat berat mu ke SANF!</p>',
                'image' => json_encode([
                    'path' => 'product/1.png',
                    'directory' => 'product/',
                    'file_name' => '1.png',
                    'mime_type' => 'image/png'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => json_encode([])
            ], [
                'id' => '2',
                'title' => 'Jual dan Sewa Balik',
                'description' => '<p>Butuh dana besar dengan jaminan asset namun tetap memakai asset tersebut? Bersama SANF semuanya dapat terwujud!</p><p></p><p>Program Sale & Lease Back dari SANF adalah adalah program yang mengkombinasikan antara penjualan aset Anda dengan penyewaan kembali aset yang sama. Tunggu apa lagi, sekarang Anda dapat memenuhi kebutuhan dana diawal dan tetap bisa menggunakan asset tanpa menghambat aktivitas perusahaan. Yuk segera hubungi SANF!</p>',
                'image' => json_encode([
                    'path' => 'product/2.png',
                    'directory' => 'product/',
                    'file_name' => '1.png',
                    'mime_type' => 'image/png'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => json_encode([])
            ], [
                'id' => '3',
                'title' => 'Anjak Piutang Dengan Jaminan',
                'description' => '<p>Bisnis terhambat karena dana macet? Yuk cairkan dana dengan cepat bersama SANF! Nikmati kemudahan pencairan dana cepat dengan atau tanpa jaminan dari SANF!</p><p></p><p>SANF memberikan kemudahan pencairan dana secara cepat dan fleksibel, dengan kemudahan persyaratan bahkan tanpa jaminan. Ditambah lagi, tenor dapat disesuaikan dengan kemampuan usaha Anda loh. Segera ajukan pencairan dana mu ke SANF!</p>',
                'image' => json_encode([
                    'path' => 'product/1.png',
                    'directory' => 'product/',
                    'file_name' => '1.png',
                    'mime_type' => 'image/png'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => json_encode([])
            ],
        ]);
    }
}
