<?php

namespace App\Console;

use App\Models\Answer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Answer::create([
                'content' => '每天10:37分执行的任务',
                'user_id' => Answer::max('user_id')+1,
                'question_id' => 1,
            ]);
        })->dailyAt('10:37');

        // ->cron('* * * * * *') 任务调度的最小单位是分钟, 这里是通配符,则表示,该命令每分钟都会被执行一次;
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
