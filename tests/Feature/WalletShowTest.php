<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_own_wallet_page(): void
    {
        $user = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'symbol' => 'TB',
            'type' => 'broker',
            'image' => 'test.png',
            'url' => 'https://example.com',
            'region' => 'EU',
        ]);

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'Main Wallet',
            'currency' => 'PLN',
        ]);

        $this->actingAs($user)
            ->get(route('wallets.show', $wallet))
            ->assertOk()
            ->assertSee('Main Wallet');
    }

    public function test_user_cannot_open_someone_elses_wallet_page(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'symbol' => 'TB',
            'type' => 'broker',
            'image' => 'test.png',
            'url' => 'https://example.com',
            'region' => 'EU',
        ]);

        $wallet = Wallet::create([
            'user_id' => $otherUser->id,
            'broker_id' => $broker->id,
            'name' => 'Hidden Wallet',
            'currency' => 'USD',
        ]);

        $this->actingAs($user)
            ->get(route('wallets.show', $wallet))
            ->assertForbidden();
    }
}
