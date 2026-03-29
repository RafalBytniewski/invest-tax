<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'currency',
        'broker_id',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    public function activeAssetsCollection()
    {
        return $this->transactions()
            ->selectRaw('asset_id, SUM(quantity) as total_quantity')
            ->groupBy('asset_id')
            ->having('total_quantity', '>', 0)
            ->with('asset')
            ->get()
            ->pluck('asset')
            ->values();
    }

    public function averageBuyPrice(int $assetId): float
    {
        $buy = $this->transactions()
            ->where('asset_id', $assetId)
            ->where('type', 'buy');

        $quantity = $buy->sum('quantity');

        if ($quantity == 0) {
            return 0;
        }

        return $buy->sum('total_value') / $quantity;
    }

    public function realizedPL(): float
    {
        $realized = 0;

        foreach ($this->transactions->where('type', 'sell') as $transaction) {
            $averageBuyPrice = $this->averageBuyPrice($transaction->asset_id);
            $sellQuantity = abs($transaction->quantity);
            $sellTotal = abs($transaction->total_value);
            $sellPrice = $sellTotal / $sellQuantity;

            $realized += ($sellPrice - $averageBuyPrice) * $sellQuantity;
        }

        return $realized;
    }
}
