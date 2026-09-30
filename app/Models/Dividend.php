<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dividend extends Model
{
    protected $fillable = [
        'asset_id',
        'amount',
        'ex_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:10',
            'ex_date' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
