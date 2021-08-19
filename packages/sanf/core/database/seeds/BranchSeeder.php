<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
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
                'name' => 'Kantor Pusat',
                'address' => '18 Office Park Lantai 23, Jl. T.B. Simatupang No. 18 Jakarta 12520',
                'msisdn' => '(021) 7817555',
                'msisdn_alternative' => '(021) 7819111; (021) 78847224',
                'email' => 'layanan.konsumen@sanf.co.id',
                'latitude' => -6.299086543708178,
                'longitude' => 106.83208399756622,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'DKI Jakarta',
                'address' => '18 Office Park Lantai 23, Jl. T.B. Simatupang No. 18 Jakarta 12520',
                'msisdn' => '(021) 7817555',
                'msisdn_alternative' => '(021) 7819111; (021) 78847224',
                'email' => 'layanan.konsumen@sanf.co.id',
                'latitude' => -6.299086543708178,
                'longitude' => 106.83208399756622,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Surabaya',
                'address' => 'Gedung Kompas Gramedia, Lantai 1, Jl. Raya Jemursari 64, Surabaya 60237',
                'msisdn' => '(031) 8471431',
                'msisdn_alternative' => '(031) 8471401',
                'email' => null,
                'latitude' => -7.323891605155826,
                'longitude' => 112.74131972676257,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => '​Medan',
                'address' => 'PT. United Tractors Cabang Medan, Lantai 2, Jl. Sisingamangaraja Km 10, Medan 20148',
                'msisdn' => '(061) 7867764',
                'msisdn_alternative' => '(061) 7867764',
                'email' => null,
                'latitude' => 3.5325097497045808,
                'longitude' => 98.73223920567898,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Pekanbaru',
                'address' => 'Komp. Perkantoran Grand Sudirman Blok A7, Jl. Parit Indah / Datuk Setiamaharaja, Pekanbaru 28282',
                'msisdn' => '(0761) 39447',
                'msisdn_alternative' => '(0761) 39447',
                'email' => null,
                'latitude' => 0.48303073912162636,
                'longitude' => 101.4589986500251,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Palembang',
                'address' => 'Sudirman City Centre Lantai 6, Jl. Jendral Sudirman No. 57, Palembang 30125',
                'msisdn' => '(0711) 360338',
                'msisdn_alternative' => '(0711) 360339',
                'email' => null,
                'latitude' => -2.9773617917901474,
                'longitude' => 104.75576815533317,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Jambi',
                'address' => 'Jl.  Hayam Wuruk RT. 34 No. 45, ​Jelutung, Jambi 36136',
                'msisdn' => '(0741) 3607400',
                'msisdn_alternative' => '(0741) 3607400',
                'email' => null,
                'latitude' => -1.603919651029534,
                'longitude' => 103.61703186785408,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Pontianak',
                'address' => 'Komp. Ruko A. Yani Mega Mall Blok C, Jl. Ahmad Yani No. 12 A, Pontianak 78122',
                'msisdn' => '(0561) 760063',
                'msisdn_alternative' => '(0561) 736103',
                'email' => null,
                'latitude' => -0.05105856359497117,
                'longitude' => 109.35309328304585,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => '​Banjarmasin',
                'address' => 'Gedung UTP, Jl. Ahmad Yani KM. 11,3 Kel. Mekar Raya Kec. Kertak Manyar, Kab. Banjar, Banjarmasin',
                'msisdn' => '(0511) 4220410',
                'msisdn_alternative' => '(0511) 4221014',
                'email' => null,
                'latitude' => -3.2178205181715125,
                'longitude' => 114.98278042221851,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Balikpapan',
                'address' => 'Komplek Ruko Little China Blok AB 6 No. 3, Balikpapan Baru, Balikpapan',
                'msisdn' => '(0542) 5650060',
                'msisdn_alternative' => '(0542) 5650060',
                'email' => null,
                'latitude' => -1.2376914197800686,
                'longitude' => 116.85324956359595,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => '​Samarinda',
                'address' => 'Hotel Bumi Senyiur, Lantai Dasar Jl. P. Diponegoro No. 17 - 19, Samarinda 75111',
                'msisdn' => '(0541) 748755',
                'msisdn_alternative' => '(0541) 748754',
                'email' => null,
                'latitude' => -0.4990959751997597,
                'longitude' => 117.14976055933614,
                'modified_by' => json_encode(json_decode('{}'))
            ], [
                'name' => 'Makassar',
                'address' => 'Hotel Claro Jl. Andi Pangeran Pettarani No. 3, Makassar 90222',
                'msisdn' => '(0411) 833888 ext 2229',
                'msisdn_alternative' => '(0411) 854107',
                'email' => 'alpian@sanf.co.id',
                'latitude' => -5.169267136682174,
                'longitude' => 119.4336980384817,
                'modified_by' => json_encode(json_decode('{}'))
            ],
        ];

        DB::table('branch')->insertOrIgnore($data);
    }
}
