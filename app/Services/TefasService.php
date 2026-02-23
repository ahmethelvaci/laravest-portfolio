<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DailyPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TefasService
{
    /**
     * Post a new fund to TEFAS tracking API
     */
    public function registerFund(string $code, string $name)
    {
        $apiKey = config('services.tefas.key');
        if (empty($apiKey))
            return false;

        $response = Http::withHeaders([
            'X-API-KEY' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('http://tefas.res.ahmethelvaci.com/api/funds', [
            'code' => $code,
            'name' => $name
        ]);

        return $response->successful();
    }

    /**
     * Fetch daily stats and update our DailyPrice records
     */
    public function updateDailyPrices()
    {
        $apiKey = config('services.tefas.key');
        if (empty($apiKey)) {
            Log::warning('TEFAS API key is missing.');
            return;
        }

        $response = Http::withHeaders([
            'X-API-KEY' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('http://tefas.res.ahmethelvaci.com/api/daily-stats');

        if ($response->successful()) {
            $data = $response->json();

            if (!is_array($data))
                return;

            $date = Carbon::today()->toDateString();
            $fundAssets = Asset::where('type', 'FUND')->get();

            foreach ($data as $item) {
                // Brief bilgisi: fund.code alanı üzerinden eşleşir, fiyat price alanından çekilir.
                if (!isset($item['fund']['code']) || !isset($item['price'])) {
                    continue;
                }

                $code = $item['fund']['code'];
                $price = (float)$item['price'];

                $asset = $fundAssets->firstWhere('code', $code);
                if ($asset) {
                    DailyPrice::updateOrCreate(
                    ['asset_id' => $asset->id, 'date' => $date],
                    ['price' => $price]
                    );
                }
            }
        }
        else {
            Log::error('TEFAS API Request failed', ['status' => $response->status(), 'response' => $response->body()]);
        }
    }
}