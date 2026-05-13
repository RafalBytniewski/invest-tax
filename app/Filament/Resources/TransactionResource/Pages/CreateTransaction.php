<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeTransactionData($data);
    }

    protected function normalizeTransactionData(array $data): array
    {
        $type = $data['type'] ?? 'buy';
        $quantity = abs((float) ($data['quantity'] ?? 0));
        $pricePerUnit = abs((float) ($data['price_per_unit'] ?? 0));
        $totalValue = $quantity * $pricePerUnit;

        $data['price_per_unit'] = $pricePerUnit;
        $data['quantity'] = $type === 'sell' ? -$quantity : $quantity;
        $data['total_value'] = $type === 'sell' ? -$totalValue : $totalValue;
        $data['affects_wallet_balance'] = $type === 'buy'
            ? (bool) ($data['affects_wallet_balance'] ?? true)
            : true;
        $data['asset_id'] = $type === 'tax' ? null : ($data['asset_id'] ?? null);

        return $data;
    }
}
