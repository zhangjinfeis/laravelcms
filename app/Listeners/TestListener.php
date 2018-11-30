<?php

namespace App\Listeners;

use App\Events\TestEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Article;

class TestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TestEvent  $event
     * @return void
     */
    public function handle(TestEvent $event)
    {
        //
        //Article::create(['body'=>$event->name,'cate_id'=>2,'title'=>'ss']);

    }
}
