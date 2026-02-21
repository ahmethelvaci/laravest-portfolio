<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoldPriceService;
use App\Services\TefasService;

class UpdateDailyPrices extends Command
{
    protected $signature = 'app:update-daily-prices';
    protected $description = 'Updates daily prices for gold and funds from external APIs';

    public function handle(GoldPriceService $goldService, TefasService $tefasService)
    {
        $this->info('Starting daily prices update...');

        $this->info('Fetching gold prices...');
        $goldService->updateDailyPrices();

        $this->info('Fetching TEFAS fund prices...');
        $tefasService->updateDailyPrices();

        $this->info('Daily prices update completed successfully.');
    }
}