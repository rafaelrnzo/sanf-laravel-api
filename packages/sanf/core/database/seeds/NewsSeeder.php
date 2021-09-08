<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $data[] = [
            'xid' => nano_id(),
            'title' => "Promo menarik 2021",
            'image_url' => 'http://apps.sanfinance.com/img/testing/pic1.jpg',
            'link_url' => 'http://apps.sanfinance.com/info/news',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($index = 1; $index <= 7; $index++) {
            $data[] = [
                'xid' => nano_id(),
                'title' => $faker->sentence(5),
                'image_url' => file_get_url("news-{$index}.png"),
                'link_url' => 'http://apps.sanfinance.com/info/news',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('news')->insertOrIgnore($data);
    }
}
