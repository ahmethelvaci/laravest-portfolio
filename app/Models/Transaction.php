<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['asset_id', 'direction', 'quantity', 'price', 'date', 'remaining_qty'];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:6',
        'price' => 'decimal:6',
        'remaining_qty' => 'decimal:6',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}