<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class RoleController extends Controller
{
    protected $permission;
    /**
     * @var
     */
    private $role;

    /**
     * RoleController constructor.
     * @param PermissionRepository $permission
     * @param RoleRepository $role
     */
    public function __construct(PermissionRepository $permission, RoleRepository $role)
    {
        $this->permission = $permission;
        $this->role = $role;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.role.list');
    }

    /**
     * datatables数据源
     * @return mixed
     */
    public function dtRoles()
    {
        return Datatables::of($this->role->all())->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perms = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        return view('admin.role.create',compact('perms'));
    }

    /**
     * Store a newly created resource in storage.
     * @param RoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = $this->role->create($request->all());
        if (!$role){
            return redirect('/admin/role/create')->withError('添加失败')->withInput();
        }
        $role->perms()->attach($request->get('perm_ids'));
        return redirect('/admin/role')->withSuccess('添加成功');
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
        $perms = $this->permission->getPermList(['id', 'display_name', 'parent_id']);
        $role = $this->role->find($id);
        $perm_ids = array_column($role->perms->toArray(),'id');
        return view('admin.role.edit',compact('role','perms','perm_ids'));
    }

    /**
     * Update the specified resource in storage.
     * @param RoleRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = $this->role->update($request->all(),$id);
        $role->perms()->sync($request->get('perm_ids',[]));
        if(!$role)
            return redirect('/admin/role/'.$id.'/edit')->withError('修改失败')->withInput();
        return redirect('/admin/role')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = $this->role->delete($id);
        return $res;
    }
}
