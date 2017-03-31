{{--修改问题modal--}}
<div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
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
                        <span v-if="title_error" class="text-danger pull-right">@{{ title_error }}</span>
                    </div>
                    <br>
                    <div class="form-group">
                        <select class="edit-topic form-control" multiple="multiple" style="width: 100%;">
                            <option v-for="topic in question.topics" :value="topic.id">@{{ topic.name }}</option>
                        </select>
                        <span v-if="topic_ids_error" class="text-danger pull-right">@{{ topic_ids_error }}</span>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">问题描述（可选）：</label>
                        <div>
                            <!-- 编辑器容器 -->
                            <script id="editContent" name="editContent" type="text/plain"></script>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <input name="is_show_user" type="checkbox" v-model="question.is_show_user"> 匿名提问
                        </label>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary form-control"
                        @click='editQues()'
                        >
                        提交修改
                        </button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>