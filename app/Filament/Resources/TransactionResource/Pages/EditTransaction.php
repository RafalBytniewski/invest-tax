<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Transaction, 500);

        return app(TransactionService::class)->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function (Transaction $record): bool {
                    app(TransactionService::class)->delete($record);

                    return true;
                }),
        ];
    }
}
