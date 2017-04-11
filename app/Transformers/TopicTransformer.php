<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class TopicTransformer extends TransformerAbstract {
    public function transform($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
        ];

        //这里要对topics进行处理

    }
}