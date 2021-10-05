<?php

use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Seeder;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $count = User::count();
        factory(Token::class,$count)->create();
    }
}
