<template>
    <!--定义模板-->
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <img src="/img/bg_logo.png" class="login-logo" alt="">
            <h3><small>与世界分享你的知识、经验和见解</small></h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST">
                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.email }"
                >
                    <div class="col-md-10 col-md-offset-1">
                        <input v-model="email" id="email" type="text" class="form-control"
                               name="email"
                               @focus="clearError(1)"
                               value="" required autofocus placeholder="邮箱">
                        <span class="help-block validator" v-if="this.validator_error.email">
                            <strong>{{ this.validator_error.email }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.password }"
                >

                    <div class="col-md-10 col-md-offset-1">
                        <input v-model="password" id="password" type="password"
                               class="form-control" name="password" required placeholder="密码"
                               @focus="clearError(0, 1)"
                        >
                        <span class="help-block validator" v-if="this.validator_error.password">
                                <strong>{{ this.validator_error.password }}</strong>
                            </span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-10 col-md-offset-1">
                        <button type="submit" class="btn btn-primary btn-block"
                                @click.prevent="sendLoginForm()"
                        >
                            登陆
                        </button>

                        <a class="btn btn-link pull-right" href="">
                            无法登陆?
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    /*组件选项定义,包括data,methods,等*/
    export default {
        name: '',
        data () {
            return {
                email : '',
                password : '',
                validator_error : {
                    email : null,
                    password : null
                },
            }
        },
        created(){
            this.checkLogin();
        },
        methods : {
            sendLoginForm(){
                //提交登陆表单
                axios.post('/api/login', {
                    email : this.email,
                    password : this.password
                })
                .then(response => {
                    if(response.status == 200) {
                        //清空错误
                        this.validator_error.email = '';
                        this.validator_error.password = '';
                        //存储token
                        localStorage.setItem('jwt_token', response.data.token)
                        //存储登陆状态
                        localStorage.setItem('is_auth',1)
                        //存储用户信息
                        localStorage.setItem('user_id', response.data.user.id)
                        localStorage.setItem('user_logo', response.data.user.logo)

                        //登陆成功,跳转界面
                        let url = sessionStorage.getItem('url');
                        if(url){
                            location.href= url;
                        }else {
                            //跳转到首页
                            location.href='/'; //如果不存在则跳转到首页
                        }

                    }
                })
                .catch(error => {
//                        this.clearError(email=null, password=null);
                    //清空密码
                    /*this.password = '';*/

                    if(error.response.status == 422){
                        if (error.response.data.errors.email){
                            this.validator_error.email = error.response.data.errors.email[0];
                        }
                        if(error.response.data.errors.password) {
                            this.validator_error.password = error.response.data.errors.password[0];
                        }
                    }

                    if(error.response.status == 401) { //401为授权失败,既表单验证通过,但用户名或者密码错误
                        this.password = '';
                        this.validator_error.password = '邮箱或密码错误';
                    }
                });
            },
            clearError(email, password){
                //清空错误
                if(email){
                    this.validator_error.email = '';
                }
                if(password){
                    this.validator_error.password = '';
                }
            },
            checkLogin(){
                //发送http请求判断? todo 暂时用存储状态进行判断
                let is_auth = localStorage.getItem('is_auth');
                if(Boolean(is_auth)){  //true
                    location.href = '/'; //跳转到首页
                }
            }
        }
    }
</script>
<style>
    .login-logo{
        margin: 0 auto;
        width: 160px;
        height: 74px;
    }
    input.form-control {
        position: relative
    }
    .validator{
        position: absolute;right: 20px;top: 0px
    }


</style>