<?php

namespace App\Http\ApiController;

use App\Http\Requests\QuestionRequest;
use App\Models\Attention;
use App\Models\Question;
use App\Models\Topic;
use App\Transformers\QuestionTransformer;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuestionController extends BaseController
{
    protected $question;
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
        $this->middleware('api.auth')->except(['show']);
        $this->question = $question;
        $this->attention = $attention;
        $this->topic = $topic;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuestionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionRequest $request)
    {
        //插入数据库
        $question = $this->question->create(array_merge($request->all(),['user_id' => auth()->user()->id]));
        //话题问题表中间表的插入(无返回)
        $question->topics()->attach($request->get('topic_ids'));
        //自己关注自己的问题
        $this->attention->attention($question->user_id, $question->id, 1);
        //话题表的该问题的个数+1
        // todo 如果修改问题,则话题表的问题个数-1 ? 如何做? 取出修改前该问题对应的话题个数-1再+1? 还是中间表插入成功后进行一个统计?
        // 统计也需要修改前的统计,删除前的统计
        // 便捷的做法应该是.  中间表查询统计,并对  取出结果进行缓存? 是否还需要topic表中的questions_count字段?
        /*$question->topics->map(function ($item){
            //每个topic的followers_ids自动增加1
            $topic = $this->topic->find($item->id);
            if(is_null($topic)){
                throw new NotFoundHttpException();
            }
            $topic->questions_count ++;
            $topic->save();
        });*/
        return $this->response->created('/questions/'.$question->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        dd($this->auth->user());   dingo/api提供的方法  -> 推荐,更加具有通用性
        // \Auth::guard('api')->user() //通过jwt提供的 guard使用 laravel的方法得到,更加容易记忆
        try{
            $res = $this->question->findOrFail($id);
        }catch (ModelNotFoundException $e){ //未找到则触发该异常
            $this->response()->errorNotFound();
        }

        //中间表数据,给标签用
        $res->topics = $res->topics; //关联关系
        $res->topic_ids = $res->topics->map(function ($topic){
            return $topic->id;
        });
        /**
         * 认证部分
         */
        //是否关注
        $res->is_attention = $this->isAttention($id);
        //是否是作者
        $res->is_author = $this->isAuthor($id);
        return $this->response->item($res, new QuestionTransformer());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  z$id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QuestionRequest $request
     * @param  int $id
     * @return int  0 or 1
     */
    public function update(QuestionRequest $request, $id)
    {
        try{
            $question = $this->question->findOrFail($id);
        }catch (ModelNotFoundException $e){ //未找到则触发该异常
            $this->response()->errorNotFound();
        }
        $res = $question->update($request->all()); //true和false
        //中间表更新
        $question->topics()->sync($request->topic_ids);
        return (int) $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int  0 or 1
     */
    public function destroy($id)
    {
        try{
        	$question = $this->question->findOrFail($id);
        }catch (ModelNotFoundException $e){ //未找到则触发该异常
        	$this->response()->errorNotFound('资源不存在');
        }
        //判断是否为文章所有者
        if( !$this->isAuthor($id) ){
            return $this->response->errorForbidden('您不是该问题的所有者');
        }
        //清空话题-问题表数据
        $question->topics()->sync([]);
        $res = $question->delete();
        /*
         * todo delQuestion
         * 删除该问题下的答案
         * 删除答案表下收藏了该答案的收藏夹
         *
         * 删除评论
         *
         * 话题表的问题个数-1
         * 关注表中删除 attention_type=1 && attention_id = question_id的数据
         *
         */
        return (int) $res;
    }

    /**
     * 判断当前用户是否关注了该问题
     * @param $id  问题id
     * @return int 0 or 1
     */
    private function isAttention($id)
    {
        if(!Auth::check()){
            return (int) false;
        }
        $user_id = Auth::user()->id;
        $count = $this->attention->where([
            ['user_id', $user_id],
            ['attention_id', $id],
            ['attention_type', 1]
        ])->count();
        return (int) ($count > 0);
    }

    /**
     * 判断当前用户是否是该问题的作者
     * @param $id   问题id
     * @return int  0 or 1
     */
    private function isAuthor($id)
    {
        if(!Auth::check()){
            return (int) false;
        }
        $question = $this->question->findOrFail($id);
        $user_id = Auth::user()->id;
        return (int) ($question->user_id == $user_id);
    }

    public function attention($id)
    {
        $user_id = Auth::user()->id;
        //如果查出来一条数据,那就删除,如果没有查出来则添加
        $count = $this->attention->where([
            ['user_id', $user_id],
            ['attention_id', $id],
            ['attention_type', 1]
        ])->count();
        if ($count){
            //取消关注问题
            $this->attention->noAttention($user_id, $id, 1);
        } else{
            //关注问题
            $this->attention->attention($user_id, $id, 1);
        }
    }
}
