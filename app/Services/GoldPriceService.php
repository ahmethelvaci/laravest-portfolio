<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DailyPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GoldPriceService
{
    /**
     * Fetch the latest gold prices from CollectAPI and update DailyPrices.
     */
    public function updateDailyPrices()
    {
        $apiKey = config('services.collectapi.key');
        if (empty($apiKey)) {
            Log::warning('CollectAPI key is missing.');
            return;
        }

        $response = Http::withHeaders([
            'authorization' => 'apikey ' . $apiKey,
            'content-type' => 'application/json',
        ])->get('https://api.collectapi.com/economy/goldPrice');

        if ($response->successful()) {
            $data = $response->json('result');
            if (!$data)
                return;

            $date = Carbon::today()->toDateString();
            // Yalnızca portföyümüze eklenmiş altınları güncelleyelim
            $goldAssets = Asset::where('type', 'GOLD')->get();

            foreach ($goldAssets as $asset) {
                // İsme göre eşleştirme (Gram Altın, Çeyrek Altın vb.)
                $match = collect($data)->firstWhere('name', $asset->name);

                if ($match && isset($match['selling'])) {
                    // price verisini clean hale getir (virgül vs)
                    $price = str_replace(',', '.', $match['selling']);
                    $price = (float)preg_replace('/[^0-9.]/', '', $price);

                    DailyPrice::updateOrCreate(
                    ['asset_id' => $asset->id, 'date' => $date],
                    ['price' => $price]
                    );
                }
            }
        }
        else {
            Log::error('CollectAPI Request failed', ['status' => $response->status(), 'response' => $response->body()]);
        }
    }
}