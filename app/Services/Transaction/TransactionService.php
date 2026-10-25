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

    /**
     * @return array{status: string, duplicate_count: int, changed: bool}
     */
    public function reconcileWalletLedger(Transaction $transaction, bool $fix = false): array
    {
        $ledgerData = $this->walletLedgerData($transaction);

        if ($ledgerData === null) {
            return [
                'status' => 'unsupported',
                'duplicate_count' => 0,
                'changed' => false,
            ];
        }

        $ledgers = $transaction->relationLoaded('walletLedgers')
            ? $transaction->walletLedgers->sortBy('id')->values()
            : $transaction->walletLedgers()->oldest('id')->get();

        if ($ledgers->count() > 1) {
            return [
                'status' => 'duplicate',
                'duplicate_count' => $ledgers->count() - 1,
                'changed' => false,
            ];
        }

        $ledger = $ledgers->first();

        if ($ledger === null) {
            if ($fix) {
                WalletLedger::create($ledgerData);
            }

            return [
                'status' => 'missing',
                'duplicate_count' => 0,
                'changed' => $fix,
            ];
        }

        if ($this->walletLedgerMatches($ledger, $ledgerData)) {
            return [
                'status' => 'ok',
                'duplicate_count' => 0,
                'changed' => false,
            ];
        }

        if ($fix) {
            $ledger->update($ledgerData);
        }

        return [
            'status' => 'mismatched',
            'duplicate_count' => 0,
            'changed' => $fix,
        ];
    }

    protected function syncWalletLedger(Transaction $transaction): void
    {
        $ledgerData = $this->walletLedgerData($transaction);

        if ($ledgerData === null) {
            $transaction->walletLedgers()->delete();

            return;
        }

        $ledger = $transaction->walletLedgers()->oldest('id')->first() ?? new WalletLedger([
            'transaction_id' => $transaction->id,
        ]);

        $ledger->fill($ledgerData);
        $ledger->save();
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function walletLedgerData(Transaction $transaction): ?array
    {
        $ledgerType = match ($transaction->type) {
            'buy', 'sell', 'dividend' => $transaction->type,
            default => null,
        };

        if ($ledgerType === null) {
            return null;
        }

        return [
            'wallet_id' => $transaction->wallet_id,
            'transaction_id' => $transaction->id,
            'type' => $ledgerType,
            'amount' => match ($transaction->type) {
                'buy' => -abs((float) $transaction->total_value),
                'sell', 'dividend' => abs((float) $transaction->total_value),
            },
            'date' => $transaction->date,
            'notes' => $transaction->notes,
        ];
    }

    /**
     * @param  array<string, mixed>  $ledgerData
     */
    protected function walletLedgerMatches(WalletLedger $ledger, array $ledgerData): bool
    {
        return $ledger->wallet_id === $ledgerData['wallet_id']
            && $ledger->transaction_id === $ledgerData['transaction_id']
            && $ledger->type === $ledgerData['type']
            && abs((float) $ledger->amount - (float) $ledgerData['amount']) < 0.00000001
            && $ledger->date?->format('Y-m-d H:i:s') === $ledgerData['date']?->format('Y-m-d H:i:s')
            && $ledger->notes === $ledgerData['notes'];
    }
}
