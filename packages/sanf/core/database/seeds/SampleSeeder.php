<?php

use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(AuthSeeder::class);
         $this->call(BranchSeeder::class);
         $this->call(ProductSeeder::class);
         $this->call(PromoAstraSeeder::class);
         $this->call(NewsSeeder::class);
         $this->call(PromoSanfSeeder::class);
    }
}
