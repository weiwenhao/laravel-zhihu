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
            'obj_comment_id' => $request->get('obj_comment_id'),
            'obj_username' => $request->get('obj_username')
        ]);
        return $res;
    }

















    public function destroy($answer_id, $comment_id)
    {
        //自己只能删除自己的回答
        $comment = Comment::find($comment_id);
        $answer = Answer::find($answer_id);
        if(!$comment){
            throw new NotFoundHttpException('资源不存在'); //404相应
        }
        $user_id = \Auth::user()->id;
        //不是该评论的作者, 并且也不是该答案的作者,则不能删除这条评论
        if($user_id !== $comment->user_id && $user_id !== $answer->user_id){
            throw new AccessDeniedHttpException(); //403响应
        }
        $comment->delete();
        return $this->response->noContent(); //无内容响应
    }
}
