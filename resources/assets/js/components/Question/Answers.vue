<template>
    <!--定义模板-->
    <div class="answers">
        <div class="panel panel-default"
            v-if="answers_count>0"
        >
            <div class="panel-body">
                <div>
                    <span>{{ answers_count }}个回答</span>
                    <button class="default-button pull-right">
                            默认排序    <i class="fa fa-sort"></i>
                    </button>
                    <hr>
                </div>
                <template v-for="answer in answers">
                    <answer :answer="answer" :question_id="question_id"
                            @refreshAnswers="getAnswers()"
                            @editIsDeleted="editIsDeleted()"
                            @showCommentModal="showCommentModal"
                    ></answer>
                </template>
            </div>
        </div>
        <!--已经登陆,并且回答了该问题,并且没有删除该问题-->
        <div class="panel panel-default"
             v-if="is_auth && user_info.is_answered && !user_info.is_deleted"
        >
            <div class="panel-body">
                <div style="text-align:center;">
                 一个问题你只能回答一次，但你可以对 <a href="" style="color: #00a0e9;">现有回答</a> 进行修改
                </div>
            </div>
        </div>
        <!--已经登陆,并且回答了该问题,但是删除了该问题-->
        <div class="panel panel-default"
             v-if="is_auth && user_info.is_answered && user_info.is_deleted"
        >
            <div class="panel-body">
                <div style="text-align:center;">
                    你已经删除了该问题的回答，如果需要修改，请先  <a href='#'  style="color: #00a0e9;" @click.prevent="cancelDelete()">撤销删除</a>
                </div>
            </div>
        </div>


        <div class="panel panel-default"
            v-if="is_auth && !user_info.is_answered"
        >
            <div class="panel-body">
                <div class="media">
                    <a class="pull-left" href="#"><img src="http://iph.href.lu/50x50" alt=""></a>
                    <div class="media-body">
                        <h4 class="media-heading">
                            {{ user_info.data.username }}
                        </h4>
                        一句话描述
                    </div>
                </div>
            </div>
            <div>
                <!-- ue编辑器容器 -->
                <div id="answer" name="answer"></div>
            </div>
            <div class="panel-body">
                <button class="btn btn-primary pull-right"
                        @click="createAnswer"
                >提交回答</button>
            </div>
        </div>
        <!--commentsModal-->
        <comment-modal
            ref="commentModal"
        ></comment-modal>
    </div>
</template>
<script>
    import Answer from './Answer.vue';
    import CommentModal from '../Comment/CommentModal.vue';
    /*组件选项定义,包括data,methods,等*/
    export default {
        name: 'Answers',
        props : ['question_id'],
        components :{
            Answer,
            CommentModal
        },
        data () {
            return {
                params : {
                    offset : 0,
                    limit : 5,
                    order : 'id',
                    sort : 'desc',
                },
                answers : {},
                answers_count : 0,
                ue : null,
                is_auth : false,
                user_info : {
                    is_answered : true,
                    is_deleted : false,
                    data : {}
                }
            }
        },
        created(){
            this.getAnswers();
        },
        methods : {
            getAnswers(){
                axios.get('/api/questions/'+this.question_id+'/answers', {
                	params: this.params
                })
                .then(response=> {
                	 if(response.status == 200) {
                	     this.answers = response.data.data;
                	     this.answers_count = response.data.meta.answers_count;
                	     this.is_auth = response.data.meta.is_auth;

                	     this.$nextTick(function () {
                             //数据接受完毕
                             if(this.is_auth){
                                 //判断并进行更详细的用户信息获取
                                 this.getUserInfo();
                             }
                         })
                     }
                })
                .catch((error)=> {
                        if(error.response.status == 500){
                            alert(error.response.data.message)
                        }
                });
            },
            renderUE(){
                this.ue = UE.getEditor('answer',{
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
                });
            },
            createAnswer(){
                //得到问题内容
                let answerContent = this.ue.getContent();
                if(answerContent)   {
                    //提交问题
                    axios.post('/api/questions/'+this.question_id+'/answers', {
                        'content' : answerContent,
                    })
                    .then(response=> {
                        if(response.status == 200 && response.data.data.id){
                            //销毁ue实例
                            this.ue.destroy();
                            //隐藏回答框
                            this.user_info.is_answered = true;
                            //从新加载答案
                            this.getAnswers();
                            /*//提示成功
                            swal({
                                title: "提交成功",
                                type:"success",
                                timer: 1000, //一秒后自动关闭
                                showConfirmButton: false
                            })*/
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

                    });
                }
            },
            getUserInfo(){
                axios.get('/api/questions/'+this.question_id+'/answers/user_info', {

                })
                .then((response)=> {
                    if(response.status == 200) {
                        this.user_info = response.data;
                        this.$nextTick(function () { //todo 使用watch来监听这样的数据变化
                            // 进行ue是否渲染的判断->并渲染 ,调用该方法时 is_auth已经通过,因此仅判断是否回答过即可
                            if(!this.user_info.is_answered){
                                this.renderUE();
                            }
                        })
                    }
                })
                .catch((error)=> {
                    if(error.response.status == 500){
                        alert(error.response.data.message)
                    }
                });
            },
            cancelDelete(){
                //修改回答状态 todo 由于修改父组件数据的数据的困难 -> 尽快使用vuex进行状态管理 , 更好的处理是可以加一个is_show字段,对问题进行显示和不显示的处理
                this.user_info.is_deleted = false;
                axios.get('/api/questions/'+this.question_id+'/answers/cancel_answer')
                .then(response => {
                    if(response.status == 204){
                        //重新获取答案,需要调用父组件的getAnswers方法

                        this.getAnswers();
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
            },


            /***************给子组建调用的接口数据*****************/
            //修改is_deleted中的数据为true,快速响应
            editIsDeleted(){
                this.user_info.is_deleted = true;
            },
            //调用commentModal框
            showCommentModal(answer_id){
                this.$refs.commentModal.showModal(answer_id); //调用评论模态组件的显示命令
            }
        }
    }
</script>
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