<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile as File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'title' => 'Sewa Guna Usaha',
                'description' => '<p>Harga alat berat terasa mahal namun tetap ingin memenuhi kebutuhan perusahaan? Bersama SANFIND yuk cicil dan miliki alat berat Anda sekarang!</p><p></p><p>SANFIND memberikan kemudahan persyaratan kredit dengan tenor hingga 24 bulan. Yuk ajukan kredit alat berat mu ke SANFIND!</p>',
            ], [
                'title' => 'Jual dan Sewa Balik',
                'description' => '<p>Butuh dana besar dengan jaminan asset namun tetap memakai asset tersebut? Bersama SANFIND semuanya dapat terwujud!</p><p></p><p>Program Sale & Lease Back dari SANFIND adalah adalah program yang mengkombinasikan antara penjualan aset Anda dengan penyewaan kembali aset yang sama. Tunggu apa lagi, sekarang Anda dapat memenuhi kebutuhan dana diawal dan tetap bisa menggunakan asset tanpa menghambat aktivitas perusahaan. Yuk segera hubungi SANFIND!</p>',
            ], [
                'title' => 'Anjak Piutang Dengan Jaminan',
                'description' => '<p>Bisnis terhambat karena dana macet? Yuk cairkan dana dengan cepat bersama SANFIND! Nikmati kemudahan pencairan dana cepat dengan atau tanpa jaminan dari SANFIND!</p><p></p><p>SANFIND memberikan kemudahan pencairan dana secara cepat dan fleksibel, dengan kemudahan persyaratan bahkan tanpa jaminan. Ditambah lagi, tenor dapat disesuaikan dengan kemampuan usaha Anda loh. Segera ajukan pencairan dana mu ke SANFIND!</p>',
            ], [
                'title' => 'Anjak Piutang Tanpa Jaminan',
                'description' => '<p>Bisnis terhambat karena dana macet? Yuk cairkan dana dengan cepat bersama SANFIND! Nikmati kemudahan pencairan dana cepat dengan atau tanpa jaminan dari SANFIND!</p><p>SANFIND memberikan kemudahan pencairan dana secara cepat dan fleksibel, dengan kemudahan persyaratan bahkan tanpa jaminan. Ditambah lagi, tenor dapat disesuaikan dengan kemampuan usaha Anda loh. Segera ajukan pencairan dana mu ke SANFIND!</p>',
            ], [
                'title' => 'Pembelian dengan Pembayaran Secara Angsuran',
                'description' => '<p>Ada benefit tambahan hanya untuk kamu, pelanggan setia SANFIND! Dengan mengikuti program Purchase with Payment by Installment dari SANFIND, Anda dapat membeli produk-habis-pakai kebutuhan perusahaan mu di vendor pilihan secara angsuran loh!</p><p>Yuk segera hubungi SANFIND!</p>',
            ], [
                'title' => 'Fasilitas Modal Usaha',
                'description' => '<p>Butuh modal usaha? Tak perlu khawatir, SANFIND akan selalu mendukung Anda dalam hal pembiaayan.</p><p>Anda dapat mengajukan fasilitas pembiayaan modal Kerja untuk melancarkan arus keuangan dengan persyaratan mudah dan tenor panjang. Tunggu apalagi? Yuk segara hubungi SANFIND.</p>',
            ],
        ];

        for ($index = 1; $index <= 6; $index++) {
            $file = new File(public_path("/assets/products/{$index}.jpg"), "{$index}.jpg");
            $uploadFile = file_upload(UploadedFile::createFromBase($file), '/product');
            $type = Storage::disk('minio_post')->getMimeType("{$uploadFile}");

            $data[$index - 1] += [
                'id' => $index,
                'image' => json_encode([
                    'file_name' => explode('/', $uploadFile)[1],
                    'path' => $uploadFile,
                    'mime_type' => $type,
                ]),
                'image_path' => $uploadFile,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'modified_by' => json_encode([]),
            ];
        }

        DB::table('product')->insertOrIgnore($data);
    }
}
