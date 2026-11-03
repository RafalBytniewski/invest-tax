<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'snapshot_at',
        'snapshot_type',
        'cash_value',
        'positions_value',
        'total_value',
        'event_type',
        'event_amount',
        'wallet_ledger_id',
        'transaction_id',
        'valuation_status',
        'price_as_of',
        'metadata',
    ];

    protected $casts = [
        'snapshot_at' => 'datetime',
        'cash_value' => 'decimal:8',
        'positions_value' => 'decimal:8',
        'total_value' => 'decimal:8',
        'event_amount' => 'decimal:8',
        'price_as_of' => 'date',
        'metadata' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function walletLedger(): BelongsTo
    {
        return $this->belongsTo(WalletLedger::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
