<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CheckWalletLedgers extends Command
{
    protected $signature = 'wallet-ledger:check {--fix : Create missing entries and repair mismatched entries}';

    protected $description = 'Check wallet ledger entries against transactions';

    public function handle(TransactionService $transactionService): int
    {
        $stats = [
            'total' => 0,
            'ok' => 0,
            'missing' => 0,
            'fixed_missing' => 0,
            'mismatched' => 0,
            'fixed_mismatched' => 0,
            'duplicates' => 0,
            'fixed_duplicates' => 0,
            'unsupported' => 0,
        ];
        $fix = (bool) $this->option('fix');

        Transaction::query()
            ->with('walletLedgers')
            ->orderBy('id')
            ->chunkById(500, function (Collection $transactions) use (&$stats, $fix, $transactionService): void {
                foreach ($transactions as $transaction) {
                    $stats['total']++;

                    $result = $transactionService->reconcileWalletLedger($transaction, $fix);

                    $stats[$result['status']]++;
                    $stats['duplicates'] += $result['duplicate_count'];

                    if ($result['changed']) {
                        $stats['fixed_'.$result['status']]++;
                    }
                }
            });

        $this->info($fix ? 'Wallet ledger check completed with fixes.' : 'Wallet ledger check completed (dry run).');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Transactions checked', $stats['total']],
                ['Matching entries', $stats['ok']],
                ['Missing entries', $stats['missing']],
                ['Missing entries fixed', $stats['fixed_missing']],
                ['Mismatched entries', $stats['mismatched']],
                ['Mismatched entries fixed', $stats['fixed_mismatched']],
                ['Duplicate entries', $stats['duplicates']],
                ['Duplicate entries fixed', $stats['fixed_duplicates']],
                ['Unsupported transaction types', $stats['unsupported']],
            ]
        );

        return self::SUCCESS;
    }
}
