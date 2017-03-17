<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 角色管理
         */
        Permission::create([
            'name' => 'user.list',
            'display_name' => '用户列表',
            'description' => '用户列表',
        ]);
        Permission::create([
            'name' => 'user.create',
            'display_name' => '添加用户',
            'description' => '添加用户',
        ]);
        Permission::create([
            'name' => 'user.edit',
            'display_name' => '修改用户',
            'description' => '修改用户',
        ]);
        Permission::create([
            'name' => 'user.destroy',
            'display_name' => '删除用户',
            'description' => '删除用户',
        ]);




        Permission::create([
            'name' => 'role.list',
            'display_name' => '角色列表',
            'description' => '角色列表',
        ]);
        Permission::create([
            'name' => 'role.create',
            'display_name' => '添加角色',
            'description' => '添加角色',
        ]);
        Permission::create([
            'name' => 'role.edit',
            'display_name' => '修改角色',
            'description' => '修改角色',
        ]);
        Permission::create([
            'name' => 'role.destroy',
            'display_name' => '删除角色',
            'description' => '删除角色',
        ]);

        Permission::create([
            'name' => 'perm.list',
            'display_name' => '权限列表',
            'description' => '权限列表',
        ]);
        Permission::create([
            'name' => 'perm.create',
            'display_name' => '添加权限',
            'description' => '添加权限',
        ]);
        Permission::create([
            'name' => 'perm.edit',
            'display_name' => '修改权限',
            'description' => '修改权限',
        ]);
        Permission::create([
            'name' => 'perm.destroy',
            'display_name' => '删除权限',
            'description' => '删除权限',
        ]);
        /**
         * 菜单权限
         */
        Permission::create([
            'name' => 'menu.list',
            'display_name' => '菜单列表',
            'description' => '菜单列表',
        ]);
        Permission::create([
            'name' => 'menu.create',
            'display_name' => '添加菜单',
            'description' => '添加菜单',
        ]);
        Permission::create([
            'name' => 'menu.edit',
            'display_name' => '修改菜单',
            'description' => '修改菜单',
        ]);
        Permission::create([
            'name' => 'menu.destroy',
            'display_name' => '删除菜单',
            'description' => '删除菜单',
        ]);

    }
}
