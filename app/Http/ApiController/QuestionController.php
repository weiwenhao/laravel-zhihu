<?php

namespace App\Http\ApiController;

use App\Models\Question;
use App\Transformers\QuestionTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class QuestionController extends BaseController
{
    protected $question;

    /**
     * QuestionController constructor.
     * @param $question
     */
    public function __construct(Question $question)
    {
        $this->question = $question;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = $this->question->paginate();
        return $this->response->paginator($res, new QuestionTransformer());
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        // JWTAuth ...  jwt原生..难记忆
        try{
            $res = $this->question->findOrFail($id);
        }catch (ModelNotFoundException $e){ //上面的方法会触发
            $this->response()->errorBadRequest();
        }

        //中间表数据,给标签用
        $res->topics = $res->topics; //关联关系
        $res->topic_ids = $res->topics->map(function ($topic){
            return $topic->id;
        });
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
