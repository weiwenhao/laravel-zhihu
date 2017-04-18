<template>
    <div class="question">
        <div class="container">
            <!--标签栏,关注者-->
            <div class="row">
                <div class="col-md-8">
                    <template v-for="topic in question.topics">
                        <span class="Tag">
                            <a>{{ topic.name }}</a>
                        </span>
                    </template>
                </div>
                <div class="col-md-4">
                    <div class="pull-right followers_num">
                        <span>
                            <a href="">关注者
                               <b>{{ question.followers_count }}</b>
                            </a>
                        </span>
                        <span>被浏览 <b>{{ question.browses_count }}</b></span>
                    </div>
                </div>
            </div>
            <!--问题栏-->
            <div class="row">
                <div class="col-md-8">
                    <article class="post">
                        <header>
                            <h2 class="post-title">{{ question.title }}</h2>
                        </header>
                        <div class="question-content" :class="{ 'hide-content':is_hide_content }"  v-html="question.content">

                        </div>
                    </article>
                </div>
            </div>
            <!--操作栏-->
            <div class="row question-status" >
                <div class="col-md-8">
                    <span
                          v-if="question.is_author"
                    >
                        <button href=""
                                class="default-button"
                                onclick="event.preventDefault();"
                                @click='showEditModal()'
                        >
                            <i class="fa fa-pencil"></i> 修改问题
                        </button>
                        <edit-question-modal
                                ref="modal"
                                :question="question"
                                @refreshQuestion="getQuestion()"
                        ></edit-question-modal>
                        <button class="default-button"
                                @click='delQuestion'
                        >
                            <i class="fa fa-trash"></i> 删除问题
                        </button>
                    </span>
                    <a href="javascript:void(0)"
                       class="pull-right"
                       v-if="is_show_hide_button"
                       v-html="show_hide_msg"
                       @click="switchShowOrHide()"
                    ></a>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-default pull-right">
                        <i class="fa fa-pencil"></i>   写回答
                    </button>
                    <div class="pull-right">
                        &nbsp;&nbsp;&nbsp;
                    </div>
                    <button class="btn pull-right" :class="[question.is_attention ? 'btn-info':'btn-primary' ]"
                            @click='attention()'
                    >
                        {{ is_attention_msg }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import EditQuestionModal from './EditQuestionModal.vue'
    /*组件选项定义,包括data,methods,等*/
    //我这里想要的是 vue实例,而非  这样一个 app ={} 东西
    export default  {
        name: 'Question',
        props : ['id'],
        data () {
            return {
                question : {},
                is_hide_content : false, //是否隐藏内容,默认不隐藏,方便计算
                is_show_hide_button : false, //不显示
            }
        },
        components : {
            EditQuestionModal
        },
        created(){
            //created表示实例已经创建完毕.此时的this代表当前实例
            //that = this;
            this.getQuestion();
        },
        mounted(){
        },
        computed : {
            is_attention_msg(){
                if( this.question.is_attention)
                    return '正在关注'
                return '关注问题'
            },
            show_hide_msg(){
                if( this.is_hide_content){
                    return '展开全部    <i class="fa fa-angle-down"></i>';
                }
                return ' <i class="fa fa-chevron-up"></i>    收起  '
            }
        },
        methods : {
            computeHeight(){
                //通过高度计算是否需要隐藏文本内容
                if($('.question-content').outerHeight()*1 > 100){ //
                    this.is_show_hide_button = true; //显示按钮
                    this.is_hide_content = true; //隐藏内容
                }
            },
            getQuestion(){
                //无需验证登陆
                axios.get('/api/questions/'+this.id, {
                    params: {
                        //这里的数据将会以  ?key=value的形式出现
                    }
                })
                .then(response=> {
                    this.question = response.data.data;
                    this.question.topics = response.data.data.topics.data

                    //todo 数据变化处
                    this.$nextTick(function () {
                        this.computeHeight();
                    });
                })
                .catch(error=> {
                    if(error.response.status == 404){
                        alert('资源不存在'+error.response.data.message);
                    }
                });
            },
            switchShowOrHide(){
                this.is_hide_content = !this.is_hide_content;
            },
            delQuestion(){
                swal({
                        title: "删除问题",
                        text: "你确定要删除这个问题吗？相关的答案也将被删除。",
                            /* type: "info",*/
                        showCancelButton: true,
                        confirmButtonColor: "#3097D1",
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        closeOnConfirm: true,
                    },
                    () => {
                        axios.delete('/api/questions/'+this.id)
                        .then(response => {
                            if(response.status == 200){
                                //跳到首页
                                window.location.href = '/';
                            }
                        })
                        .catch(error=> {
                            if(error.response.status == 401){ //401 Unauthori...
                                //todo 弹出登陆框
                                alert('请先登陆!')
                            }
                            if(error.response.status == 404) {
                                alert(error.response.data.message)
                            }
                            if(error.response.status == 403) {
                                alert(error.response.data.message)
                            }
                        });
                    });
            },
            /**
             * 关注 or 取消关注问题
             */
            attention(){
                //关注或取消关注问题开关
                axios.get('/api/questions/'+this.id+'/attention')
                .then(response=> {
                    //关注者数字-1或者+1
                    this.followCountChange();
                    //直接更改状态
                    this.question.is_attention = !this.question.is_attention;
                })
                .catch(error=> {
                    if(error.response.status == 401){ //401 Unauthori...
                        //todo 弹出登陆框
                        alert('请先登陆!')
                    }
                });
            },
            /**
             * 关注者数字 + or - 1
             */
            followCountChange(){
                if(this.question.is_attention){
                    this.question.followers_count --;
                }else {
                    this.question.followers_count ++
                }
            },
            showEditModal(){
                this.$refs.modal.showModal();
            }

        },
    }
</script>
<style>
    body {
        margin-top:49px;
    }
    .followers_num > span {
        margin-right: 15px;
        font-size: 16px;
    }
    .default-button {
        margin-right: 24px;
    }
    .row {
        margin: 15px 0px
    }
    /*问题框背景颜色透明,下边框灰色*/
    .question {
        /*  height: 226px;*/
        background-color: #fff;
        border-bottom: 1px solid #e7eaf1;
    }
    /*蓝色话题标签*/
    .Tag {
        position: relative;
        display: inline-block;
        height: 30px;
        padding: 0 12px;
        font-size: 14px;
        line-height: 30px;
        color: #3e7ac2;
        vertical-align: top;
        background: #eef4fa;
        border-radius: 100px;
        margin-right: 15px;
    }
    /*问题*/
    .hide-content {
        height: 100px;
        line-height:25px;
        overflow:hidden;
    }
</style>