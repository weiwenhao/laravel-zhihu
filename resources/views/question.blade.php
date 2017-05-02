@extends('layouts.app')
@section('css')
<style>

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
	new Vue({
	    el:'#app',
        data : {
        },
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