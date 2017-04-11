<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;


/*
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }*/

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'phone_number';
    }

    protected function guard()
    {
//
        return auth()->guard('api');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $request->only('phone_number','password');
        if ($token = $this->guard()->attempt($credentials)) { //根据用户名和密码创建token,这里默认使用的guard是 api
            $this->clearLoginAttempts($request);
            return response()->json([
                'token' => $token,
            ]);
        }
        return response()->json([ //从哪里去除了错误??
            'message' => Lang::get('auth.failed'),
        ], 401);
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            'phone_number' => ['required'],
            'password' => ['required']
        ];
        $validator = app('validator')->make($request->only('phone_number', 'password'), $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('验证失败',$validator->errors()); //422
        }
    }


}
