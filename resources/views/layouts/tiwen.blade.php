<link href="/plugins/select2/select2.css" rel="stylesheet">
{{--提问modal--}}
<div class="modal fade" id="tiwen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                <form action="">
                    <div class="form-group">
                        <input type="text" class="form-control"  placeholder="问题标题"
                               v-model="title"
                        >
                        <span v-if="title_error" class="text-danger pull-right">@{{ title_error }}</span>
                    </div>
                    <br>
                    <div class="form-group">
                        <select class="topic form-control" multiple="multiple" style="width: 100%;"></select>
                        <span v-if="topic_ids_error" class="text-danger pull-right">@{{ topic_ids_error }}</span>
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
                </form>
                {{--<button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>--}}

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- Scripts -->
@include('vendor.ueditor.assets')
<script src="/plugins/select2/select2.js"></script>
<script>
    /**
     ue编辑器
     */
    var ue = UE.getEditor('content',{
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
                'customstyle', //自定义标题
                'insertcode', //代码语言
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
    ue.ready(function() {
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
    });
    /*
        问题提交
    */
    var tiwen = new Vue({
        el : '#tiwen',
        data : {
            title :'',
            is_show_user : 0,
            title_error : null,
            topic_ids_error : null
        },
        methods :{
            submitQues(){
                this.clearError();

                //name
                let title = this.title;
                //select
                let topic_ids = $('.topic').val(); //["5", "3"]
                //ueditor
                let content = ue.getContent(); //html数据
                //是否匿名
                let is_show_user = this.is_show_user * 1;
                //提交到后台
                axios.post('/question', {
                    title : title,
                    topic_ids : topic_ids,
                    content : content,
                    is_show_user : is_show_user
                })
                    .then(response=> {
                        if (response.data){
                            window.location.href = '/question/'+response.data.id
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
            clearError(){
                this.title_error = null;
                this.topic_ids_error = null;
            }
        }
    });
    /**
     * select2
     * remote模式
     */
    $(".topic").select2({
        ajax: {
            url: "/topic/select_res",
            dataType: 'json',
            delay: 500,
            data: function (params) {
                /*console.log(params); page: 1 ,term: "输入的内容"*/
                return {
                    name: params.term, // search term 传递给服务器端口的参数,由下面的函数得到
                };
            },
            processResults: function (data, params) {  //processResults 处理数据源
                // parse the results into the format expected by Select2 将数据源解析成select希望的格式
//                    params.page = params.page || 1;  //给 params.page一个默认值1
                /*var itemList = [];//当数据对象不是{id:0,text:'ANTS'}这种形式的时候，可以使用类似此方法创建新的数组对象
                 var arr = data.result.list
                 for(item in arr){
                 itemList.push({id: item, text: arr[item]})
                 }*/
                return {
                    results: data, // data是ajax数据源
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        language: 'zh-CN',
        placeholder: "请选择至少一个话题",
        allowClear: true,
    });

</script>