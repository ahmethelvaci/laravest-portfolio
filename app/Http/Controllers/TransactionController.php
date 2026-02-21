<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\PortfolioService;

class TransactionController extends Controller
{
    public function create()
    {
        return view('transactions.create');
    }

    public function store(Request $request, PortfolioService $portfolioService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'direction' => 'required|in:BUY,SELL',
            'type' => 'required|in:GOLD,FUND',
            'code' => 'required_if:type,FUND|nullable|string',
            'name' => 'required|string',
            'quantity' => 'required|numeric|min:0.000001',
            'price' => 'required|numeric|min:0.000001',
        ]);

        try {
            $asset = $portfolioService->getOrCreateAsset(
                $validated['type'],
                $validated['name'],
                $validated['code'] ?? null
            );

            $portfolioService->addTransaction(
                $asset,
                $validated['direction'],
                $validated['quantity'],
                $validated['price'],
                $validated['date']
            );

            return redirect()->route('dashboard')->with('success', 'İşlem başarıyla eklendi.');
        }
        catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}