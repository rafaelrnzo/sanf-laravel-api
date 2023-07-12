<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoSanfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data[] = [
            'xid' => nano_id(),
            'image_url' => 'https://apps.sanfinance.com/img/testing/pic1.jpg',
            'link_url' => 'https://apps.sanfinance.com/info/promo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($index = 1; $index <= 7; $index++) {
            $image = ($index % 2 == 0) ? 'promo-sanf-1.png' : 'promo-sanf-2.png';
            $data[] = [
                'xid' => nano_id(),
                'image_url' => file_get_url($image),
                'link_url' => 'https://apps.sanfinance.com/info/promo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $data[] = [
            'xid' => 'sanf-scanina',
            'image_url' => 'https://via.placeholder.com/800x600.png',
            'link_url' => 'https://via.placeholder.com/800x600.png',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        DB::table('promo_sanf')->insertOrIgnore($data);
    }
}
