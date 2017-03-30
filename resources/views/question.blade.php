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
        <input type="hidden" name="id" value="{{ $question->id }}">
        <div class="container">
            <div class="row" style="margin-top: 15px">
                <div class="col-md-5">
                    @foreach($question->topics as $topic)
                        <div class="Tag" @onclik='showTopic({{ $topic->id }})'>
                            <a href="{{ url('/topic/'.$topic->id) }}">{{ $topic->name }}</a>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-3 col-md-offset-4">
                   <span>
                       <a href="">关注者
                           <span id="followers-count">{{ $question->followers_count }}</span>
                       </a>
                   </span>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                   <span>被浏览 {{ $question->browses_count }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <article class="post">
                        <header class="post-header">
                            <h2 class="post-title">{{ $question->title }}</h2>
                        </header>
                        <section class="post-excerpt">
                            <p>
                                {!! $question->content !!}
                                <a href="" class="pull-right">展示全部  <i class="fa fa-angle-down"></i></a>
                            </p>
                        </section>
                    </article>
                </div>
            </div>
            <div class="row" style="margin-bottom: 15px">
                <div class="col-md-2">
                    @if(auth()->user()->id == $question->user_id)
                        <span style="font-size: 16px">
                            <a href="javascript:void(0);">
                                <i class="fa fa-pencil"></i> 修改问题
                            </a>
                        </span>
                    @endif
                </div>
                <div class="col-md-3 col-md-offset-7">
                    <button class="btn" :class="[is_attention ? 'btn-info':'btn-primary' ]"
                        @click='attention({{ $question->id }})'
                    >
                        @{{ attention_msg }}
                    </button>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    <button class="btn btn-default">
                        <i class="fa fa-pencil"></i>   写回答
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script>
	new Vue({
	    el:'#question',
        data : {
            is_attention : false,
        },
        created(){
            this.getAttentionStatus();
        },
        computed : {
            attention_msg(){
                if( this.is_attention)
                    return '取消关注'
                return '关注问题'
            }
        },
        methods : {
            /*
            * 判断用户是否关注了问题
            * */
            getAttentionStatus(){
                let question_id = $('[name=id]').val();
                axios.get('/question/'+question_id+'/is_attention', {
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
            attention(question_id){
                //关注者数字-1或者+1
                this.incOrDec();
                //直接更改状态
                this.is_attention = !this.is_attention;
                //关注或取消关注问题开关
                 axios.post('/question/attention', {
                  	'question_id': question_id
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
            incOrDec(){
                //is_attention == true , -1
                let att_count = $('#followers-count').text() * 1;
                if(this.is_attention){
                    att_count --;
                }else {
                    att_count ++
                }
                //赋值
                $('#followers-count').text(att_count);
            }

        }
    });
</script>
@stop