<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            HostSeeder::class,
            GuestSeeder::class,
            TokenSeeder::class,
            AppointmentSeeder::class,
        ]);
        // $this->call(UserSeeder::class);
    }
}
