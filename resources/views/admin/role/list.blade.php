@extends('admin.layouts.layout')
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
    <style>

    </style>
@stop
@section('content')
    <!-- Main content -->
    <section class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> {{ session('success') }}</h4>
            </div>
        @elseif(session('error'))
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> {{ session('error') }}</h4>
            </div>
        @endif
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">角色列表</h3>

                <div class="box-tools pull-right">
                    <div class="box-tools pull-right">
                        <a href="{{ url('/admin/role/create') }}" class="btn bg-olive" title="Collapse">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table id="roles" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>角色值</th>
                        <th>角色名称</th>
                        <th>描述</th>
                        <th>创建时间</th>
                        <th>修改时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@stop
@section('js')
    {{--datatables--}}
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script>
        /**
         * datatables配置
         * @type {jQuery}
         */
        var table = $('#roles').DataTable( {
            stateSave: false,//保存当前页面状态,再次刷新进来依旧显示当前状态,比如本页的排序规则,显示记录条数
            language: {
                "sProcessing": "处理中...",
                "sLengthMenu": "每页显示 _MENU_ 条记录",
                "sZeroRecords": "没有匹配结果",
                "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
                "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
                "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix": "",
                "sSearch": "搜索:",
                "sUrl": "",
                "sEmptyTable": "表中数据为空",
                "sLoadingRecords": "载入中...",
                "sInfoThousands": ",",
                "oPaginate": {
                    "sFirst": "首页",
                    "sPrevious": "上页",
                    "sNext": "下页",
                    "sLast": "末页"
                },

            }, //语言国际化
            "order": [[ 0, "desc" ]],
            "serverSide": true,//开启服务器模式
            "searchDelay": 1000, //搜索框请求间隔
            // "lengthMenu": [15,25,50], //自定义每页显示条数菜单

            "search" : {
                "regex": true  //正则搜索还是精确搜索
            },
            "ajax": {
                "url" : '/admin/role/dt_roles',
            },
            "columns": [
                {
                    'data':'id', //对应json中的字段
                },
                {
                    'data':'name',
                },
                {
                    'data':'display_name',
                    "orderable" : false,
                },
                {
                    'data':'description',
                    "orderable" : false,
                    render : function (data, type, row, meta) {
                        if(data){
                            return '<div style="width:100px;overflow: hidden;white-space:nowrap;text-overflow: ellipsis;">'+data+'</div>';
                        }
                        return '暂无';
                    }
                },
                {
                    'data':'created_at'
                },
                {
                    'data':'updated_at'
                },
                {
                    searchable: false,
                    'data' : null, //对应服务器端
                    "orderable" : false, //是否开启排序
                    'width' : '20%',
                    render: function(data, type, row, meta) {
                        return "<a href='/admin/role/"+row.id+"/edit' class='btn btn-xs btn-info edit'><i class='fa fa-edit'></i>  修改</a>  " +
                            "<button value="+row.id+" class='btn btn-xs btn-danger del'><i class='fa fa-trash'></i>  删除</button>";
                    }
                }

            ],

        });
        /**
         * ajax删除
         */
        $.ajaxSetup({ //这段话的意思使用ajax,会将csrf加入请求头中
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('body').on('click', 'button.del', function() {
            var url = '/admin/role/'+$(this).val(); //this代表删除按钮的DOM对象
            if( !confirm('你确定要删除该角色吗?')){
                return false;
            }
            $.ajax({
                type: "DELETE",
                url: url,
                success: function(data){
                    if (data){
                        //刷新dt
                        table.ajax.reload(null, false); //databales对象从新加载
                        alert('删除成功');
                    }
                }
            });
        });
    </script>
@stop