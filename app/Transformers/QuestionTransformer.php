<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract {
    protected $availableIncludes = [''];
    protected $defaultIncludes = ['topics'];
    public function transform($model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
            'user_id' => $model->user_id,
            'followers_count' => $model->followers_count,
            'is_show_user' => (bool) $model->is_show_user,
//            'created_at' => $model->created_at->toDateTimeString(),
            'topic_ids' => $model->topic_ids,
        ];

        //这里要对topics进行处理

    }

    public function includeTopics($model)
    {
        return $this->collection($model->topics, new TopicTransformer());
    }
}