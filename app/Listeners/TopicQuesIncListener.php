<?php

namespace App\Listeners;

use App\Events\CreateQuestionEvent;
use App\Models\Topic;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Request;

class TopicQuesIncListener
{
    /**
     * @var Topic
     */
    private $topic;

    /**
     * Create the event listener.
     *
     * @param Topic $topic
     */
    public function __construct(Topic $topic)
    {
        //
        $this->topic = $topic;
    }

    /**
     * Handle the event.
     *
     * @param  CreateQuestionEvent  $event
     * @return void
     */
    public function handle(CreateQuestionEvent $event)
    {
        /*dd(\Request::get('topic_ids')); //ok*/
        $event->question->topics->map(function ($item){
            //每个topic的followers_ids自动增加1
            $topic = $this->topic->findOrFail($item->id);
            $topic->questions_count ++;
            $topic->save();
        });
    }
}
