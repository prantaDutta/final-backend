<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Remove telescope data daily
        $schedule->command('telescope:prune')->daily();
        // laravel database backup
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('01:30');
        // Control the loan limit of the lenders
        $schedule->command('command:control-loan-limit')->daily()->at('03:00');
        // auto-payments of borrower's due installments
        $schedule->command('command:auto-payments')->daily();
        // The following command is for testing purpose only
        // It will be disabled in production
        if (App::environment('local')) {
            $schedule->command('command:manage-installments')->withoutOverlapping();
            $schedule->command('command:auto-payments')->withoutOverlapping();
        }
        // set installment penalties and make them due from unpaid
        $schedule->command('command:manage-installments')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
