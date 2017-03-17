<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $weiwenhao = \App\Models\Admin::create([
            'name' => 'weiwenhao',
            'email' => '1101140857@qq.com',
            'password' => bcrypt('123456'),
            'remember_token' => str_random(10),
        ]);
        $xiaowei = \App\Models\Admin::create([
            'name' => 'xiaowei',
            'email' => '123@123.com',
            'password' => bcrypt('123456'),
            'remember_token' => str_random(10),
        ]);
        //分配角色
        $admin = \App\Models\Role::where('name','admin')->first();
        $user = \App\Models\Role::where('name','user')->first();
        $weiwenhao->roles()->attach($admin->id);
        $xiaowei->roles()->attach($user->id);
    }
}
