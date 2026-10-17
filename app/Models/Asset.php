<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'image',
        'symbol',
        'asset_type',
        'sector',
        'industry',
        'exchange_id',
    ];

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function brokers(): BelongsToMany
    {
        return $this->belongsToMany(Broker::class);
    }

    public function assetPrices(): HasMany
    {
        return $this->hasMany(AssetPrice::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(AssetPrice::class)->latestOfMany('date');
    }

    public function dividends(): HasMany
    {
        return $this->hasMany(Dividend::class);
    }
}
