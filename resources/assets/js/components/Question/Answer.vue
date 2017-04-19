<template>
    <div>
        <!--定义模板-->
        <div class="row media">
            <a class="pull-left" href="#"><img src="http://iph.href.lu/50x50" alt=""></a>
            <div class="media-body">
                <h4 class="media-heading">
                    {{ answer.user.data.username }}
                </h4>
                一句话描述todo
            </div>
        </div>
        <template v-if="is_show_ue">
            <div class="panel panel-default">
                <div>
                    <!-- ue编辑器容器 -->
                    <div id="edit-answer"></div>
                </div>
                <div class="panel-body">
                    <div class=" pull-right">
                        <a href="" @click.prevent="cancelEditAnswer()"  style="margin-right: 15px">取消</a>
                        <button class="btn btn-primary"
                                @click="confirmEditAnswer()"
                        >提交</button>
                    </div>
                </div>
            </div>
        </template>
        <template v-else="">
            <div class="row"
                 :class="['answer-'+answer.id, is_hide_content?'hide-answer':'']"
                 v-html="answer.content"
            >

            </div>
            <div class="row">
                <a href="">发布于 {{ answer.created_at }}</a>
            </div>
            <div class="row">
            <span>
                <button class="blue-button">
                     <i class="fa fa-caret-up"></i>
                    &nbsp;
                    <span>{{ answer.likes_count }}</span>
                </button>
                <button class="blue-button">
                     <i class="fa fa-caret-down"></i>
                </button>
            </span>
                <span style="margin-left: 24px">
                <button class="default-button">
                    <i class="fa fa-comment"></i>
                    {{ answer.comments_count }}条评论
                </button>
                <button class="default-button">
                    <i class="fa fa-paper-plane"></i>
                    分享
                </button>
                <button class="default-button">
                    <i class="fa fa-bookmark"></i>
                    收藏
                </button>


                <template v-if="answer.is_author">
                    <button class="default-button" @click="editAnswer()">
                        <i class="fa fa-pencil"></i>
                        修改
                    </button>
                    <button class="default-button" @click="delAnswer()">
                        <i class="fa fa-trash"></i>
                        删除
                    </button>
                </template>

                <button class="default-button pull-right"
                        v-if="is_show_hide_button"
                        v-html="show_hide_msg"
                        @click="switchShowOrHide()"
                >
                </button>
            </span>
            </div>
        </template>
        <hr>
    </div>
</template>
<script>
    /*组件选项定义,包括data,methods,等*/
    export default {
        name: 'Answer',
        props : ['answer', 'question_id'],
        data () {
            return {
                is_show_ue : false,
                is_hide_content : false, //不隐藏,为了初始计算
                is_show_hide_button : false, //不显示
                ue : null,
            }
        },
        computed : {
            show_hide_msg(){
                if( this.is_hide_content){
                    return '展开全部    <i class="fa fa-angle-down"></i>';
                }
                return ' <i class="fa fa-chevron-up"></i>    收起  '
            }
        },
        created(){//实例创建完毕
//             console.log($(this.answer.content).outerHeight());
        },
        mounted(){ //数据渲染完成?
//            console.log($(this.answer.content).outerHeight());
//             console.log($('.answer-'+this.answer.id).outerHeight());
            //通过高度计算是否需要隐藏文本内容
            this.$nextTick(function () {
                this.computeHeight();
            });
        },
        methods : {
            computeHeight(){
                let defereds = [];
                $('.answer-'+this.answer.id).find('img').each(function() { //每加载完一张图片 resolve()，when() 当所有的 Deferred 完成便执行 done()。
                    var dfd = $.Deferred();
                    $(this).bind('load',function(){
                        dfd.resolve();
                    })
                    defereds.push(dfd);
                });
                $.when.apply(null, defereds).done(() => {//注： 因为 $.when 支持的参数是 $.when(dfd1, dfd2, dfd3, ...)，所以我们这里使用了 apply 来接受数组参
                    //通过高度计算是否需要隐藏文本内容
                    if($('.answer-'+this.answer.id).outerHeight()*1 > 100){ //
                        this.is_show_hide_button = true; //显示按钮
                        this.is_hide_content = true; //隐藏内容
                    }
                });
                /*//通过高度计算是否需要隐藏文本内容
                if($('.answer-'+this.answer.id).outerHeight()*1 > 200){ //
                    this.is_show_hide_button = true; //显示按钮
                    this.is_hide_content = true; //隐藏内容
                }*/
            },
            delAnswer(){ //该接口对父组件的影响较多,因此使用时间转发传递到外面?
                swal({
                        title: "你确定要删除自己的答案吗？",
                        text: "答案内容不会被永久删除，你还可以撤消本次删除操作。",
                            /* type: "info",*/
                        showCancelButton: true,
                        confirmButtonColor: "#3097D1",
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        closeOnConfirm: true,
                    },
                    () => {
                        axios.delete('/api/questions/'+this.question_id+'/answers/'+this.answer.id)
                        .then(response => {
                            if(response.status == 204){
                                //重新获取答案,需要调用父组件的getAnswers方法
                                this.$emit('refreshAnswers');
                                //修改回答状态 todo 由于修改父组件数据的数据的困难 -> 尽快使用vuex进行状态管理
                                this.$emit('editIsDeleted');
                            }
                        })
                        .catch((error)=> {

                            if(error.response.status == 404) {
                                alert(error.response.data.message)
                            }
                            if(error.response.status == 403) {
                                alert(error.response.data.message)
                            }
                        });
                    });
            },
            editAnswer(){
                //开启ue框
                this.is_show_ue = true;
                //ue渲染 -> 如果无法渲染可以考虑 this.$nextT...
                this.$nextTick(function () {
                    this.renderUE();
                })
            },
            confirmEditAnswer(){
                //得到content
                let content = this.ue.getContent();
                if(content){
                    //提交修改
                    axios.put('/api/questions/'+this.question_id+'/answers/'+this.answer.id, {
                        'content' : content,
                    })
                    .then(response=> {
                        if(response.status == 200 && response.data.data.id){
                            //个人习惯上先进行数据赋值,再进行样式处理
                            this.answer = response.data.data;
                            //调用取消方法,关闭答案框,并
                            this.cancelEditAnswer();

                        }
                    })
                    .catch(error=> {
                        if(error.response.status == 401){ //401 Unauthori...
                            //todo 弹出登陆框
                            alert('请先登陆!')
                        }
                        if(error.response.status == 404){ //401 Unauthori...
                            //todo
                            alert('资源不存在!')
                        }
                        if(error.response.status == 403){ //401 Unauthori...
                            //todo
                            alert('权限不足!')
                        }

                    });
                }
            },
            cancelEditAnswer(){
              //如何销毁ue实例?使用了v-if还需要手动销毁吗
                this.is_show_ue = false;
                this.ue.destroy(); //销毁ue -> 需要手动销毁ue才不影响下次使用该id继续创建实例
            },
            renderUE(){
                this.ue = UE.getEditor('edit-answer',{
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
                this.ue.ready(() => {
                    this.ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                    //set content
                    this.ue.setContent(this.answer.content);
                });
            },
            switchShowOrHide(){
                this.is_hide_content = !this.is_hide_content;
            }
        }
    }
</script>
<style>
    /*回单框的ueditor样式修改*/
    #edit-answer > .edui-editor {
        border: 1px solid transparent;
        background-color: white;
    }
    .edui-default .edui-editor-toolbarbox {
        background: #f5f8fa;
    }
    /*收起答案框*/
    .hide-answer {
        height: 200px;
        line-height:25px;
        overflow:hidden;
    }
</style>