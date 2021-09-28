<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Host;
use App\Models\User;
use Faker\Generator as Faker;


$factory->define(Host::class, function (Faker $faker) {
    
    return [
        'name' => $faker->name,
        'nip' => strval(rand(1000000000, 9999999999)),
        'position' => $faker->jobTitle(),
        'user_id' => function (array $attributes) {
            return factory(User::class)->create([
                'name' => $attributes['name'],
                'role' => 'host'
            ]);
        },
        // 'user_id'=> rand(1,10),
        // 'user_id' => User::factory()
        //     ->has(
        //         User::factory()
        //                 ->count(3)
        //                 ->state(function (array $attributes, User $user) {
        //                     return ['role' => 'host'];
        //                 })
        //     )
        //     ->create()
    ];
});
