<?php

namespace App\Http\ApiController;

use App\Http\Requests\AnswerRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use App\Transformers\AnswerTransformer;
use App\Transformers\CommentTransformer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['index']); //中间件
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($answer_id)
    {
        //取出答案,然后使用答案关联关系
        try{
        	$answer = Answer::findOrFail($answer_id);
        }catch (ModelNotFoundException $e){ //未找到则触发该异常
        	$this->response()->errorNotFound();
        }
        //该问题下的评论数量
        $comments = $answer->comments()->orderBy('created_at', 'desc')->paginate(10);
        return $this->response->paginator($comments, new CommentTransformer())
            ->setMeta([
                'is_auth'=> \Auth::check(),
            ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CommentRequest $request
     * @param $answer_id
     * @return \Illuminate\Http\Response
     * @internal param $question_id
     */
    public function store(CommentRequest $request, $answer_id)
    {
        $res = Comment::create([
            'content' => $request->get('content'),
            'answer_id' => $answer_id,
            'user_id' => \Auth::user()->id,
            'obj_comment_id' => $request->get('obj_comment_id')
        ]);
        return $res;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }
    /**
     * Update the specified resource in storage.
     *
     * @param AnswerRequest|\Illuminate\Http\Request $request
     * @param $question_id
     * @param $answers_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(AnswerRequest $request, $question_id, $answers_id)
    {
        $answer = Answer::find($answers_id);
        if(!$answer){
            throw new NotFoundHttpException('资源不存在'); //404相应
        }

        $answer->content = $request->get('content');
        $answer->save();
        return $this->response->item($answer, new AnswerTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $question_id
     * @param $answers_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($question_id,$answers_id)
    {
        //自己只能删除自己的回答
        $answer = Answer::find($answers_id);
        if(!$answer){
            throw new NotFoundHttpException('资源不存在'); //404相应
        }
        if(\Auth::user()->id !== $answer->user_id){
            throw new AccessDeniedHttpException(); //403响应
        }
        $answer->is_deleted = 1; //1表示true 已经删除
        $answer->save();
        return $this->response->noContent(); //无内容响应
    }

    /**
     * 撤销对该问题的删除
     * @param $question_id
     * @return \Dingo\Api\Http\Response
     */
    public function cancelAnswer($question_id){
        $user_id =  \Auth::user()->id;
        $answer = Answer::where('question_id', $question_id)->where('user_id', $user_id)->first();
        if(!$answer){
            throw new NotFoundHttpException('资源不存在');
        }
        $answer->is_deleted = 0; //0表示不删除
        $answer->save();
        return $this->response->noContent(); //无内容响应
    }

    public function userInfo($question_id){
        $user = \Auth::user();
        //查找答案表中, 寻找question_id,user_id ->找一条这样的记录,找到则回答过了
        $answer = Answer::where('question_id', $question_id)->where('user_id', $user->id)->first();
        //必须在answer存在的情况下才能判断是否删除了答案,否则给is_deleted一个默认值false->没有删除该答案
        $answer?$is_deleted = $answer->is_deleted : $is_deleted=false;  //todo该判断如何优化
        return [
            'data' => $user,
            'is_answered' => !empty($answer),
            'is_deleted' => (bool) $is_deleted
        ];
    }
}
