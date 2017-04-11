<?php

namespace App\Http\ApiController;

use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{

    /**
     * 验证登陆
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
//        $this->validateLogin($request);
        //对用户的输入进行表单的验证
        // grab credentials from the request
        $credentials = $request->only('phone_number', 'password');
        dd(\Auth::guard()->attempt($credentials));
        /*try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }*/

        //走到这里已经登陆成功了,是否需要给用户存储一次session?既传统的web登陆?
        // all good so return the token
        return response()->json(compact('token'));
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