<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\Wallet\WalletLedgerService;

class TransactionObserver
{
    public function __construct(
        protected WalletLedgerService $walletLedgerService
    ) {
    }

    public function created(Transaction $transaction): void
    {
        $this->walletLedgerService->syncTransaction($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        $this->walletLedgerService->syncTransaction($transaction);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->walletLedgerService->deleteTransactionLedger($transaction);
    }
}
