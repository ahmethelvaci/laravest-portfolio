<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\PortfolioService;
use App\Models\DailyPrice;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(PortfolioService $portfolioService)
    {
        $summary = $portfolioService->getPortfolioSummary();

        // Prepare chart data for last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
        $dailyPrices = DailyPrice::where('date', '>=', $thirtyDaysAgo)
            ->with('asset')
            ->orderBy('date', 'asc')
            ->get();

        // Group by date to get daily total portfolio values
        // This is a simplified version, it assumes you held the same quantity for 30 days.
        // A more accurate version would require scanning daily transactions.

        return view('dashboard', compact('summary'));
    }
}