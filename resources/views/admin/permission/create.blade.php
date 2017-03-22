@extends('admin.layouts.layout')
@section('css')
<style>

</style>
@stop
@section('content')
    <!-- Main content -->
    <section class="content">
        @if(session('error'))
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> {{ session('error') }}</h4>
            </div>
        @endif
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">添加权限</h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <form action="{{ url('/admin/permission') }}" method="post" class="form-horizontal">
            <div class="box-body">
                <div class="col-md-10 col-md-offset-1">
                        {{ csrf_field() }}
                        <div class="form-group {{ $errors->has('name')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">权限值</label>
                            <div class="col-md-6">
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="inputError" placeholder="例:  role.index   role.create   role.edit   role.destroy">
                                @if($errors->has('name'))
                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('display_name')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">*权限名称</label>
                            <div class="col-md-6">
                                <input type="text" name="display_name" value="{{ old('display_name') }}" class="form-control" id="inputError" placeholder="对应左侧菜单名称">
                                @if($errors->has('display_name'))
                                    <span class="help-block">{{ $errors->first('display_name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('parent_id')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">父级权限</label>
                            <div class="col-md-6">
                                <select name="parent_id" id="" class="form-control">
                                    <option value="0">请选择</option>
                                    @foreach($perm_list as $key => $value)
                                        <option value="{{ $value->id }}" {{ $value->id == old('parent_id')?'selected':'' }}>
                                            {{ str_repeat('- - - - | ',$value->level).$value->display_name }}
                                        </option>
                                    @endforeach
                                    @if($errors->has('parent_id'))
                                        <span class="help-block">{{ $errors->first('parent_id') }}</span>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('url')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">权限链接</label>
                            <div class="col-md-6">
                                <input type="text" name="url" value="{{ old('url') }}" class="form-control" id="inputError" placeholder="例: /role  二级权限才需要填写链接">
                                @if($errors->has('url'))
                                    <span class="help-block">{{ $errors->first('url') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('icon')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">权限图标</label>
                            <div class="col-md-6">
                                <input type="text" name="icon" value="{{ old('icon') }}" class="form-control" id="inputError" placeholder="例: home  请参照font-awesome图标表">
                                @if($errors->has('icon'))
                                    <span class="help-block">{{ $errors->first('icon') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('sort')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">权重</label>
                            <div class="col-md-6">
                                <input type="text" name="sort" value="{{ old('sort') }}" class="form-control" id="inputError" placeholder="">
                                @if($errors->has('sort'))
                                    <span class="help-block">{{ $errors->first('sort') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('description')?'has-error':'' }}">
                            <label class="col-md-3 control-label" for="inputError">描述</label>
                            <div class="col-md-6">
                                <textarea name="description" id="" cols="30" rows="3" class="form-control">{{ old('description') }}</textarea>
                                @if($errors->has('description'))
                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                        </div>

                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="col-md-2 col-md-offset-4">
                    <a href="{{ url('/admin/permission') }}" class="btn btn-block btn-default btn-flat">返回</a>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-block btn-primary btn-flat">提交</button>
                </div>
            </div>
            <!-- /.box-footer-->
            </form>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@stop
@section('js')
<script>
	
</script>
@stop