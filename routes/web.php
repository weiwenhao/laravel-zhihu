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



Auth::routes();

Route::group(['prefix' => 'admin','namespace'=>'Admin'], function () {
    //登录
    Route::get('login','Auth\LoginController@showLoginForm');
    Route::post('login','Auth\LoginController@login');
    //注册入口
    Route::get('register','Auth\RegisterController@showRegistrationForm');
    Route::post('register','Auth\RegisterController@register');

    Route::post('logout','Auth\LoginController@logout');
});
Route::group(['prefix' => 'admin', 'namespace'=>'Admin', 'middleware'=>['auth.admin','check.admin'] ], function () {
    Route::get('/',function (){
//            dd(Auth::guard('admin')->user()->name);
//        dd(auth('admin')->user());
//        dd(auth('admin')->user()->hasRole('admin'));
        return view('admin.index');
    });
    //权限管理
//    Route::get('/permission/get_nest_perm_list','PermissionController@getNestPermList')->name('menu.index');
    Route::resource('permission','PermissionController');
    //角色管理
    Route::get('/role/dt_roles','RoleController@DtRoles')->name('role.index');
    Route::resource('role','RoleController');
    //管理员管理
    Route::get('/admin/dt_admins','AdminController@DtAdmins')->name('admin.index');;
    Route::resource('admin','AdminController');
});

//前台主页
Route::get('/', 'IndexController@index');
//topic 的 select2数据源
Route::get('/topic/select_res', 'TopicController@select_res');
//存储问题
Route::post('/questions', 'QuestionController@store');
//修改问题
Route::put('/questions/{id}', 'QuestionController@update');
//删除问题
Route::delete('/questions/{id}', 'QuestionController@destroy');
//用户是否可以修改该问题
Route::get('/questions/is_auth', 'QuestionController@isAuth');
//展示问题试图
Route::get('/questions/{id}', 'QuestionController@show');
//获得问题数据
Route::get('/api_question', 'QuestionController@apiQuestion');
//用户是否关注了该问题
Route::get('/questions/{id}/is_attention', 'QuestionController@isAttention');
//用户关注或者取消关注问题
Route::post('/questions/attention', 'QuestionController@attention');

Route::resource('/answer','AnswerController');