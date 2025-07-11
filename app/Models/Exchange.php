<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exchange extends Model
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
        'country',
        'currency'
    ];

    public function assets(): HasMany{
        return $this->hasMany(Asset::class);
    }

    public function wallets(): HasMany{
        return $this->hasMany(Wallet::class);
    }
}
