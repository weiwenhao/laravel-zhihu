<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attention extends Model
{
    protected $guarded = []; //黑名单,  守卫者

    /**
     * 用户关注
     * @param $user_id
     * @param $attention_id
     * @param $attention_type
     * @return bool
     * @internal param $follow_id $question_id or $topic_id:
     * @internal param $follow_type 0:话题 1:问题 2:用户
     */
    public function attention($user_id, $attention_id, $attention_type)
    {
        //判断该用户是否已经关注过该问题
        $count  = $this->where([
            ['user_id',$user_id],
            ['attention_id',$attention_id],
            ['attention_type',$attention_type],
        ])->count();
        if ($count > 0)
            return false;
        //添加数据
        $res = $this->create([
            'user_id' => $user_id,
            'attention_id' => $attention_id,
            'attention_type' => $attention_type
        ]);
        //该类型表的attentions_counts字段自增1
        $this->attention_inc($attention_id, $attention_type);
        return $res;
    }

    public function attention_inc($attention_id, $attention_type)
    {
        switch ($attention_type)
        {
            case 0: //话题
                $ques = Topic::findOrFail($attention_id);
                $ques->followers_count ++;
                $ques->save();
                break;

            case 1: //问题
                $ques = Question::findOrFail($attention_id);
                $ques->followers_count ++;
                $ques->save();
                break;

            case 2: //用户
                $ques = User::findOrFail($attention_id);
                $ques->followers_count ++;
                $ques->save();
                break;

            default:
                return null;
        }

    }
}
