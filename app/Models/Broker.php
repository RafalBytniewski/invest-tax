<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'image',
        'url',
        'country'
    ];

    public function wallets(): HasMany{
        return $this->hasMany(Wallet::class);
    }

    public function assets(): BelongsToMany{
        return $this->belongsToMany(Asset::class);
    }
}
