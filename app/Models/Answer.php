<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['content','user_id','question_id'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAnswerApi($question_id, $page_size, $order)
    {
        //排序处理
        $sort_filed = substr($order,0,strrpos($order,'_')); //从右边寻找'_' 线
        $sort_order = substr($order,strrpos($order,'_')+1);
        //当前用户
        $request_user_id = request()->user()->id;
        //先limit取出
        $answers = $this->where('question_id', $question_id)
            ->orderBy($sort_filed, $sort_order)
            ->limit($page_size)->get();
        //数据处理
        $answers->transform(function ($answer) use ($request_user_id){
            //todo think:只要使用到了关联关系($answer->user) ,那么关联数据就会被赋值在模型中
            $answer->is_auth = $answer->user->id == $request_user_id;
            //时间处理
            $answer->renderDate = $this->renderDate($answer->created_at);
            return $answer;
        });
        return $answers;
    }

    public function renderDate($data)
    {
        //如果发表时间在24小时之内则显示事件差,否则显示日期
        //now > 1 + create_dt 表表示已经超过一天了
        if ($data->isYesterday() ){
            //超过一天
            return $data->toDateString();
        }
        return $data->diffForHumans();
    }
}
