<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'base_currency',
        'quote_currency',
        'rate',
        'date',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:10',
            'date' => 'date',
        ];
    }
}
