<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Services\Wallet\WalletSnapshotService;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class CaptureWalletSnapshots extends Command
{
    protected $signature = 'wallet-snapshots:capture
                            {--date= : Capture a specific date in Y-m-d format}
                            {--from= : Capture every day from this date}
                            {--to= : Capture every day through this date}';

    protected $description = 'Capture daily wallet value snapshots';

    public function handle(WalletSnapshotService $snapshotService): int
    {
        $from = $this->option('from')
            ? CarbonImmutable::parse($this->option('from'))
            : CarbonImmutable::parse($this->option('date') ?: 'today');
        $to = $this->option('to')
            ? CarbonImmutable::parse($this->option('to'))
            : $from;

        foreach (CarbonPeriod::create($from, $to) as $date) {
            Wallet::query()
                ->orderBy('id')
                ->chunkById(100, function ($wallets) use ($snapshotService, $date): void {
                    foreach ($wallets as $wallet) {
                        $snapshotService->captureDaily($wallet, $date);
                    }
                });
        }

        $this->info('Wallet snapshots captured from '.$from->toDateString().' to '.$to->toDateString().'.');

        return self::SUCCESS;
    }
}
