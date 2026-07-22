<?php

namespace App\Models;

use App\Services\Asset\AssetCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    public function wallet_ledgers(): HasMany
    {
        return $this->hasMany(WalletLedger::class);
    }

    public function activeAssetsCollection(): Collection
    {
        return $this->transactions()
            ->whereIn('type', ['buy', 'sell'])
            ->selectRaw('asset_id, SUM(quantity) as total_quantity')
            ->groupBy('asset_id')
            ->having('total_quantity', '>', 0)
            ->with('asset')
            ->get()
            ->pluck('asset')
            ->values();
    }

    /**
     * Return the FIFO summary of one asset held in this wallet.
     *
     * @return array{
     *     quantity: float,
     *     cost_basis: float,
     *     average: ?float,
     *     realized_pl: float,
     *     buy_count: int,
     *     sell_count: int
     * }
     */
    public function assetSummary(int $assetId): array
    {
        $transactions = $this->transactions()
            ->where('asset_id', $assetId)
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        return app(AssetCalculator::class)->calculate($transactions);
    }

    public function averageBuyPrice(int $assetId): float
    {
        return (float) ($this->assetSummary($assetId)['average'] ?? 0.0);
    }

    public function realizedPL(): float
    {
        $calculator = app(AssetCalculator::class);
        $transactionsByAsset = $this->transactions()
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy('asset_id');

        return (float) $transactionsByAsset->sum(
            fn (Collection $transactions): float => $calculator->calculate($transactions)['realized_pl']
        );
    }
}
