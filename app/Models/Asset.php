<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'broker_id'
    ];

    public function broker(): BelongsTo{
        return $this->belongsTo(Broker::class);
    }

    public function transactions(): HasMany{
        return $this->hasMany(Transaction::class);
    }

    public function exchanges(): BelongsToMany
    {
        return $this->belongsToMany(Exchange::class, 'broker_exchange')
                    ->withTimestamps();
    }

}
