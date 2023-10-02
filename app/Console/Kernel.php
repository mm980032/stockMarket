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
        // php artisan schedule:work
        $schedule->call(function () {
            $service = app()->make(StockMarketService::class);
            $service->updateBaseStockInfo();
        })->dailyAt('14:00')->weekdays(); // 請替換為您希望的時間

        // 推薦購買排成
        $schedule->call(function () {
            $service = app()->make(StockMarketService::class);
            $service->recommendBuy();
        })->everyFiveMinutes()->between('9:00', '13:30')->weekdays(); // 請替換為您希望的時間
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
