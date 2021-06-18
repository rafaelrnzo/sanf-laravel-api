<?php

use Illuminate\Database\Seeder;
use Sanf\Core\Modules\Branch\BranchModel;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(BranchModel::class, 10)->create();
    }
}
