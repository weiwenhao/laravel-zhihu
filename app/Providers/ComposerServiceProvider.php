<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\PermissionRepository;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param PermissionRepository $permission
     * @return void
     */
    public function boot(PermissionRepository $permission)
    {
        $menus = $permission->getNestPermList();
        //绑定菜单数据到后台
        \View::composer('admin.*', function ($view) use ($menus){
            $view->with(compact('menus'));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
