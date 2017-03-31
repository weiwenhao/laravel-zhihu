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
</style>
@stop
@section('content')
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
                    &nbsp;
                    &nbsp;
                    &nbsp;
                   <span>被浏览 @{{ question.browses_count }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <article class="post">
                        <header class="post-header">
                            <h2 class="post-title">@{{ question.title }}</h2>
                        </header>
                        <section class="post-excerpt">
                            <p v-html="question.content ">
                            </p>
                            <a href="" class="pull-right">展示全部  <i class="fa fa-angle-down"></i></a>
                        </section>
                    </article>
                </div>
            </div>
            <div class="row" style="margin-bottom: 15px">
                <div class="col-md-3">
                        <span style="font-size: 14px" v-if="is_auth">
                            <a href=""
                               onclick="event.preventDefault();"
                               data-toggle="modal" data-target="#editQuestion"
                               @click='renderEditModel'
                            >
                                <i class="fa fa-pencil"></i> 修改问题
                            </a>
                            &nbsp;
                            <a href=""
                               onclick="event.preventDefault();"
                            @click='delQuestion'
                            >
                                <i class="fa fa-trash"></i> 删除问题
                            </a>
                        </span>
                </div>
                <div class="col-md-3 col-md-offset-6">
                    <button class="btn" :class="[is_attention ? 'btn-info':'btn-primary' ]"
                        @click='attention()'
                    >
                        @{{ attention_msg }}
                    </button>
                    &nbsp;&nbsp;&nbsp;
                    <button class="btn btn-default">
                        <i class="fa fa-pencil"></i>   写回答
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
                        You are logged in!
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
<script>
	var question = new Vue({
	    el:'#question',
        data : {
            is_attention : false,
            is_auth : false,
            question : {},
            title :'',
            is_show_user : 0,
            title_error : null,
            topic_ids_error : null,
            question_id : null,
            editUE : '',
            topic_ids_error : null,
            title_error : null,

        },
        created(){
            this.question_id = $('[name=id]').val();
            this.getAttentionStatus();
            this.getQuestion();
            this.isEdit();
        },
        computed : {
            attention_msg(){
                if( this.is_attention)
                    return '正在关注'
                return '关注问题'
            }
        },
        methods : {
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
                        closeOnConfirm: false
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
                        this.getQuestion();
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
            getQuestion(){
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
                        question.editUE.setContent(question.question.content);
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

</script>
@stop