<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 判断当前用户是否是这条评论的作者
     * 模型存在值后才能调用该方法
     * @return bool
     */
    public function isAuthor()
    {
        if( !\Auth::check()){
            return false;
        }

        return $this->user_id == \Auth::user()->id;
    }

    public function getObjUserName($obj_user_id)
    {
        if(!$obj_user_id){
            return null;
        }
        $user = User::find($obj_user_id);
        return $user->username;
    }

    /**
     *判断该用户是否是这个答案的作者
     */
    public function isAnswerAuthor($answer_id)
    {
        if( !\Auth::check()){
            return false;
        }
        $answer = Answer::find($answer_id);
        return $answer->user_id == \Auth::user()->id;
    }
}
