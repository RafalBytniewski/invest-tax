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
        'id',
        'name',
        'symbol',
        'country',
        'currency',
        'timezone',
        'url',
        'trading_hours',
        'image',
    ];

    public function assets(): HasMany{
        return $this->hasMany(Asset::class);
    }

}
