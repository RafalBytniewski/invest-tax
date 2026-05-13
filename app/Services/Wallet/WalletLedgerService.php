<?php

namespace App\Services\Wallet;

use App\Models\Transaction;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\Schema;

class WalletLedgerService
{
    public function syncTransaction(Transaction $transaction): void
    {
        if (! Schema::hasTable('wallet_ledgers')) {
            return;
        }

        $definition = $this->definitionForTransaction($transaction);

        if ($definition === null) {
            $this->deleteTransactionLedger($transaction);
            return;
        }

        $amount = abs((float) $transaction->total_value);

        if ($amount <= 0) {
            $this->deleteTransactionLedger($transaction);
            return;
        }

        WalletLedger::updateOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'wallet_id' => $transaction->wallet_id,
                'type' => $definition['type'],
                'source' => $definition['source'],
                'amount' => $amount,
                'date' => $transaction->date,
                'notes' => $transaction->notes,
            ]
        );
    }

    public function deleteTransactionLedger(Transaction $transaction): void
    {
        if (! Schema::hasTable('wallet_ledgers')) {
            return;
        }

        WalletLedger::query()
            ->where('transaction_id', $transaction->id)
            ->delete();
    }

    protected function definitionForTransaction(Transaction $transaction): ?array
    {
        return match ($transaction->type) {
            'buy' => $transaction->affects_wallet_balance
                ? ['type' => 'outflow', 'source' => 'buy']
                : null,
            'sell' => ['type' => 'inflow', 'source' => 'sell'],
            'dividend' => ['type' => 'inflow', 'source' => 'dividend'],
            'crypto_reward' => ['type' => 'inflow', 'source' => 'crypto_reward'],
            'tax' => ['type' => 'outflow', 'source' => 'tax'],
            default => null,
        };
    }
}
