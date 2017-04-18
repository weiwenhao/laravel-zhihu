@extends('layouts.app')
@section('css')
<style>
    body {
        margin-top:49px;
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


</style>
@stop
@section('content')
    {{--问题区域--}}
    <question id="{{ $id }}"></question>

    <div class="container">
        <div class="row" style="margin-top: 15px">
            <div class="col-md-8">
                {{--回答区域--}}
                <answers :question_id="{{ $id }}"></answers>
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
@stop
@section('js')
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
//            this.getAttentionStatus();
//            this.isEdit();
//            this.randerAnswerUE();
            /*this.getAnswers();*/
        },
        computed : {

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
                        axios.delete('/questions/'+this.id, {
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
                //尝试手动打开modal框
                $('#editQuestion').modal('show');
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
                axios.put('/questions/'+this.question_id, {
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
                    if(error.response.data.title){
                        this.title_error = error.response.data.title[0];
                    }
                    if (error.response.data.topic_ids){
                        this.topic_ids_error = error.response.data.topic_ids[0];
                    }

                });
            },
            getQuestion(){
                axios.get('/api/questions/'+this.question_id, {
                    params: {
//                        question_id: this.question_id,
                        include : 'topics'
                    }
                })
                .then(response=> {
                     this.question = response.data.data;
                     this.question.topics = response.data.data.topics.data
                })
                .catch(error=> {
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
                    this.editUE.ready(ue =>{
                        this.editUE.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                        if(this.question.content){
                            this.editUE.setContent(this.question.content);
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
              axios.get('/questions/is_auth', {
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
            /*
            * 判断用户是否关注了问题
            * */
            getAttentionStatus(){
                axios.get('/questions/'+this.question_id+'/is_attention', {
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
                 axios.post('/questions/attention', {
                  	'question_id': this.question_id
                 })
                 .then(response=> {
                 	console.log(response.data);
                 })
                 .catch(error=> {
                 	console.log(error.response.data)
                 });
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