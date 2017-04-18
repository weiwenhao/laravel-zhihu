<?php

namespace App\Transformers;

use App\Models\Answer;
use League\Fractal\TransformerAbstract;

class AnswerTransformer extends TransformerAbstract {
    protected $availableIncludes = [];
    protected $defaultIncludes = ['user']; //user,和 单个answer同级
    public function transform(Answer $model)
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
            'likes_count' => (int)$model->likes_count,
            'comments_count' => (int)$model->comments_count,
            'is_comment' => (bool) $model->is_comment,
            'is_author' => $model->isAuthor(),
            'created_at' => $model->renderDate($model->created_at),
        ];
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }
}