<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exchange extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'country',
        'currency',
        'timezone',
        'url',
        'trading_hours',
        'image',
    ];

    public function brokers(): BelongsToMany
    {
        return $this->belongsToMany(Broker::class, 'broker_exchange')
                    ->withTimestamps();
    }
}
