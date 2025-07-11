<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'exchange_id'
    ];

    public function transactions(): HasMany{
        return $this->hasMany(Transaction::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    
    public function exchange(): BelongsTo{
        return $this->belongsTo(Exchange::class);
    }
}
