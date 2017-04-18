<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract {
    protected $availableIncludes = [''];
    protected $defaultIncludes = ['topics']; //默认引入topic,和返回数据同级
    public function transform($model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
            'user_id' => $model->user_id,
            'followers_count' => $model->followers_count,
            'browses_count' => $model->browses_count,
            'topic_ids' => $model->topic_ids,
            'is_show_user' => (bool) $model->is_show_user,
            'is_attention' => (bool) $model->is_attention,
            'is_author' => (bool) $model->is_author
        ];
    }

    public function includeTopics($model)
    {
        return $this->collection($model->topics, new TopicTransformer());
    }
}