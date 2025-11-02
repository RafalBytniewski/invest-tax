<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'currency_type',
        'currency',
        'value',
        'transaction_id'
    ];

    public function transaction():BelongsTo{
        return $this->belongsTo(Transaction::class);
    }
}
