<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'reward_type',
        'currency',
        'quantity',
        'price_per_unit',
        'total_value',
        'date',
        'notes',
        'wallet_id',
        'asset_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function scopeSearch(Builder $query, ?string $value): Builder
    {
        if (blank($value)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($value) {
            $query->whereHas('asset', function (Builder $assetQuery) use ($value) {
                $assetQuery->where('name', 'like', "%{$value}%");
            })->orWhereHas('wallet', function (Builder $walletQuery) use ($value) {
                $walletQuery->where('name', 'like', "%{$value}%")
                    ->orWhereHas('broker', function (Builder $brokerQuery) use ($value) {
                        $brokerQuery->where('name', 'like', "%{$value}%");
                    });
            });
        });
    }
}
