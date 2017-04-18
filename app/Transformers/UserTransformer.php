<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract {
    public function transform(User $model)
    {
        return [
            'id' => $model->id,
            'username' => $model->username,
        ];

    }
}