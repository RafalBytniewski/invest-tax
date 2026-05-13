<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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

    public function ledgers(): HasMany
    {
        return $this->hasMany(WalletLedger::class);
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
            ->whereNotNull('asset_id')
            ->whereIn('type', ['buy', 'sell'])
            ->selectRaw('asset_id, SUM(quantity) as total_quantity')
            ->groupBy('asset_id')
            ->having('total_quantity', '>', 0)
            ->with('asset')
            ->get()
            ->pluck('asset')
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

    public function cashBalance(): float
    {
        if (! Schema::hasTable('wallet_ledgers')) {
            return 0.0;
        }

        $ledgers = $this->relationLoaded('ledgers')
            ? $this->ledgers
            : $this->ledgers()->get();

        return $this->calculateCashBalance($ledgers);
    }

    protected function calculateCashBalance(Collection $ledgers): float
    {
        return (float) $ledgers->sum(function (WalletLedger $ledger) {
            $amount = (float) $ledger->amount;

            return $ledger->type === 'inflow' ? $amount : -$amount;
        });
    }
}
