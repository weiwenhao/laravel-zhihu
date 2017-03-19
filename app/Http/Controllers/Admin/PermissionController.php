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
        return view('admin.permission.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perm_list = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        return view('admin.permission.create',compact('perm_list'));
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
        \Cache::forget('perm_list');
        if(!$res){
            return redirect('/admin/permission/create')->with('error','系统错误，添加失败')->withInput($request->all());
        }
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
