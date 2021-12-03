<?php

namespace database\factories;
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Appointment;
use App\Models\Host;
use App\Models\Guest;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Appointment::class, function (Faker $faker) {
    $host_ids = Host::all()->pluck('id')->toArray();
    $guest_ids = Guest::all()->pluck('id')->toArray();
    return [
        'host_id' => $faker->randomElement($host_ids),
        'guest_id' => $faker->randomElement($guest_ids),
        'purpose'=> $faker->sentence(6),
        'status'=>$faker->randomElement(['waiting','accepted','declined']),
        'notes'=> $faker->sentence(6),
        'date'=>Carbon::today()->subDays(rand(0, 365))->format('D, d M Y'),
        'time'=>$faker->date('H:i'),
        //
    ];
});
