@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <hr>
            <div class="panel panel-default">
                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
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
@endsection
@section('js')
<script>
    new Vue({
        el : '#app',
        created(){

//            localStorage.removeItem('jwt_token')
            //请求测试
            axios.get('/api/question/3', {

            })
            .then(response=> {
                console.log(response);

            })
            .catch(error=> {
                console.log(error.response);
            });
        }
    });
</script>
@stop
