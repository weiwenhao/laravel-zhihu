@extends('admin.layouts.layout')
@section('css')
<style>

</style>
@stop
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Title</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                        <i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <el-tree
                        :data="data2"
                        :props="defaultProps"
                        node-key="id"
                            default-expand-all>
                </el-tree>

            </div>
            <!-- /.box-body -->
            <!-- /.box-footer-->
        </div>
    </section>
    <!-- /.content -->
@stop
@section('js')
<script>
    var app = new Vue({
        el: '#app',
        data : {
            data2: [{
                id: 1,
                label: '一级 1',
                children: [{
                    id: 4,
                    label: '二级 1-1',
                    children: [{
                        id: 9,
                        label: '三级 1-1-1'
                    }, {
                        id: 10,
                        label: '三级 1-1-2'
                    },{
                        id: 11,
                        label: '测试'
                    }]
                }]
            }],
            defaultProps: {
                children: 'children',
                label: 'label'
            }
        }
    });
</script>
@stop