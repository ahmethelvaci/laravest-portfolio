<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyPrice extends Model
{
    protected $fillable = ['asset_id', 'price', 'date'];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:6',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}