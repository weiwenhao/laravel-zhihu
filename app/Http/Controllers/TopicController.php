<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Request;

class TopicController extends Controller
{
    /**
     * select2的ajax数据源
     */
    public function select_res()
    {
        $name = Request::get('name');
        $res = Topic::where('name','like',"%$name%")->limit(10)->select('id','name as text')->get();
        return $res;
    }
}
