<?php

namespace database\factories;
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Appointment;
use App\Models\Host;
use App\Models\Guest;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    return [
        'hosts_id'=> factory(Host::class),
        'guests_id'=> factory(Guest::class),
        'purpose'=> $faker->lexify('??????????????????????'),
        'status'=>$faker->randomElement(['waiting','accepted','declined','pending']),
        'date'=>$faker->date('Y-m-d','now'),
        'time'=>$faker->time('H:i','now'),
        //
    ];
});
