<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionRequest;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    protected $permission;

    /**
     * PermissionController constructor.
     * @param $permission
     */
    public function __construct(PermissionRepository $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perm_list = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        return view('admin.permission.list', compact('perm_list'));
    }

    /**
     * 得到嵌套的权限列表, 提供树形结构使用
     * @return mixed
     */
    public function getNestPermList()
    {
        $res = $this->permission->getNestPermList(['id', 'display_name', 'parent_id']);
        return $res;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perm_list = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        return view('admin.permission.create', compact('perm_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionRequest $request)
    {
        $res = $this->permission->create($request->all());
        if(!$res)
            return redirect('/admin/permission/create')->withInput()->with('error', '系统错误，添加失败');
        \Cache::forget('perm_list');
        return redirect('/admin/permission')->withSuccess('添加成功');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $perm_child_ids = $this->permission->getChildPermIds($id);
        $perm_list = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        $perm = $this->permission->find($id);
        return view('admin.permission.edit',compact('perm','perm_list','perm_child_ids'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionRequest $request, $id)
    {
        $res = $this->permission->update($request->all(), $id);
        if (!$res)
            return redirect('/admin/permission/'.$id.'/edit')->withInput()->withError('系统错误，修改失败');
        \Cache::forget('perm_list');//刷新缓存
        return redirect('/admin/permission')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $perm_child_ids = $this->permission->getChildPermIds($id);
        $res = $this->permission->delete($perm_child_ids); //返回删除的记录数
        if(!$res){
            return redirect('/admin/permission')->withError('删除失败');
        }
        \Cache::forget('perm_list');
        return redirect('/admin/permission')->withSuccess('删除成功');
    }
}
