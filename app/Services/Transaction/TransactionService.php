<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Transaction
    {
        return DB::transaction(function () use ($attributes): Transaction {
            $transaction = Transaction::create($attributes);

            $this->syncWalletLedger($transaction);

            return $transaction;
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Transaction $transaction, array $attributes): Transaction
    {
        return DB::transaction(function () use ($transaction, $attributes): Transaction {
            $transaction->update($attributes);
            $transaction->refresh();

            $this->syncWalletLedger($transaction);

            return $transaction;
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $transaction->walletLedgers()->delete();
            $transaction->delete();
        });
    }

    protected function syncWalletLedger(Transaction $transaction): void
    {
        $ledgerType = match ($transaction->type) {
            'buy', 'sell', 'dividend' => $transaction->type,
            default => null,
        };

        if ($ledgerType === null) {
            $transaction->walletLedgers()->delete();

            return;
        }

        $amount = match ($transaction->type) {
            'buy' => -abs((float) $transaction->total_value),
            'sell', 'dividend' => abs((float) $transaction->total_value),
        };

        $ledger = WalletLedger::query()->firstOrNew([
            'transaction_id' => $transaction->id,
        ]);

        $ledger->fill([
            'wallet_id' => $transaction->wallet_id,
            'type' => $ledgerType,
            'amount' => $amount,
            'date' => $transaction->date,
            'notes' => $transaction->notes,
        ]);
        $ledger->save();
    }
}
