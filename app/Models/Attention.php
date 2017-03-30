<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attention extends Model
{
    protected $guarded = []; //黑名单,  守卫者

    /**
     * 用户关注该话题
     * @param $user_id
     * @param $attention_id
     * @param $attention_type
     * @return bool
     * @internal param $follow_id $question_id or $topic_id:
     * @internal param $follow_type 0:话题 1:问题 2:用户
     */
    public function attention($user_id, $attention_id, $attention_type)
    {
        //添加数据
        $res = $this->create([
            'user_id' => $user_id,
            'attention_id' => $attention_id,
            'attention_type' => $attention_type
        ]);
        //该类型表的attentions_counts字段自增1
        $this->followersCountChange($attention_id, $attention_type, 1);
        return $res;
    }


    /**
     * 例: 用户取消对该话题的关注
     * @param $user_id
     * @param $attention_id
     * @param $attention_type
     * @return mixed
     */
    public function noAttention($user_id, $attention_id, $attention_type)
    {
        $res = $this->where([
            ['user_id', $user_id],
            ['attention_id', $attention_id],
            ['attention_type', $attention_type]
        ])->delete();
        //该类型表的attentions_counts字段减1
        $this->followersCountChange($attention_id, $attention_type, -1);
        return $res;
    }

    /**
     * 例: 用户关注话题, 话题表中的 followers_count字段的值则增加1
     * @param $attention_id
     * @param $attention_type
     * @param $count
     * @return null
     */
    public function followersCountChange($attention_id, $attention_type, $count)
    {
        switch ($attention_type)
        {
            case 0: //话题
                $ques = Topic::findOrFail($attention_id);
                $ques->followers_count = $ques->followers_count + $count;
                $ques->save();
                break;

            case 1: //问题
                $ques = Question::findOrFail($attention_id);
                $ques->followers_count = $ques->followers_count + $count;
                $ques->save();
                break;

            case 2: //用户
                $ques = User::findOrFail($attention_id);
                $ques->followers_count = $ques->followers_count + $count;
                $ques->save();
                break;

            default:
                return null;
        }

    }
}
