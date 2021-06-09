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
                'name' => 'General User',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
        DB::table(config('auth.table_names.user_auth'))->insertOrIgnore([ [
            'id' => '1',
            'full_name' => $faker->name,
            'username' => 'user@user.com',
            'password' => bcrypt('user123'),
            'status_id' => '10',
            'landline_number' => '0123456789',
            'phone_number' => '08123456789',
            'entity_type_id' => '10',
            'password_updated_at' => date('Y-m-d H:i:s'),
        ]]);
    }
}
