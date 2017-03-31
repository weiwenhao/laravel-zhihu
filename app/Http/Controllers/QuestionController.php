<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Models\Attention;
use App\Models\Question;
use App\Models\Topic;

class QuestionController extends Controller
{
    /**
     * @var Question
     */
    private $question;
    /**
     * @var Attention
     */
    private $attention;
    /**
     * @var Topic
     */
    private $topic;

    /**
     * QuestionController constructor.
     * @param Question $question
     * @param Attention $attention
     * @param Topic $topic
     */
    public function __construct(Question $question, Attention $attention, Topic $topic)
    {
//        $this->middleware('auth',['except'=>'isAttention']);
        $this->middleware('auth');
        $this->question = $question;
        $this->attention = $attention;
        $this->topic = $topic;
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
        //自己关注自己的问题
        $this->attention->attention($question->user_id, $question->id, 1);
        //话题表的该问题的个数+1
        $question->topics->map(function ($item){
            //每个topic的followers_ids自动增加1
            $topic = $this->topic->findOrFail($item->id);
            $topic->questions_count ++;
            $topic->save();
        });
        return $question;
    }

    public function show($id)
    {
        return view('question',compact('id'));
    }

    public function update(QuestionRequest $request, $id)
    {
        $question = $this->question->findOrFail($id);
        $bool = $question->update($request->all()); //true和false
        //中间表更新
        $question->topics()->sync($request->topic_ids);

        return (int) $bool;
    }
    public function apiQuestion()
    {
        $question_id = request('question_id');
        $res = $this->question->find($question_id);
        //中间表数据,给标签用
        $res->topics = $res->topics;
        $res->topic_ids = $res->topics->map(function ($topic){
            return $topic->id;
        });
        return $res;
    }
    /**
     *
     * @param $question_id
     * @return int 1 or 0
     */
    public function isAttention($question_id)
    {
        $user_id = auth()->user()->id;
        $count = $this->attention->where([
            ['user_id', $user_id],
            ['attention_id', $question_id],
            ['attention_type', 1]
        ])->count();
        return (int) ($count > 0);
    }

    public function attention()
    {
        $question_id = request('question_id');
        $user_id = auth()->user()->id;
        //如果查出来一条数据,那就删除,如果没有查出来则添加
         $count = $this->attention->where([
             ['user_id', $user_id],
             ['attention_id', $question_id],
             ['attention_type', 1]
         ])->count();
        if ($count){
            //取消关注问题
            $this->attention->noAttention($user_id, $question_id, 1);
        } else{
            //关注问题
            $this->attention->attention($user_id, $question_id, 1);
        }
    }

    public function isAuth()
    {
        $question = $this->question->find(request('question_id'));
        $user_id = auth()->user()->id;
        return (int) ($question->user_id == $user_id);
    }

    public function destroy($id)
    {
        $question = $this->question->findOrFail($id); //没找到抛出异常
        $res = $question->delete();
        //由于没有使用外键,所以要删除中间表的数据
        return (int)$res; //bool  0 or 1
    }
    
}
