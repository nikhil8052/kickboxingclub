<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// use App\Console\Commands\YourCommandName;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:update-orders')->hourly()->withoutOverlapping();
        $schedule->command('app:update-membership-instance')->hourly()->withoutOverlapping();
        $schedule->command('app:update-user')->hourly()->withoutOverlapping();
        $schedule->command('app:update-order-lines')->hourly()->withoutOverlapping();
        $schedule->command('app:updatetime-clock-shifts')->hourly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
