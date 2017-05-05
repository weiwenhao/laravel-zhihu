<template>
    <!--定义模板-->
    <div class="panel panel-default login">
        <div class="panel-heading text-center">
            <img src="/img/bg_logo.png" class="login-logo" alt="">
            <h3><small>与世界分享你的知识、经验和见解</small></h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST">
                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.username }"
                >
                    <div class="col-md-10 col-md-offset-1">
                        <input v-model="username" type="text" class="form-control"
                               name="username"
                               @focus="clearError(1, 0, 0, 0)"
                               value=""  placeholder="用户名">
                        <span class="help-block validator" v-if="this.validator_error.username">
                            <strong>{{ this.validator_error.username }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.email }"
                >
                    <div class="col-md-10 col-md-offset-1">
                        <input v-model="email" type="text" class="form-control"
                               name="email"
                               @focus="clearError(0, 1, 0, 0)"
                               value=""  placeholder="邮箱">
                        <span class="help-block validator" v-if="this.validator_error.email">
                            <strong>{{ this.validator_error.email }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.password }"
                >
                    <div class="col-md-10 col-md-offset-1">
                        <input v-model="password" type="password" class="form-control"
                               name="password"
                               @focus="clearError(0, 0, 1, 0)"
                               value=""  placeholder="密码(不少于6位)">
                        <span class="help-block validator" v-if="this.validator_error.password">
                            <strong>{{ this.validator_error.password }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group"
                     :class="{ 'has-error':this.validator_error.captcha }"
                >
                    <div class="col-md-6 col-md-offset-1">
                        <input v-model="captcha" type="text" class="form-control"
                               name="captcha"
                               @focus="clearError(0, 0, 0, 1)"
                               value=""  placeholder="验证码">
                        <span class="help-block validator" v-if="this.validator_error.captcha">
                            <strong>{{ this.validator_error.captcha }}</strong>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-default btn-blue btn-block"
                                @click.prevent="sendCaptcha()"
                                :class="{ 'disabled':send_email_count }"
                        >{{ send_email_btn }}</button>
                    </div>
                    <div class="col-md-12" v-if="send_email_count" style="margin-top: 15px">
                        <span>验证码已经发送到您的邮箱,请注意查收~</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-10 col-md-offset-1">
                        <button type="submit" class="btn btn-primary btn-block" @click.prevent="sendRegisterForm()">
                            注册
                        </button>
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
                username : '',
                email : '',
                password : '',
                captcha : '',
                validator_error : {
                    username : null,
                    email : null,
                    password : null,
                    captcha : null,
                },
                send_email_count : 0,
                sec : null,
            }
        },
        computed : {
            send_email_btn(){
                if(this.send_email_count){
                    return this.send_email_count+'s';
                }
                return '获取';
            }
        },
        created(){
            this.checkLogin();
        },
        methods : {
            sendRegisterForm(){
                //清空一下错误
                this.clearError(1, 1, 1, 1);
                //提交登陆表单
                axios.post('/api/register', {
                    //key : value
                    username : this.username,
                    email : this.email,
                    password : this.password,
                    captcha : this.captcha
                })
                .then(response => {
                    if(response.status == 201) {
                        //跳转到首页
                        location.href='/login'; //如果不存在则跳转到首页
                    }
                })
                .catch(error => {
                    if(error.response.status == 422){ //定义错误
                        if (error.response.data.errors.username){
                            this.validator_error.username = error.response.data.errors.username[0];
                        }
                        if (error.response.data.errors.email){
                            this.validator_error.email = error.response.data.errors.email[0];
                        }
                        if(error.response.data.errors.password) {
                            this.validator_error.password = error.response.data.errors.password[0];
                        }
                        if(error.response.data.errors.captcha) {
                            this.validator_error.captcha = error.response.data.errors.captcha[0];
                        }
                    }
                });
            },
            clearError(username, email, password, captcha){
                //清空错误
                if(username){
                    this.validator_error.username = null;
                }
                if(email){
                    this.validator_error.email = null;
                }
                if(password){
                    this.validator_error.password = null;
                }
                if(captcha){
                    this.validator_error.captcha = null;
                }
            },
            checkLogin(){
                //发送http请求判断? todo 暂时用存储状态进行判断
                let is_auth = localStorage.getItem('is_auth');
                if(Boolean(is_auth)){  //true
                    location.href = '/'; //跳转到首页
                }
            },
            sendCaptcha(){
                if(this.send_email_count == 0){
                    axios.post('/api/register_email', {
                        email : this.email,
                    })
                    .then(response => {
                        if(response.status == 204) {
                            this.setSendEmailCount(); //开始计时
                        }
                    })
                    .catch(error => {
                        if(error.response.status == 422){
                            if (error.response.data.errors.email){
                                this.validator_error.email = error.response.data.errors.email[0];
                            }
                        }
                    });
                }
            },
            setSendEmailCount(){
                this.send_email_count = 60;
                this.timer();
            },
            //循环倒计时
            timer: function () {
                if (this.send_email_count > 0) {
                    this.send_email_count--;
                    setTimeout(this.timer, 1000);
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