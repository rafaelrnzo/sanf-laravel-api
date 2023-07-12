<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoAstraSeeder extends Seeder
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
            'web_url' => 'https://www.astralife.co.id/',
            'android_url' => null,
            'ios_url' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($index = 1; $index <= 7; $index++) {
            $data[] = [
                'xid' => nano_id(),
                'image_url' => file_get_url('product-astra.png'),
                'web_url' => 'https://astrapay.com/',
                'android_url' => 'com.ada.astrapay',
                'ios_url' => '1487585085',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('promo_astra')->insertOrIgnore($data);
    }
}
