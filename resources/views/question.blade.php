@extends('layouts.app')
@section('css')
<style>
    body {
        margin-top: 49px;
    }
    .question {
      /*  height: 226px;*/
        background-color: #fff;
        border-bottom: 1px solid #e7eaf1;
    }
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
    }
    .hide-content {
        height: 100px;
        line-height:25px;
        overflow:hidden;
    }

    img {
        width: 100%
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    .blue-button {
        height: 34px;
        padding: 0 10px;
        line-height: 32px;
        color: #2d84cc;
        text-align: center;
        cursor: pointer;
        background: #ebf3fb;
        border-radius: 3px;
        box-sizing: border-box;
        font: inherit;
        border: none;
        outline: none;
        -webkit-appearance: none;
    }

    .default-button {
        height: auto;
        padding: 0;
        line-height: inherit;
        background-color: transparent;
        border: none;
        border-radius: 0;
        font: inherit;
        border: none;
        outline: none;
        -webkit-appearance: none;
        color: #8590a6;
        text-align: center;
        cursor: pointer;
        margin-left: 24px;
    }
    /*.fixed-bottom {
        position:fixed;
        bottom: 0px;
    }*/
</style>
@stop
@section('content')
    <div id="app">
        <div class="question" id="question">
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="container">
                <div class="row" style="margin-top: 15px">
                    <div class="col-md-5">
                        <template v-for="topic in question.topics">
                            <div class="Tag" v-on:onclik='showTopic(topic.id)'>
                                <a :href="topicUrl(topic.id)">@{{ topic.name }}</a>
                            </div> &nbsp;
                        </template>
                    </div>
                    <div class="col-md-3 col-md-offset-4">
                   <span>
                       <a href="">关注者
                           <span>@{{ question.followers_count }}</span>
                       </a>
                   </span>
                        <span style="margin-left: 20px">被浏览 @{{ question.browses_count }}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <article class="post">
                            <header>
                                <h2 class="post-title">@{{ question.title }}</h2>
                            </header>
                            <div :class="{ 'hide-content':is_hide_content }">
                                <div v-html="question.content">
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                <div class="row question-status" style="margin: 15px 0px"  >
                    <div class="col-md-8">
                    <span style="font-size: 14px" v-if="is_auth">
                        <button href=""
                                class="default-button"
                           onclick="event.preventDefault();"
                           data-toggle="modal" data-target="#editQuestion"
                        @click='renderEditModel'
                        >
                            <i class="fa fa-pencil"></i> 修改问题
                        </button>

                        <button href=""
                                class="default-button"
                           style="margin-left: 30px;"
                           onclick="event.preventDefault();"
                        @click='delQuestion'
                        >
                            <i class="fa fa-trash"></i> 删除问题
                        </button>
                    </span>
                        <a href="javascript:void(0)"
                           class="pull-right"
                           v-html="show_hide_msg"
                        @click="triggerContent()"
                        ></a>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-default pull-right">
                            <i class="fa fa-pencil"></i>   写回答
                        </button>
                        <div class="pull-right">
                            &nbsp;
                            &nbsp;
                        </div>
                        <button class="btn pull-right" :class="[is_attention ? 'btn-info':'btn-primary' ]"
                        @click='attention()'
                        >
                        @{{ attention_msg }}
                        </button>
                    </div>
                </div>
            </div>
            @include('question.editQuestion')
        </div>
        <div class="container">
            <div class="row" style="margin-top: 15px">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div>
                                <b>n个回答</b>
                                <span class="pull-right">
                                默认排序    <i class="fa fa-sort"></i>
                                </span>
                                <hr>
                            </div>
                            <div v-for="answer in answers">
                                <div class="media">
                                    <a class="pull-left" href="#"><img src="http://iph.href.lu/50x50" alt=""></a>
                                    <div class="media-body">
                                        <h4 class="media-heading">
                                            @{{ answer.user.username }}
                                        </h4>
                                        一句话描述todo
                                    </div>
                                </div>
                                <div style="margin-top: 15px"
                                    v-html="answer.content"
                                >
                                </div>
                                <div style="margin: 15px 0">
                                    <a href="">发布于 @{{ answer.renderDate }}</a>
                                </div>
                                <div class="status">
                                    <span>
                                        <button class="blue-button">
                                             <i class="fa fa-caret-up"></i>
                                            &nbsp;
                                            <span>@{{ answer.likes_count }}</span>
                                        </button>
                                        <button class="blue-button">
                                             <i class="fa fa-caret-down"></i>
                                        </button>
                                    </span>
                                    <button class="default-button">
                                        <i class="fa fa-comment"></i>
                                        @{{ answer.comments_count }}条评论
                                    </button>
                                    <button class="default-button">
                                        <i class="fa fa-paper-plane"></i>
                                        分享
                                    </button>
                                    <button class="default-button">
                                        <i class="fa fa-bookmark"></i>
                                        收藏
                                    </button>
                                    <button class="default-button">
                                        <i class="fa fa-heart"></i>
                                        感谢
                                    </button>
                                    <button class="default-button">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="media">
                                <a class="pull-left" href="#"><img src="http://iph.href.lu/50x50" alt=""></a>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        用户名
                                    </h4>
                                    一句话描述
                                </div>
                            </div>
                        </div>
                        <div>
                            <!-- 编辑器容器 -->
                            <script id="answer" name="answer" type="text/plain"></script>
                        </div>
                        <div class="panel-body">
                            <button class="btn btn-primary pull-right"
                            @click='createAnswer()'
                            >提交回答</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            You are logged in!
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Dashboard</div>

                        <div class="panel-body">
                            You are logged in!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<style>
    /*回单框的ueditor样式修改*/
    #answer > .edui-editor {
        border: 1px solid transparent;
        background-color: white;
    }
    .edui-default .edui-editor-toolbarbox {
        background: #f5f8fa;
    }
</style>
<script>
	var question = new Vue({
	    el:'#app',
        data : {
            is_attention : false,
            is_auth : false,
            question : {},
            answers : {},
            title :'',
            is_show_user : 0,
            title_error : null,
            topic_ids_error : null,
            question_id : null,
            editUE : '',
            answerUE : '',
            topic_ids_error : null,
            title_error : null,
            is_hide_content : true,

        },
        created(){
            this.question_id = $('[name=id]').val();
            this.getAttentionStatus();
            this.getQuestions();
            this.isEdit();
            this.randerAnswerUE();
            this.getAnswers();
        },
        computed : {
            attention_msg(){
                if( this.is_attention)
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
            getAnswers(page_size=5, order='likes_count_desc'){
                //排序规则
                //评论内容,is_auth,每条评论内容要附带用户信息(id,username,一句话描述,logo)
                axios.get('/answer', {
                	params: {
                	    page_size : page_size,
                		question_id : this.question_id,
                        order : order
                	}
                })
                .then(response=> {
                    this.answers = response.data;
                })
                .catch(error=> {
                    swal({
                    		title: "系统错误",
                    		type:"error",
                    		text: '<a href="/" class="btn btn-primary"><i class="fa fa-home"></i>首页</a>',
                    		html : true,
                    		/*timer: 2000,*/
                    		showConfirmButton: false
                    	})
                });
            },
            createAnswer(){
                //得到问题内容
                let answerContent = this.answerUE.getContent();
                if(answerContent){
                    //提交问题
                    axios.post('/answer', {
                        'content' : answerContent,
                        'question_id' : this.question_id
                    })
                    .then(response=> {
                        if(response.data.id){
                            //清空答案框
                            this.answerUE.setContent('');
                            //从新加载答案
                            this.getAnswers();
                            //提示成功
                            swal({
                            		title: "提交成功",
                            		type:"success",
                            		/*text: '<a href="/" class="btn btn-primary"><i class="fa fa-home"></i>首页</a>',
                            		html : true,*/
                            		timer: 1000,
                            		showConfirmButton: false
                            	})
                        }
                    })
                    .catch(error=> {
                        //500以外状态码提示
                        console.log(error.response.data)
                    });
                }
            },
            /*
            *
            * 隐藏或者展开问题
            * */
            triggerContent(){
                this.is_hide_content = !this.is_hide_content;
            },
            delQuestion(){
                //弹出对话框,确认是否删除
                swal({
                        title: "删除问题",
                        text: "你确定要删除这个问题吗？相关的评论也将被删除。",
                       /* type: "info",*/
                        showCancelButton: true,
                        confirmButtonColor: "#3097D1",
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        closeOnConfirm: false,
                    },
                    confirm => {
                        axios.delete('/question/'+this.question_id, {
                            //key : value
                        })
                        .then(response => {
                            if(response.data){
                                //跳到首页
                                window.location.href = '/';
                            }
                        })
                        .catch(error=> {
                            swal("删除失败", "系统错误!", "error")
                        });
                    });
            },
            renderEditModel(){
                //给标题赋值
                $('[name=title]').val(this.question.title);
                //数据接受完毕后进行 select2的渲染
                this.renderSelect2();
                //ueditor渲染
                this.renderUeditor();
            },
            editQues(){
                this.clearError();
                //name
                let title = $('[name="title"]').val();
                //select
                let topic_ids = $('.edit-topic').val(); //["5", "3"]
                //ueditor
                let content = this.editUE.getContent(); //html数据
                //是否匿名
                let is_show_user = this.question.is_show_user * 1;
                //提交到后台
                axios.put('/question/'+this.question_id, {
                    id : this.question_id,
                    title : title,
                    topic_ids : topic_ids,
                    content : content,
                    is_show_user : is_show_user
                })
                .then(response=> {
                    if (response.data){
                        //刷新界面
                        this.getQuestions();
                        //隐藏modal
                        $('#editQuestion').modal('hide');

                    }else{
                        swal({
                            title: "更新失败",
                            type:"error",
                            /*timer: 2000,*/
                            showConfirmButton: true
                        })
                    }
                })
                .catch(error=> {
                    if(error.response.data){
                        this.title_error = error.response.data.title[0];
                    }
                    if (error.response.data.topic_ids){
                        this.topic_ids_error = error.response.data.topic_ids[0];
                    }

                });
            },
            getQuestions(){
                axios.get('/api_question', {
                    params: {
                        question_id: this.question_id,
                    }
                })
                    .then(response=> {
                        this.question = response.data;
                    })
                    .catch(error=> {
                        swal({
                            title: "系统错误",
                            type:"error",
                            text: '<a href="/" class="btn btn-primary"><i class="fa fa-home"></i>首页</a>',
                            html : true,
                            /*timer: 2000,*/
                            showConfirmButton: false
                        })
                        //返回上一页
                    });
            },
            randerAnswerUE(){
                this.answerUE = UE.getEditor('answer',{
                    toolbars: [
                        [
                            'bold',
                            'italic',
                            'blockquote',//引用
                            /*'insertcode', //代码语言*/
                            'insertunorderedlist', //无序
                            'insertorderedlist', //有序
                            'insertvideo', //视频
                            'insertimage',
                        ]
                    ],
                    elementPathEnabled: false,
                    enableContextMenu: false,
                    autoClearEmptyNode:true,
                    wordCount:false,
                    imagePopup:false,
                    autotypeset:{ indent: true,imageBlockLine: 'center' },
                    initialFrameHeight: 140,
                });
                this.answerUE.ready(function() {
                    question.answerUE.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                });
            },
            renderUeditor(){
                this.$nextTick(function () {
                    this.editUE = UE.getEditor('editContent',{
                        toolbars: [
                            [
                                'bold',
                                'italic',
                                'blockquote',//引用
                                /*'insertcode', //代码语言*/
                                'insertunorderedlist', //无序
                                'insertorderedlist', //有序
                                'insertvideo', //视频
                                'insertimage',
                            ]
                        ],
                        elementPathEnabled: false,
                        enableContextMenu: false,
                        autoClearEmptyNode:true,
                        wordCount:false,
                        imagePopup:false,
                        autotypeset:{ indent: true,imageBlockLine: 'center' },
                        initialFrameHeight: 140,
                        zIndex: 3000
                    });
                    this.editUE.ready(function() {
                        question.editUE.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                        if(question.question.content){
                            question.editUE.setContent(question.question.content);
                        }
                    });
                });
            },
            renderSelect2(){
                this.$nextTick(function () {
                    $(".edit-topic").select2({
                        ajax: {
                            url: "/topic/select_res",
                            dataType: 'json',
                            delay: 500,
                            data: function (params) {
                                return {
                                    name: params.term, // search term 传递给服务器端口的参数,由下面的函数得到
                                };
                            },
                            processResults: function (data, params) {  //processResults 处理数据源
                                return {
                                    results: data, // data是ajax数据源
                                };
                            }
                        },
                        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                        minimumInputLength: 1,
                        language: 'zh-CN',
                        placeholder: "请选择至少一个话题",
                        allowClear: true,
                    }).val(this.question.topic_ids).trigger('change');
                });
            },
            isEdit(){
              axios.get('/question/is_auth', {
              	params: {
              		question_id: this.question_id,
              	}
              })
              .then(response=> {
              	 this.is_auth = response.data;

              })
              .catch(error=> {
              	console.log(error);
              });
            },
            /**
             * 话题跳转链接
             * */
            topicUrl(id){
                return '/topic/'+id;
            },
            /*
            * 判断用户是否关注了问题
            * */
            getAttentionStatus(){
                axios.get('/question/'+this.question_id+'/is_attention', {
                })
                .then(response=> {
                     this.is_attention = response.data  // 0 or 1
                })
                .catch(error=> {
                	console.log(error);
                });
            },
            /*
            * 关注或者取消关注问题
            * */
            attention(){
                //关注者数字-1或者+1
                this.followChange();
                //直接更改状态
                this.is_attention = !this.is_attention;
                //关注或取消关注问题开关
                 axios.post('/question/attention', {
                  	'question_id': this.question_id
                 })
                 /*.then(response=> {
                 	console.log(response.data);
                 })
                 .catch(error=> {
                 	console.log(error.response.data)
                 });*/
            },
            /**
             * 关注者人数加1或者－1
             */
            followChange(){
                if(this.is_attention){
                    this.question.followers_count --;
                }else {
                    this.question.followers_count ++
                }
            },
            clearError(){
                this.title_error = null;
                this.topic_ids_error = null;
            },

        }
    });
	/*$(window).scroll(function () {
        var window_height = $(window).height(); //窗口的距离  定值
        var window_offset_top = $(window).scrollTop(); //窗口上部偏移出的距离
        var res_offset_top = $('.position-reference')[0].offsetTop; //当前div距离页面顶端的距离
        /!*!//当前div距离窗口顶部的距离
         console.log(res_offset_top - window_offset_top);*!/
         if(res_offset_top - window_offset_top > window_height) {
             //屏幕外面
             $('.question-status').css({'position':'fixed', 'bottom':'0px'})
         }else{
             $('.question-status').css({'position':'static', 'bottom':'none'})
         }

    });*/

</script>
@stop