<?php

namespace App\Http\Controllers;

use App\Events\CreateQuestionEvent;
use App\Http\Requests\QuestionRequest;
use App\Models\Attention;
use App\Models\Question;

class QuestionController extends Controller
{
    protected $attention;
    /**
     * @var Question
     */
    private $question;

    /**
     * QuestionController constructor.
     * @param Attention $attention
     * @param Question $question
     */
    public function __construct(Attention $attention, Question $question)
    {
        $this->middleware('auth');
        $this->attention = $attention;
        $this->question = $question;
    }

    /**
     * 问题存储
     * @param QuestionRequest $request
     * @return mixed 问题模型
     */
    public function store(QuestionRequest $request)
    {
        //插入数据库
        $question = $this->question->create(array_merge($request->all(),['user_id' => auth()->user()->id]));
        //中间表的插入(无返回)
        $question->topics()->attach($request->get('topic_ids'));
        //触发创建问题事件
        event(new CreateQuestionEvent($question));
        return true;
    }
    
}
