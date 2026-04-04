<?php

namespace App\Services\Asset;

class AssetCalculator
{
    public function average(float $buyValue, float $buyQty): float|string
    {
        if ($buyQty == 0) return '-';
        return $buyValue / $buyQty;
    }
    
    public function positionValue(?float $price, float $quantity): ?float
    {
        return is_numeric($price) ? $price * $quantity : null;
    }

    public function realizedPL(float $sellValue, float $sellQty, float|string $average): float|string
    {
        if ($sellQty == 0 || !is_numeric($average)) return '0';
        $avgSell = $sellValue / $sellQty;
        return (abs($avgSell) - $average) * abs($sellQty);
    }

/*     public function unrealizedPL(?float $positionValue, float|string $average, float $quantity): ?float
    {
        if ($positionValue === null || !is_numeric($average)) return null;
        return $positionValue - ($average * $quantity);
    } */
}
