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
            'image_url' => 'http://apps.sanfinance.com/img/testing/pic1.jpg',
            'link_url' => 'https://www.astralife.co.id/',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($index = 1; $index <= 7; $index++) {
            $data[] = [
                'xid' => nano_id(),
                'image_url' => file_get_url('product-astra.png'),
                'link_url' => 'https://www.astralife.co.id/',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('promo_astra')->insertOrIgnore($data);
    }
}
