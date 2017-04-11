@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST">
                        <div class="form-group"
                            :class="{ 'has-error':this.validator_error.phone_number }"
                        >
                            <label for="phone_number" class="col-md-4 control-label">手机号码</label>

                            <div class="col-md-6">
                                <input v-model="phone_number" id="phone_number" type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required autofocus>
                                <span class="help-block" v-if="this.validator_error.phone_number">
                                    <strong>@{{ this.validator_error.phone_number }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group"
                             :class="{ 'has-error':this.validator_error.password }"
                        >
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input v-model="password" id="password" type="password" class="form-control" name="password" required>
                                <span class="help-block" v-if="this.validator_error.password">
                                    <strong>@{{ this.validator_error.password }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary"
                                    @click.prevent="sendLoginForm()"
                                >
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    new Vue({
        el : '#app',
        data : {
            phone_number : '',
            password : '',
            validator_error : {
                phone_number : null,
                password : null
            },
        },
        methods : {
            sendLoginForm(){
                //提交登陆表单
                axios.post('/api/login', {
                    //key : value
                    phone_number : this.phone_number,
                    password : this.password
                })
                .then(response => {
                    if(response.data.token) {
                        //清空错误
                        this.validator_error.phone_number = '';
                        this.validator_error.password = '';
                        //存储token
                        localStorage.setItem('jwt_token', response.data.token)
                         //登陆成功,跳转界面
                        //跳转到首页
                        location.href='/';
                    }
                })
                .catch(error => {
                    //清空错误
                    this.validator_error.phone_number = '';
                    this.validator_error.password = '';
                    //清空密码
                    /*this.password = '';*/

                    if(error.response.status == 422){
                        if (error.response.data.errors.phone_number){
                            this.validator_error.phone_number = error.response.data.errors.phone_number[0];
                        }
                        if(error.response.data.errors.password) {
                            this.validator_error.password = error.response.data.errors.password[0];
                        }
                    }

                    if(error.response.status == 401) {
                        this.password = '';
                        this.validator_error.password = '密码错误';
                    }
                });
            }
        }
    });
</script>    
@stop