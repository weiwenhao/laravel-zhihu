<template>
    <div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"
                        @click="hideModal()"
                    >
                        &times;
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel">
                        修改问题
                        <small class="text-defalut center-block">描述精确的问题更易得到解答</small>
                    </h4>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="form-group">
                            <input type="text" name="title" class="form-control"  placeholder="问题标题">
                            <span v-if="validator.title_error" class="text-danger pull-right">{{ validator.title_error }}</span>
                        </div>
                        <br>
                        <div class="form-group">
                            <select class="edit-topic form-control" multiple="multiple" style="width: 100%;">
                                <option v-for="topic in question.topics" :value="topic.id">{{ topic.name }}</option>
                            </select>
                            <span v-if="validator.topic_ids_error" class="text-danger pull-right">{{ validator.topic_ids_error }}</span>
                        </div>
                        <div class="form-group">
                            <label>问题描述（可选）：</label>
                            <div>
                                <!-- 编辑器容器 -->
                                <div id="editContent" name="editContent"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                <input name="is_show_user" type="checkbox" v-model="question.is_show_user"> 匿名提问
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary form-control"
                                    @click='submitEditQues()'
                            >
                                提交修改
                            </button>
                        </div>
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
</template>
<script>
    export default {
        props: ['question'],
        data () {
            return {
                ue : null,
                validator : {
                    title_error : null,
                    topic_ids_error : null
                }
            }
        },
        methods : {
            showModal(){
                //判断用户是否登陆 return bool
                axios.get('/api/is_auth')
                .then(response=> {
                    //状态码200表示登陆成功->直接进行业务逻辑
                    if(response.data.is_auth){
                        //设置
                        $('#editQuestion').modal({backdrop: 'static', keyboard: false});
                        //打开modal
                        $('#editQuestion').modal('show');
                        //渲染数据
                        this.renderData();
                    }
                })
                .catch(error=> {
                    if(error.response.status == 401){ //401 Unauthori...
                        //todo 弹出登陆框
                        alert('请先登陆!')
                    }
                });
            },
            hideModal(){
                $('#editQuestion').modal('hide');
                this.clearError();
            },
            submitEditQues(){
                //清空错误
                this.clearError();
                //数据获取
                let title = $('[name="title"]').val();
                let topic_ids = $('.edit-topic').val(); //["5", "3"]
                let content = this.ue.getContent(); //html数据
                let is_show_user = Number(this.question.is_show_user);
                //提交问题
                //提交到后台
                axios.put('/api/questions/'+this.question.id, {
                    id : this.question.id,
                    title : title,
                    topic_ids : topic_ids,
                    content : content,
                    is_show_user : is_show_user
                })
                .then(response=> {
                    if(response.data == 1 && response.status == 200){
                        //刷新问题数据
                        this.$emit('refreshQuestion');
                        //隐藏modal
                        $('#editQuestion').modal('hide');
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
                    if(error.response.status == 401){ //401 Unauthori...
                        //todo 弹出登陆框
                        alert('请先登陆!')
                    }
                });
            },
            clearError(){
                this.validator.title_error = null;
                this.validator.topic_ids_error = null;
            },
            renderData(){
                //数据渲染
                $('[name="title"]').val(this.question.title);
                //组件和数据同时进行了渲染
                this.renderSelect2();
                this.renderUEditor();
            },
            renderSelect2(){
                $(".edit-topic").select2({
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
                }).val(this.question.topic_ids).trigger('change');
            },
            renderUEditor(){
                this.ue = UE.getEditor('editContent',{
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
                this.ue.ready( ()=>{
                    this.ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                    if(this.question.content){
                        this.ue.setContent(this.question.content);
                    }
                });
            }
        }
    }
</script>
