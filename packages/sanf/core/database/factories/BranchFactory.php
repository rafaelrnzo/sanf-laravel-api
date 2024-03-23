<?php

/** @var Factory $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Sanf\Core\Modules\Branch\BranchModel as Branch;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Branch::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address' => $faker->address,
        'msisdn' => $faker->e164PhoneNumber,
        'msisdn_alternative' => $faker->e164PhoneNumber,
        'email' => $faker->companyEmail,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
        'modified_by' => json_encode([]),
    ];
});
