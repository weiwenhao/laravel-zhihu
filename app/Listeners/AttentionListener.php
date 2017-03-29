<?php

namespace App\Listeners;

use App\Events\CreateQuestionEvent;
use App\Models\Attention;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttentionListener
{
    /**
     * @var Attention
     */
    private $attention;

    /**
     * Create the event listener.
     *
     * @param Attention $attention
     */
    public function __construct(Attention $attention)
    {
        //
        $this->attention = $attention;
    }

    /**
     * Handle the event.
     *
     * @param  CreateQuestionEvent  $event
     * @return void
     */
    public function handle(CreateQuestionEvent $event)
    {
         //关注事件  -> 隶属于attentions表
        $this->attention->attention($event->question->user_id, $event->question->id, 1);//1代表问题
    }
}
