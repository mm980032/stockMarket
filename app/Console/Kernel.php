<?php

namespace App\Console;

use App\Services\StockMarketService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $service = app()->make(StockMarketService::class);
            $service->compareOpeningAndLowest();
        })->dailyAt('09:00')->everyMinute(); // 請替換為您希望的時間
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
