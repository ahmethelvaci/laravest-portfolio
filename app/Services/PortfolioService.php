<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class PortfolioService
{
    protected TefasService $tefasService;

    public function __construct(TefasService $tefasService)
    {
        $this->tefasService = $tefasService;
    }

    /**
     * Get or create an asset. If it's a new FUND, register it via TefasService.
     */
    public function getOrCreateAsset(string $type, string $name, ?string $code = null): Asset
    {
        if ($type === 'FUND' && $code) {
            $asset = Asset::firstWhere('code', $code);
            if (!$asset) {
                // Register to external API
                $this->tefasService->registerFund($code, $name);

                $asset = Asset::create([
                    'type' => 'FUND',
                    'code' => $code,
                    'name' => $name,
                ]);
            }
            return $asset;
        }

        // For GOLD
        $asset = Asset::firstOrCreate(
        ['type' => 'GOLD', 'name' => $name],
        ['code' => null]
        );

        return $asset;
    }

    /**
     * Add a new transaction (BUY or SELL) and apply FIFO if SELL.
     */
    public function addTransaction(Asset $asset, string $direction, float $quantity, float $price, string $date): Transaction
    {
        return DB::transaction(function () use ($asset, $direction, $quantity, $price, $date) {
            if ($direction === 'SELL') {
                $this->applyFifoSell($asset, $quantity);
                $remainingQty = 0;
            }
            else {
                $remainingQty = $quantity;
            }

            return Transaction::create([
                'asset_id' => $asset->id,
                'direction' => $direction,
                'quantity' => $quantity,
                'price' => $price,
                'date' => $date,
                'remaining_qty' => $remainingQty,
            ]);
        });
    }

    /**
     * Process FIFO algorithm for selling an asset.
     */
    protected function applyFifoSell(Asset $asset, float $sellQuantity): void
    {
        $buyTransactions = Transaction::where('asset_id', $asset->id)
            ->where('direction', 'BUY')
            ->where('remaining_qty', '>', 0)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        $totalAvailable = $buyTransactions->sum('remaining_qty');

        if ($sellQuantity > $totalAvailable) {
            throw new Exception('Satış miktarı mevcut bakiyeden (portföydeki ' . $asset->name . ' miktarından) fazla olamaz.');
        }

        $remainingToSell = $sellQuantity;

        foreach ($buyTransactions as $buyTx) {
            if ($remainingToSell <= 0) {
                break;
            }

            if ($buyTx->remaining_qty >= $remainingToSell) {
                $buyTx->remaining_qty -= $remainingToSell;
                $buyTx->save();
                $remainingToSell = 0;
            }
            else {
                $remainingToSell -= $buyTx->remaining_qty;
                $buyTx->remaining_qty = 0;
                $buyTx->save();
            }
        }
    }

    /**
     * Calculate Portfolio Summary: totals, weighted average costs, current prices.
     */
    public function getPortfolioSummary()
    {
        $assets = Asset::with(['transactions' => function ($q) {
            $q->where('direction', 'BUY')->where('remaining_qty', '>', 0);
        }, 'latestDailyPrice'])->get();

        $summary = [];
        $totalCost = 0;
        $totalCurrentValue = 0;

        foreach ($assets as $asset) {
            $remainingTransactions = $asset->transactions;
            $currentQty = $remainingTransactions->sum('remaining_qty');

            if ($currentQty <= 0) {
                continue; // Skip assets not in portfolio
            }

            $assetTotalCost = 0;
            foreach ($remainingTransactions as $tx) {
                $assetTotalCost += ($tx->remaining_qty * $tx->price);
            }

            $weightedAvgCost = $currentQty > 0 ? $assetTotalCost / $currentQty : 0;

            $latestPriceRecord = $asset->latestDailyPrice;
            $currentPrice = $latestPriceRecord ? $latestPriceRecord->price : $weightedAvgCost; // fallback to cost if no price

            $currentValue = $currentQty * $currentPrice;
            $profit = $currentValue - $assetTotalCost;
            $profitPercentage = $assetTotalCost > 0 ? ($profit / $assetTotalCost) * 100 : 0;

            $totalCost += $assetTotalCost;
            $totalCurrentValue += $currentValue;

            $summary[] = [
                'asset' => $asset,
                'quantity' => $currentQty,
                'avg_cost' => $weightedAvgCost,
                'current_price' => $currentPrice,
                'total_cost' => $assetTotalCost,
                'current_value' => $currentValue,
                'profit' => $profit,
                'profit_percentage' => $profitPercentage,
            ];
        }

        $totalProfit = $totalCurrentValue - $totalCost;
        $totalProfitPercentage = $totalCost > 0 ? ($totalProfit / $totalCost) * 100 : 0;

        return [
            'assets' => collect($summary),
            'total_cost' => $totalCost,
            'total_current_value' => $totalCurrentValue,
            'total_profit' => $totalProfit,
            'total_profit_percentage' => $totalProfitPercentage,
        ];
    }
}