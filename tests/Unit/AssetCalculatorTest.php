<?php

namespace Tests\Unit;

use App\Services\Asset\AssetCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AssetCalculatorTest extends TestCase
{
    public function test_it_calculates_remaining_fifo_cost_and_realized_profit_chronologically(): void
    {
        $result = (new AssetCalculator)->calculate([
            ['type' => 'buy', 'quantity' => 10, 'total_value' => 1000],
            ['type' => 'sell', 'quantity' => -4, 'total_value' => -600],
            ['type' => 'buy', 'quantity' => 4, 'total_value' => 800],
        ]);

        $this->assertEqualsWithDelta(10, $result['quantity'], 0.00000001);
        $this->assertEqualsWithDelta(1400, $result['cost_basis'], 0.00000001);
        $this->assertEqualsWithDelta(140, $result['average'], 0.00000001);
        $this->assertEqualsWithDelta(200, $result['realized_pl'], 0.00000001);
        $this->assertSame(2, $result['buy_count']);
        $this->assertSame(1, $result['sell_count']);
    }

    public function test_it_calculates_the_kghm_example_using_fifo(): void
    {
        $result = (new AssetCalculator)->calculate([
            ['wallet_id' => 2, 'type' => 'buy', 'quantity' => 1, 'total_value' => 287.60],
            ['wallet_id' => 2, 'type' => 'buy', 'quantity' => 1, 'total_value' => 260.30],
            ['wallet_id' => 2, 'type' => 'sell', 'quantity' => -1, 'total_value' => -373.90],
            ['wallet_id' => 2, 'type' => 'buy', 'quantity' => 1, 'total_value' => 332.30],
            ['wallet_id' => 2, 'type' => 'sell', 'quantity' => -1, 'total_value' => -382.05],
        ]);

        $this->assertEqualsWithDelta(1, $result['quantity'], 0.00000001);
        $this->assertEqualsWithDelta(332.30, $result['cost_basis'], 0.00000001);
        $this->assertEqualsWithDelta(332.30, $result['average'], 0.00000001);
        $this->assertEqualsWithDelta(208.05, $result['realized_pl'], 0.00000001);
    }

    public function test_fifo_lots_are_kept_separately_for_each_wallet(): void
    {
        $result = (new AssetCalculator)->calculate([
            ['wallet_id' => 1, 'type' => 'buy', 'quantity' => 1, 'total_value' => 100],
            ['wallet_id' => 2, 'type' => 'buy', 'quantity' => 1, 'total_value' => 200],
            ['wallet_id' => 2, 'type' => 'sell', 'quantity' => -1, 'total_value' => -250],
        ]);

        $this->assertEqualsWithDelta(1, $result['quantity'], 0.00000001);
        $this->assertEqualsWithDelta(100, $result['average'], 0.00000001);
        $this->assertEqualsWithDelta(50, $result['realized_pl'], 0.00000001);
    }

    public function test_it_resets_cost_basis_when_the_position_is_closed(): void
    {
        $result = (new AssetCalculator)->calculate([
            ['type' => 'buy', 'quantity' => 2, 'total_value' => 200],
            ['type' => 'sell', 'quantity' => -2, 'total_value' => -180],
        ]);

        $this->assertSame(0.0, $result['quantity']);
        $this->assertSame(0.0, $result['cost_basis']);
        $this->assertNull($result['average']);
        $this->assertEqualsWithDelta(-20, $result['realized_pl'], 0.00000001);
    }

    public function test_it_rejects_a_sale_larger_than_the_held_position(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new AssetCalculator)->calculate([
            ['type' => 'buy', 'quantity' => 1, 'total_value' => 100],
            ['type' => 'sell', 'quantity' => -2, 'total_value' => -200],
        ]);
    }

    public function test_unrealized_profit_uses_the_remaining_cost_basis(): void
    {
        $calculator = new AssetCalculator;

        $this->assertSame(250.0, $calculator->unrealizedPL(1250, 1000));
        $this->assertNull($calculator->unrealizedPL(null, 1000));
    }
}
