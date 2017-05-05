<?php

namespace App\Transformers;

use App\Models\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {
    protected $availableIncludes = [];
    protected $defaultIncludes = ['user']; //user,和 单个answer同级
    public function transform(Comment $model)
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
            'likes_count' => (int)$model->likes_count,
            'created_at' => $model->created_at->diffForHumans(),
            'is_author' => $model->isAuthor(),
            'is_answer_author' => $model->isAnswerAuthor(),
            'comment_user' => $model->getCommentUserInfo(),
            'obj' => $model->getObjInfo($model->obj_username),
            'start_reply' => false,
            'reply_content' => null,
        ];
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }
}