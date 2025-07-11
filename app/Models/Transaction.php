<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'quantity',
        'price_per_unit',
        'total_fees',
        'date',
        'notes',
        'wallet_id',
        'asset_id'
    ];

    public function wallet():BelongsTo{
        return $this->belongsTo(Wallet::class);
    }

    public function asset():BelongsTo{
        return $this->belongsTo(Asset::class);
    }
}
