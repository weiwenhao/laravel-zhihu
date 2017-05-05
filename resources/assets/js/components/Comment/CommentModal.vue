<template>
    <!--定义模板-->
    <div class="modal fade" id="comments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <div>
                        <span>{{ pagination.total }}条评论</span>
                    </div>
                </div>
                <div class="modal-body answer-comment">
                    <div class="comment-body">
                        <div v-for="(comment,index) in comments">
                            <div class="row media">
                                <a class="pull-left" href="#"><img src="http://iph.href.lu/100x100" alt=""></a>
                                <div class="media-body">
                                <span class="media-heading">
                                    {{ comment.user.data.username }} <span v-if="comment.comment_user.is_answer_author">（作者）</span>
                                    <template v-if="comment.obj.username">
                                        <span class="comment-reply">回复</span>{{ comment.obj.username }} <span v-if="comment.obj.is_answer_author">（作者）</span>
                                    </template>
                                </span>

                                <span class="pull-right">
                                    {{ comment.created_at }}
                                </span>
                                </div>
                            </div>
                            <div class="row">
                                {{ comment.content }}
                            </div>
                            <div class="row" v-if="!comment.start_reply">
                                <span>
                                    <button class="default-button">
                                        <i class="fa fa-thumbs-up"></i>
                                        {{ comment.likes_count }}
                                    </button>
                                    <button class="default-button"
                                        @click="comment.start_reply = !comment.start_reply"
                                    >
                                        <i class="fa fa-reply"></i>
                                        回复
                                    </button>
                                    <button class="default-button"
                                            v-if="comment.is_author || comment.is_answer_author"
                                            @click="delComment(comment.id)"
                                    >
                                        <i class="fa fa-trash"></i>
                                        删除
                                    </button>
                                </span>
                            </div>
                            <div class="row" v-else>
                                <div>
                                    <textarea rows="1" class="form-control" :placeholder="'回复'+ comment.user.data.username"
                                              v-model="comment.reply_content"
                                    ></textarea>
                                </div>
                                <div style="margin-top:15px">
                                    <div class=" pull-right">
                                        <a href="" @click.prevent="comment.start_reply = !comment.start_reply"  style="margin-right: 15px">取消</a>
                                        <button class="btn btn-primary"
                                                :class="{ disabled:!comment.reply_content }"
                                                @click="createComment(comment.reply_content, comment.id, comment.user.data.username)"
                                        >评论</button>
                                    </div>
                                </div>
                            </div>

                            <hr>
                        </div>
                        <!--分页区域-->
                    </div>
                    <div v-if="!pagination.total">
                        <b>暂时还有评论,快来占个沙发吧!</b>
                    </div>
                    <!--每页记录数大于总记录数时才需要显示分页-->
                    <div class="comment-footer text-center"
                        v-if="pagination.total_pages > 1"
                    >
                        <ul class="pagination">
                            <!--当prev_page_url不存在(false) 时我希望的时,不显示,disabled为true-->
                            <li
                                    @click="getComments(pagination.links.prev)"
                                    :class="{disabled:!pagination.links.prev}"
                            >
                                <a href="javascript:void(0)" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <li v-for="n in pagination.total_pages"
                                @click="getComments('/api/answers/'+answer_id+'/comments?page='+n)"
                                :class="{active:pagination.current_page == n}"
                            ><a href="javascript:void(0)" >{{ n }}</a></li>

                            <li
                                    @click="getComments(pagination.links.next)"
                                    :class="{disabled:!pagination.links.next}"
                            >
                                <a href="javascript:void(0)" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="modal-footer">
                   <div class="col-md-10">
                       <!--<input type="text" class="form-control">-->
                       <textarea rows="1" class="form-control" placeholder="写下你的评论"
                            v-model="content"
                       ></textarea>
                   </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block" :class="{ disabled:!content }"
                            @click="createComment(content)"
                        >提交</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
</template>
<script>
    /*组件选项定义,包括data,methods,等*/
    export default {
        name: '',
        data () {
            return {
                content : null,
                answer_id : null,
                comments : [],
                pagination : {},
                is_auth : false,
            }
        },
        created(){
            //设置comment高度
            //windows的高度
        },
        mounted(){ //挂载完成,可以理解为已经初步渲染完毕
            this.setModalHeight();
        },
        methods : {
            //showModal方法 -> get数据
            showModal(answer_id){
                 this.answer_id = answer_id;
                //打开modal
                $('#comments').modal('show');
                this.getComments();
            },
            delComment(comment_id){
                swal({
                        title: "你确定要删除这条评论吗？",
                            /* type: "info",*/
                        showCancelButton: true,
                        confirmButtonColor: "#3097D1",
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        closeOnConfirm: true,
                    },
                    () => {
                        axios.delete('/api/answers/'+this.answer_id+'/comments/'+comment_id)
                            .then(response => {
                                if(response.status == 204){
                                    swal({
                                        title: "评论已删除",
                                        timer: 800,
                               /*         type: "success",*/
                                        showConfirmButton: false
                                    });
                                    this.getComments(sessionStorage.getItem('comment_url'));
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
            getComments(url = '/api/answers/'+this.answer_id+'/comments'){
                //url存一下缓存
                sessionStorage.setItem('comment_url', url);
                 axios.get(url, {
                 })
                 .then((response)=> {
                     //data存储
                 	 this.comments = response.data.data;
                     //分页信息存储
                     this.pagination = response.data.meta.pagination;
                     //是否登陆
                     this.is_auth = response.data.meta.is_auth;

                 })
                 .catch(error=> {
                 	console.log(error);
                 });
            },
            createComment(content, obj_comment_id = null, obj_username = null){
                if(content && this.is_auth){
                     axios.post('/api/answers/'+this.answer_id+'/comments', {
                      	'content' : content,
                         'obj_comment_id' :  obj_comment_id,
                         'obj_username' : obj_username
                     })
                     .then((response)=> {
                         if(response.status == 200 && response.data.comment.id){
                             this.content = null;
                             this.getComments();
                             swal({
                                 title: "评论成功",
                                 timer: 1000,
                                 type: "success",
                                 showConfirmButton: false
                             });
                             /*//滚动条回到最上面
                              $('.answer-comment').prop('scrollTop',0);*/
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
            setModalHeight(){
                //设置回答modal的高度
                let height = $(window).height();
                $('.answer-comment').css("max-height", height-65-50-70);
            },
        }
    }
</script>
<style>
    .comment-body img {
        width: 25px;
        height :25px;
    }
    .comment-body .comment-reply {
        color: #8590a6;
        margin-right: 8px;
        margin-left: 8px;
    }
    #comments .answer-comment {
        overflow-y:auto;
    }
</style>