<?php

namespace App\Services\Asset;

use InvalidArgumentException;

class AssetCalculator
{
    private const EPSILON = 0.00000001;

    /**
     * Calculate a long position using FIFO, separately for each wallet.
     * Transactions must be supplied in chronological order.
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
    public function calculate(iterable $transactions): array
    {
        $realizedPL = 0.0;
        $buyCount = 0;
        $sellCount = 0;
        $lotsByWallet = [];

        foreach ($transactions as $transaction) {
            $type = (string) data_get($transaction, 'type');

            if (! in_array($type, ['buy', 'sell'], true)) {
                continue;
            }

            $transactionQuantity = abs((float) data_get($transaction, 'quantity', 0));
            $transactionValue = abs((float) data_get($transaction, 'total_value', 0));
            $walletKey = (string) data_get($transaction, 'wallet_id', 'default');

            if ($transactionQuantity <= self::EPSILON) {
                continue;
            }

            if ($type === 'buy') {
                $lotsByWallet[$walletKey][] = [
                    'quantity' => $transactionQuantity,
                    'unit_cost' => $transactionValue / $transactionQuantity,
                ];
                $buyCount++;

                continue;
            }

            $availableQuantity = array_sum(array_column($lotsByWallet[$walletKey] ?? [], 'quantity'));

            if ($transactionQuantity - $availableQuantity > self::EPSILON) {
                throw new InvalidArgumentException('A sell transaction cannot exceed the currently held quantity.');
            }

            $salePrice = $transactionValue / $transactionQuantity;
            $quantityToSell = $transactionQuantity;

            while ($quantityToSell > self::EPSILON) {
                $lot = &$lotsByWallet[$walletKey][0];
                $matchedQuantity = min($quantityToSell, $lot['quantity']);

                $realizedPL += ($salePrice - $lot['unit_cost']) * $matchedQuantity;
                $lot['quantity'] -= $matchedQuantity;
                $quantityToSell -= $matchedQuantity;

                if ($lot['quantity'] <= self::EPSILON) {
                    array_shift($lotsByWallet[$walletKey]);
                }

                unset($lot);
            }

            $sellCount++;
        }

        $quantity = 0.0;
        $costBasis = 0.0;

        foreach ($lotsByWallet as $lots) {
            foreach ($lots as $lot) {
                $quantity += $lot['quantity'];
                $costBasis += $lot['quantity'] * $lot['unit_cost'];
            }
        }

        return [
            'quantity' => $quantity,
            'cost_basis' => $costBasis,
            'average' => $quantity > self::EPSILON ? $costBasis / $quantity : null,
            'realized_pl' => $realizedPL,
            'buy_count' => $buyCount,
            'sell_count' => $sellCount,
        ];
    }

    public function average(float $buyValue, float $buyQty): ?float
    {
        return abs($buyQty) > self::EPSILON ? abs($buyValue) / abs($buyQty) : null;
    }

    public function positionValue(?float $price, float $quantity): ?float
    {
        return $price === null ? null : $price * $quantity;
    }

    public function realizedPL(float $sellValue, float $sellQty, ?float $average): float
    {
        if (abs($sellQty) <= self::EPSILON || $average === null) {
            return 0.0;
        }

        return ((abs($sellValue) / abs($sellQty)) - $average) * abs($sellQty);
    }

    public function unrealizedPL(?float $positionValue, float $costBasis): ?float
    {
        return $positionValue === null ? null : $positionValue - $costBasis;
    }
}
