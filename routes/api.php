<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//采用dingo/api提供的方式注册路由
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace'=> 'App\Http\ApiController',], function ($api) {
    /*
     * jwt.auth 中间件的作用是,必须携带正确的信息才能访问token,否则提示401 Unauthorized //未授权 这个则是jwt封装的中间件,状态码
     * api.auth 同上 ,需要在 config/api.php 的auth选项中配置 jwt 或其他认证方式
     * 返回  401 Failed to authenticate because of bad credentials or an invalid authorization header
     * 推荐,更加具有通用性
     *       无法验证，因为credentials不正确 或 header头中的authorization不正确
     *
     * auth:api 同上,但返回 500 Unauthenticated. //未认证,这个是laravel auth自带的相应
     * */
//    $api->get('question/{id}', 'QuestionController@show')->middleware('api.auth'); //这里必须要登陆了才能通过,所以在show方法中不应该再次判断是否登陆,而应该直接去获取用户信息


    //登陆路由
    $api->post('login', 'AuthController@login');
    //注册
    $api->post('register', 'AuthController@register');
    //发送邮件验证码
    $api->post('register_email', 'AuthController@registerEmail'); //设置该接口的调用频率为1分钟一次
    //判断用户是否登陆
    $api->get('is_auth', function (){
        return response()->json(['is_auth'=>true]);
    })->middleware('api.auth');

    //select2数据源
    $api->get('/topics/select_data', 'TopicController@selectData');
    //问题表
    $api->get('/questions/{id}/attention', 'QuestionController@attention');
    $api->resource('/questions', 'QuestionController');
    //答案表
    $api->get('/questions/{question_id}/answers/cancel_answer', 'AnswerController@cancelAnswer');
    $api->get('/questions/{question_id}/answers/user_info', 'AnswerController@userInfo');
    $api->resource('/questions/{question_id}/answers', 'AnswerController');
    //评论
    $api->resource('/answers/{answer_id}/comments', 'CommentController');
});