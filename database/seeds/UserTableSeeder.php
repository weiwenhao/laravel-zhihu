<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $weiwenhao = User::create([
            'username' => 'weiwenhao',
            'phone_number' => '13168065609',
            'password' => bcrypt('123456')
        ]);

        $laowang = User::create([
            'username' => 'laowang',
            'phone_number' => '18026650158',
            'password' => bcrypt('123456')
        ]);

    }
}
