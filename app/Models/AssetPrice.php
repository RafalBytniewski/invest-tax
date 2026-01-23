<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'open_price',
        'low_price',
        'high_price',
        'close_price',
        'source',
        'date',
        'asset_id'
    ];

    public function asset(): BelongsTo{
        return $this->belongsTo(Asset::class);
    }
}
