<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Token;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Token::class, function (Faker $faker) {
    $user_ids = User::all()->pluck('id')->toArray();
    return [
        //
        'user_id'=> $faker->randomElement($user_ids),
        'token' => Str::random(10),
        'type' => $faker->randomElement(['forget','refresh','fcm_token']),
    ];
});
