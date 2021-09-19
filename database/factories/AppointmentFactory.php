<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
namespace Database\Factories;
use App\Models\Appointment;
use App\Models\Host;
use App\Models\Guest;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    return [
        'hosts_id'=> Host::factory(),
        'guests_id'=> Guest::factory(),
        'purpose'=> $faker->lexify('???????????????????????????'),
        'status'=>$faker->randomElement(['waiting','accepted','declined','pending']),
        'date'=>$faker->date('now'),
        'time'=>$faker->time('now'),
        //
    ];
});
