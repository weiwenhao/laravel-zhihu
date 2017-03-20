<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');


Route::group(['prefix' => 'admin','namespace'=>'Admin'], function () {
    //登录方面路由
    Route::get('login','Auth\LoginController@showLoginForm');
    Route::post('login','Auth\LoginController@login');
    //注册入口
    Route::get('register','Auth\RegisterController@showRegistrationForm');
    Route::post('register','Auth\RegisterController@register');

    Route::post('logout','Auth\LoginController@logout');
});


Route::group(['prefix' => 'admin', 'namespace'=>'Admin', 'middleware'=>['auth.admin'] ], function () {
    Route::get('/',function (){
//        dd(auth('admin')->user()->hasRole('admin'));
        return view('admin.index');
    });
    //权限管理
    Route::get('/permission/get_nest_perm_list','PermissionController@getNestPermList');
    Route::resource('permission','PermissionController');
});