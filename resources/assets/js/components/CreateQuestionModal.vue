<template>
    <!--modal-->
    <div class="modal fade" id="createQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title text-center" id="myModalLabel">
                            写下你的问题
                            <small class="text-defalut center-block">描述精确的问题更易得到解答</small>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div >
                            <div class="form-group">
                                <input type="text" class="form-control"  placeholder="问题标题"
                                       v-model="title"
                                >
                                <span v-if="validator.title_error" class="text-danger pull-right">{{ validator.title_error }}</span>
                            </div>
                            <br>
                            <div class="form-group">
                                <select class="topic form-control" multiple="multiple" style="width: 100%;">
                                </select>
                                <span v-if="validator.topic_ids_error" class="text-danger pull-right">{{ validator.topic_ids_error }}</span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">问题描述（可选）：</label>
                                <div>
                                    <!-- 编辑器容器 -->
                                    <script id="content" name="content" type="text/plain"></script>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" v-model="is_show_user"> 匿名提问
                                </label>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary form-control"
                                        @click='submitQues'
                                >
                                    提交问题
                                </button>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
</template>
<script>
    /*组件选项定义,包括data,methods,等*/
    export default {
        name: 'CreateQuestionModal',
        data () {
            return {
                title :'',
                ue : null,
                is_show_user : 0,
                validator : {
                    title_error : null,
                    topic_ids_error : null
                }
            }
        },
        created(){

        },
        methods : {
            showModal(){
                //判断用户是否登陆 return bool
                axios.get('/api/is_auth')
                .then(response=> {
                	//状态码200表示登陆成功->直接进行业务逻辑
                    if(response.data.is_auth){
                        //打开modal
                        $('#createQuestion').modal('show');
                        //数据接受完毕后进行 select2的渲染
                        this.renderSelect2();
                        //ueditor渲染
                        this.renderUeditor();
                    }
                })
                .catch(error=> {
                	if(error.response.status == 401){ //401 Unauthori...
                	    //todo 弹出登陆框
                        alert('请先登陆!')
                    }
                });
            },
            renderSelect2(){
                //todo: 待添加ajax头 ,暂时先不添加验证
                $(".topic").select2({
                    ajax: {
                        url: "/api/topics/select_data",
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

                });
            },
            renderUeditor(){
                this.ue = UE.getEditor('content',{  //UE应该是UE的全局变量,类似于VUE,$等
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
                this.ue.ready(() => {
                    this.ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                });
            },
            submitQues(){
                this.clearError();
                let title = this.title;
                let topic_ids = $('.topic').val(); //["5", "3"]
                let content = this.ue.getContent(); //html数据,即未转义
                let is_show_user = this.is_show_user * 1; //类型转换
                axios.post('/api/questions', {
                    title : title,
                    topic_ids : topic_ids,
                    content : content,
                    is_show_user : is_show_user
                })
                .then(response=> {
                    if(response.status == 201){
                        location.href=response.headers.location;
                    }
                })
                .catch(error=> {
                     if(error.response.status == 422){
                         if(error.response.data.errors.title){
                             this.validator.title_error = error.response.data.errors.title[0];
                         }
                         if (error.response.data.errors.topic_ids){
                             this.validator.topic_ids_error = error.response.data.errors.topic_ids[0];
                         }
                     }
                });
            },
            clearError(){
                this.validator.title_error = null;
                this.validator.topic_ids_error = null;
            }
        }
    }
</script>
<style>

</style>