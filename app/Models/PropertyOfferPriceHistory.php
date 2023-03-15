<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyOfferPriceHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offer_id',
        'datetime',
        'price',
        'online',
        'latest',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'datetime' => 'datetime',
        'price' => 'decimal',
        'online' => 'boolean',
        'latest' => 'boolean',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PropertyOffer::class);
    }
}
