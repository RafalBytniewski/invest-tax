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
        'broker_id'
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

public function assetsCollection()
{
    return $this->transactions()
        ->with('asset')
        ->get()
        ->pluck('asset')
        ->unique('id')
        ->values();
}


    public function averageBuyPrice($assetId)
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


    public function realizedPL()
    {
        $realized = 0;

        foreach ($this->transactions->where('type', 'sell') as $t) {
            $avg = $this->averageBuyPrice($t->asset_id);

            // bierzemy ABS, żeby ignorować minus w bazie
            $sellQuantity = abs($t->quantity);
            $sellTotal = abs($t->total_value);

            $sellPrice = $sellTotal / $sellQuantity;

            $realized += ($sellPrice - $avg) * $sellQuantity;
        }

        return $realized;
    }
}
