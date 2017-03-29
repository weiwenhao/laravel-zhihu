<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\Question;

class QuestionController extends Controller
{

    public function store(QuestionRequest $request)
    {
        //插入数据库
        $question = Question::create(array_merge($request->all(),['user_id' => auth()->user()->id]));
        //中间表的插入
        $res = $question->topics()->attach($request->topic_ids);
        return $question;
    }
}
