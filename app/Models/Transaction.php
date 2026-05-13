<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'reward_type',
        'currency',
        'quantity',
        'price_per_unit',
        'total_value',
        'date',
        'notes',
        'affects_wallet_balance',
        'wallet_id',
        'asset_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'affects_wallet_balance' => 'boolean',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function walletLedger(): HasOne
    {
        return $this->hasOne(WalletLedger::class);
    }

    public function scopeSearch($query, $value)
    {
        $query->where(function ($q) use ($value) {
            // Szukaj po nazwie assetu
            $q->whereHas('asset', function ($q2) use ($value) {
                $q2->where('name', 'like', "%{$value}%");
            })
                // Szukaj po nazwie walletu
                ->orWhereHas('wallet', function ($q2) use ($value) {
                    $q2->where('name', 'like', "%{$value}%")
                        // Szukaj także po nazwie brokera przez wallet
                        ->orWhereHas('broker', function ($q3) use ($value) {
                            $q3->where('name', 'like', "%{$value}%");
                        });
                });
        });
    }

    public function scopeForUserAssets($query, $userId , $assetId){
        return $query
            ->where('asset_id', $assetId)
            ->whereHas('wallet', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }
}
