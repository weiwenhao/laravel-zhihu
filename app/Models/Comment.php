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

    /**
     *判断该用户是否是这个答案的作者
     */
    public function isAnswerAuthor($user_id = null)
    {
        if(!$user_id){
            if( !\Auth::check()){
                return false;
            }
            $user_id = \Auth::user()->id;
        }
        $answer = Answer::where('id', $this->answer_id)->first();
        return $answer->user_id == $user_id;
    }

    public function getObjInfo($obj_username)
    {
        if(!$obj_username){
            return [
                'obj_username' => $obj_username,
                'is_answer_author' => false,
            ];
        }
        $obj_user = User::where('username', $obj_username)->first();
        return [
            'username' => $obj_username,
            'is_answer_author' => $this->isAnswerAuthor($obj_user->id)
        ];
    }

    public function getCommentUserInfo(){
        return [
            'is_answer_author' => $this->isAnswerAuthor($this->user_id, $this->answer_id)
        ];
    }
}
