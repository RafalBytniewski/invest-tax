<?php

namespace App\Services\Wallet;

use App\Models\AssetPrice;
use App\Models\Wallet;
use App\Models\WalletSnapshot;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class WalletSnapshotService
{
    public function captureDaily(
        Wallet $wallet,
        CarbonInterface $snapshotAt,
        bool $onlyIfMissing = false,
    ): ?WalletSnapshot {
        $snapshotAt = $snapshotAt->copy()->endOfDay();

        if (! $wallet->transactions()->where('date', '<=', $snapshotAt)->exists()) {
            return null;
        }

        $snapshotKey = [
            'wallet_id' => $wallet->id,
            'snapshot_type' => 'daily',
            'snapshot_at' => $snapshotAt,
        ];

        if ($onlyIfMissing) {
            return WalletSnapshot::query()->firstOrCreate(
                $snapshotKey,
                $this->buildSnapshot($wallet, $snapshotAt),
            );
        }

        $snapshot = $this->buildSnapshot($wallet, $snapshotAt);

        return WalletSnapshot::updateOrCreate(
            $snapshotKey,
            $snapshot,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSnapshot(Wallet $wallet, CarbonInterface $snapshotAt): array
    {
        $positions = $wallet->transactions()
            ->where('date', '<=', $snapshotAt)
            ->selectRaw('asset_id, SUM(quantity) as quantity')
            ->groupBy('asset_id')
            ->get();

        $assetIds = $positions
            ->where('quantity', '>', 0)
            ->pluck('asset_id');

        $prices = $this->latestPrices($assetIds, $snapshotAt);
        $positionsValue = 0.0;
        $isComplete = true;

        foreach ($positions as $position) {
            $quantity = (float) $position->quantity;

            if ($quantity <= 0) {
                continue;
            }

            $price = $prices->get($position->asset_id);

            if ($price === null) {
                $isComplete = false;

                continue;
            }

            $positionsValue += $quantity * (float) $price->close_price;
        }

        return [
            'cash_value' => 0,
            'positions_value' => $positionsValue,
            'total_value' => $positionsValue,
            'valuation_status' => $isComplete ? 'complete' : 'partial',
            'price_as_of' => $snapshotAt->toDateString(),
        ];
    }

    /**
     * @param  Collection<int, int>  $assetIds
     * @return Collection<int, AssetPrice>
     */
    private function latestPrices(Collection $assetIds, CarbonInterface $snapshotAt): Collection
    {
        if ($assetIds->isEmpty()) {
            return collect();
        }

        return AssetPrice::query()
            ->whereIn('asset_id', $assetIds)
            ->whereDate('date', '<=', $snapshotAt->toDateString())
            ->whereNotNull('close_price')
            ->orderByDesc('date')
            ->get()
            ->unique('asset_id')
            ->keyBy('asset_id');
    }
}
