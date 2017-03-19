<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Eloquent\Repository;

class PermissionRepository extends Repository
{
    /**
     * Specify Model class name   该方法返回需要实例化的模型的完全限定名称
     * return  App/Models/User::class
     * @return mixed
     */
    public function modelName()
    {
        return Permission::class;
    }

    public function getPermList($columns=['*'])
    {
        //在缓存中去除perm_list,如果不存在则讲return的结果存入到缓存中,并返回
        $res = \Cache::remember('perm_list',60,function () use ($columns) {
           return  $this->sortPermList($this->model->get($columns));
        });
        return $res;
    }

    public function sortPermList($perm_list, $parent_id=0, $level=0)
    {
        $newArray = [];
        foreach ($perm_list as $key => $value){
            if($value->parent_id == $parent_id){
                $value->level = $level;
                $newArray[] = $value;
                $newArray = array_merge($newArray, $this->sortPermList($perm_list,$value->id,$level+1));
            }
        }
        return $newArray;
    }
}