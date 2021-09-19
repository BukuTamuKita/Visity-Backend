<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Guest;
use Faker\Generator as Faker;

$factory->define(Guest::class, function (Faker $faker) {
    return [
        //
        'name'=>$faker->name,
        'nik' => strval(rand(1100000000, 9499999999)),
        'email'=>$faker->email,
        'address'=>$faker->address
    ];
});
