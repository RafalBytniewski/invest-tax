<?php

namespace App\Services\Wallet;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Asset\AssetCalculator;
use Illuminate\Support\Collection;

class WalletOverviewService
{
    private const EPSILON = 0.00000001;

    public function __construct(private AssetCalculator $assetCalculator) {}

    /**
     * @return list<array{wallet: Wallet, overview: array<string, mixed>}>
     */
    public function forUser(User $user): array
    {
        return Wallet::query()
            ->whereBelongsTo($user)
            ->with($this->walletRelations())
            ->get()
            ->map(fn (Wallet $wallet): array => [
                'wallet' => $wallet,
                'overview' => $this->forWallet($wallet),
            ])
            ->all();
    }

    /**
     * @return array{
     *     active_assets_count: int,
     *     portfolio_value: ?float,
     *     cost_basis: float,
     *     unrealized_pl: ?float,
     *     realized_pl: float,
     *     last_price_date: ?string,
     *     allocation: list<array{name: string, value: float, percentage: float}>
     * }
     */
    public function forWallet(Wallet $wallet): array
    {
        $wallet->loadMissing($this->walletRelations());

        $transactions = $wallet->transactions->sortBy(
            fn (Transaction $transaction): string => sprintf(
                '%020d-%020d',
                $transaction->date?->timestamp ?? 0,
                $transaction->id,
            ),
        );
        $fifo = $this->assetCalculator->calculate($transactions);

        $positions = $transactions
            ->filter(fn (Transaction $transaction): bool => in_array($transaction->type, ['buy', 'sell'], true))
            ->groupBy('asset_id')
            ->map(function (Collection $assetTransactions): ?array {
                $calculation = $this->assetCalculator->calculate($assetTransactions);

                if ($calculation['quantity'] <= self::EPSILON) {
                    return null;
                }

                $asset = $assetTransactions->first()->asset;
                $closePrice = $asset?->latestPrice?->close_price;

                return [
                    'name' => (string) ($asset?->name ?? $asset?->symbol ?? 'Unknown asset'),
                    'quantity' => $calculation['quantity'],
                    'close_price' => $closePrice === null ? null : (float) $closePrice,
                    'price_date' => $asset?->latestPrice?->date,
                ];
            })
            ->filter()
            ->values();

        $hasMissingPrice = $positions->contains(fn (array $position): bool => $position['close_price'] === null);
        $portfolioValue = $hasMissingPrice
            ? null
            : $positions->sum(fn (array $position): float => $position['quantity'] * $position['close_price']);
        $allocationValue = $positions
            ->filter(fn (array $position): bool => $position['close_price'] !== null)
            ->map(fn (array $position): array => [
                'name' => $position['name'],
                'value' => $position['quantity'] * $position['close_price'],
            ])
            ->values();
        $allocationTotal = $allocationValue->sum('value');

        return [
            'active_assets_count' => $positions->count(),
            'portfolio_value' => $portfolioValue,
            'cost_basis' => (float) $fifo['cost_basis'],
            'unrealized_pl' => $portfolioValue === null
                ? null
                : $portfolioValue - (float) $fifo['cost_basis'],
            'realized_pl' => (float) $fifo['realized_pl'],
            'last_price_date' => $positions
                ->pluck('price_date')
                ->filter()
                ->sortDesc()
                ->first()?->format('d.m.Y'),
            'allocation' => $allocationValue
                ->map(fn (array $position): array => [
                    ...$position,
                    'percentage' => $allocationTotal > 0
                        ? ($position['value'] / $allocationTotal) * 100
                        : 0.0,
                ])
                ->all(),
        ];
    }

    /**
     * @return array<string, callable|string>
     */
    private function walletRelations(): array
    {
        return [
            'broker',
            'transactions' => fn ($query) => $query
                ->with(['asset.latestPrice'])
                ->orderBy('date')
                ->orderBy('id'),
        ];
    }
}
