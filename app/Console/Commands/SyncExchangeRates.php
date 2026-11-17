<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\Currency\ExchangeRateService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'exchange-rates:sync
        {--from= : First date to synchronize, in YYYY-MM-DD format}
        {--to= : Last date to synchronize, in YYYY-MM-DD format}
        {--buffer=10 : Number of days to subtract from the first transaction date}';

    protected $description = 'Synchronize historical EUR/USD exchange rates against PLN from NBP';

    public function handle(ExchangeRateService $exchangeRateService): int
    {
        $to = $this->option('to')
            ? CarbonImmutable::parse((string) $this->option('to'))
            : CarbonImmutable::today();
        $from = $this->option('from')
            ? CarbonImmutable::parse((string) $this->option('from'))
            : $this->defaultFrom($to);

        if ($from->greaterThan($to)) {
            $this->error('The --from date must not be later than --to.');

            return self::FAILURE;
        }

        $stats = $exchangeRateService->sync($from, $to);

        $this->info("Exchange rates synchronized from {$from->toDateString()} to {$to->toDateString()}.");
        $this->table(['Metric', 'Count'], [
            ['Calendar days requested', $stats['requested']],
            ['Rates stored', $stats['stored']],
        ]);

        return self::SUCCESS;
    }

    private function defaultFrom(CarbonImmutable $to): CarbonImmutable
    {
        $firstTransactionDate = Transaction::query()->min('date');

        return $firstTransactionDate === null
            ? $to->subDays(30)
            : CarbonImmutable::parse($firstTransactionDate)->subDays((int) $this->option('buffer'));
    }
}
