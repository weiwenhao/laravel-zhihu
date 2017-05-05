<?php

namespace App\Http\ApiController;

use App\Mail\CaptchaEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class AuthController extends BaseController
{

    use AuthenticatesUsers;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
//        $this->middleware('throttle:1')->only('registerEmail');
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return auth()->guard('api');
    }

    /**
     * 登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $request->only('email','password');
        if ($token = $this->guard()->attempt($credentials)) { //根据用户名和密码创建token,这里默认使用的guard是 api
            $this->clearLoginAttempts($request);
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => \Auth::user()->id,
                    'logo' => \Auth::user()->logo,
                ],
            ]);
        }
        return response()->json([ //从哪里去除了错误??
            'message' => \Lang::get('auth.failed'),
        ], 401);
    }

    /**
     * 表单验证
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|email',
            'password' => ['required']
        ];
        $msg = [
            $this->username().'.email' => '邮箱格式错误'
        ];
        $validator = app('validator')->make($request->only('email', 'password'), $rules, $msg);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('验证失败',$validator->errors()); //422
        }
    }


    /**
     * 注册
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);
        $data = $request->only('username', 'email', 'password');
        $res = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        if($res){
            return $this->response->created(); //201
        }
    }

    private function validateRegister($request)
    {
        $rules = [
            'username'  => 'required|between:2,10',
            $this->username() => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
        $msg = [
            $this->username().'.email' => '邮箱格式错误'
        ];
        $validator = app('validator')->make($request->only('username', 'email', 'password'), $rules, $msg);
        $validator->after(function ($validator) use ($request){ //验证后钩子 可以用来自定义验证规则
            //验证码验证
            $email = $request->get('email');
            $captcha = $request->get('captcha');
            if( !$captcha || \Cache::get('email:'.$email) != $captcha){
                $validator->errors()->add('captcha', '验证码错误');
            }
        });
        if ($validator->fails()) { //如果验证未通过钩子
            throw new StoreResourceFailedException('验证失败',$validator->errors()); //422
        }
    }


    public function registerEmail(Request $request)
    {
        $this->validateEmail($request);
        $captcha_code = $this->getCaptchaCode($request);
        \Mail::to($request->get('email'))->send(new CaptchaEmail($captcha_code)); //todo 如何限制该接口的频率
        return $this->response->noContent(); //204
    }


    private function validateEmail($request)
    {
        $rules = [
            $this->username() => 'required|email',
        ];
        $msg = [
            $this->username().'.email' => '邮箱格式错误'
        ];
        $validator = app('validator')->make($request->only('email'), $rules, $msg);

        if ($validator->fails()) { //如果验证未通过钩子
            throw new StoreResourceFailedException('验证失败',$validator->errors()); //422
        }
    }

    /**
     * 创建一个6位随机数,并存储在 cache中
     *         key       =>     value
     * email:xxx@xxx.xx  =>     654781
     * @param $request
     * @return string 固定6位数的随机数
     */
    private function getCaptchaCode($request){
        //生成6位随机数
        $code =  sprintf('%06d', mt_rand(1,999999));
        //存储在session中 ->session和缓存的本质区别是, 同一个key对用户的唯一性
        // 考虑到实效性,存储在session中 key  email:1101140857@qq.com =>
        \Cache::put('email:'.$request->get('email'), $code, config('zhihu.captcha_timer'));
        return $code;
    }

}