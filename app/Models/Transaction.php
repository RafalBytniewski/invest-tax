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
        'currency',
        'quantity',
        'price_per_unit',
        'total_fees',
        'total_value',
        'date',
        'notes',
        'wallet_id',
        'asset_id'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function wallet():BelongsTo{
        return $this->belongsTo(Wallet::class);
    }

    public function asset():BelongsTo{
        return $this->belongsTo(Asset::class);
    }
    
   public function scopeSearch($query, $value)
    {
        $query->where(function ($q) use ($value) {
            $q->whereHas('asset', function ($q2) use ($value) {
                $q2->where('name', 'like', "%{$value}%")
                ->orWhereHas('broker', function ($q3) use ($value) {
                    $q3->where('name', 'like', "%{$value}%");
                });
            })
            ->orWhereHas('wallet', function ($q2) use ($value) {
                $q2->where('name', 'like', "%{$value}%");
            });
        });
    }

}
