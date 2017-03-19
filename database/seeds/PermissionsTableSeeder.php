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
         * RABC
         */
        $rabc = Permission::create([
            'name' => '',
            'display_name' => 'RABC',
            'icon' => 'fa-unlock',
            'description' => 'RABC',
        ]);

        /**
         * 角色管理
         */
        $admin = Permission::create([
            'name' => 'admin.index',
            'display_name' => '管理员管理',
            'url' => '/admin',
            'parent_id' => $rabc->id,
            'description' => '管理员管理',
        ]);
        Permission::create([
            'name' => 'admin.create',
            'display_name' => '添加管理员',
            'parent_id' => $admin->id,
            'description' => '添加管理员',
        ]);
        Permission::create([
            'name' => 'admin.edit',
            'display_name' => '修改管理员',
            'parent_id' => $admin->id,
            'description' => '修改管理员',
        ]);
        Permission::create([
            'name' => 'admin.destroy',
            'display_name' => '删除管理员',
            'parent_id' => $admin->id,
            'description' => '删除管理员',
        ]);

        $role = Permission::create([
            'name' => 'role.index',
            'display_name' => '角色管理',
            'url' => '/role',
            'parent_id' => $rabc->id,
            'description' => '角色管理',
        ]);
        Permission::create([
            'name' => 'role.create',
            'display_name' => '添加角色',
            'parent_id' => $role->id,
            'description' => '添加角色',
        ]);
        Permission::create([
            'name' => 'role.edit',
            'display_name' => '修改角色',
            'parent_id' => $role->id,
            'description' => '修改角色',
        ]);
        Permission::create([
            'name' => 'role.destroy',
            'display_name' => '删除角色',
            'parent_id' => $role->id,
            'description' => '删除角色',
        ]);

        $perm = Permission::create([
            'name' => 'permission.index',
            'display_name' => '权限管理',
            'url' => '/permission',
            'parent_id' => $rabc->id,
            'description' => '权限管理',
        ]);
        Permission::create([
            'name' => 'permission.create',
            'display_name' => '添加权限',
            'parent_id' => $perm->id,
            'description' => '添加权限',
        ]);
        Permission::create([
            'name' => 'permission.edit',
            'display_name' => '修改权限',
            'parent_id' => $perm->id,
            'description' => '修改权限',
        ]);
        Permission::create([
            'name' => 'permission.destroy',
            'display_name' => '删除权限',
            'parent_id' => $perm->id,
            'description' => '删除权限',
        ]);

    }
}
