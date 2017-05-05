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
            'email' => '1101140857@qq.com',
            'password' => bcrypt('123456')
        ]);

        $laowang = User::create([
            'username' => 'laowang',
            'email' => '123@123.com',
            'password' => bcrypt('123456')
        ]);

    }
}
