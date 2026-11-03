<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\WalletSnapshot;
use App\Services\Wallet\WalletSnapshotService;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CaptureWalletSnapshots extends Command
{
    protected $signature = 'wallet-snapshots:capture
                            {--date= : Capture a specific date in Y-m-d format}
                            {--from= : Capture every day from this date}
                            {--to= : Capture every day through this date}
                            {--update : Fill missing daily snapshots from wallet creation to today}
                            {--reset : Delete all wallet snapshots}';

    protected $description = 'Capture daily wallet value snapshots';

    public function handle(WalletSnapshotService $snapshotService): int
    {
        if ($this->option('reset')) {
            $deletedSnapshots = WalletSnapshot::query()->delete();

            $this->info("Deleted {$deletedSnapshots} wallet snapshots.");

            return self::SUCCESS;
        }

        if ($this->option('update')) {
            if ($this->option('date') || $this->option('from') || $this->option('to')) {
                $this->error('The --update option cannot be combined with --date, --from or --to.');

                return self::FAILURE;
            }

            $today = CarbonImmutable::today();

            Wallet::query()
                ->orderBy('id')
                ->chunkById(100, function (Collection $wallets) use ($snapshotService, $today): void {
                    foreach ($wallets as $wallet) {
                        $from = CarbonImmutable::instance($wallet->created_at)->startOfDay();

                        if ($from->isAfter($today)) {
                            continue;
                        }

                        foreach (CarbonPeriod::create($from, $today) as $date) {
                            $snapshotService->captureDaily($wallet, $date, onlyIfMissing: true);
                        }
                    }
                });

            $this->info('Missing wallet snapshots filled from wallet creation to '.$today->toDateString().'.');

            return self::SUCCESS;
        }

        $from = $this->option('from')
            ? CarbonImmutable::parse($this->option('from'))
            : CarbonImmutable::parse($this->option('date') ?: 'today');
        $to = $this->option('to')
            ? CarbonImmutable::parse($this->option('to'))
            : $from;

        foreach (CarbonPeriod::create($from, $to) as $date) {
            Wallet::query()
                ->orderBy('id')
                ->chunkById(100, function (Collection $wallets) use ($snapshotService, $date): void {
                    foreach ($wallets as $wallet) {
                        $snapshotService->captureDaily($wallet, $date);
                    }
                });
        }

        $this->info('Wallet snapshots captured from '.$from->toDateString().' to '.$to->toDateString().'.');

        return self::SUCCESS;
    }
}
