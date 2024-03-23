<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table(config('auth.table_names.entity_type'))->insertOrIgnore([
            [
                'id' => '10',
                'name' => 'Admin',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '20',
                'name' => 'Mobile',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
        DB::table(config('auth.table_names.user_auth'))->insertOrIgnore([
            'id' => '1',
            'name' => $faker->name,
            'username' => 'admin@admin.com',
            'password' => bcrypt('admin123'),
            'status_id' => '10',
            'entity_type_id' => '10',
        ]);
    }
}
