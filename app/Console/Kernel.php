<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Not installed yet
        if (!env('APP_INSTALLED')) {
            return;
        }

        $schedule_time = env('APP_SCHEDULE_TIME', '09:00');

        $schedule->command('reminder:invoice')->dailyAt($schedule_time);
        $schedule->command('reminder:bill')->dailyAt($schedule_time);
        $schedule->command('recurring:check')->dailyAt($schedule_time);
        $schedule->command('MoneyBird:import-contact')->dailyAt($schedule_time);

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');

        $this->load(__DIR__ . '/Commands');
    }
}
