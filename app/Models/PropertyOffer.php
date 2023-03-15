<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyOffer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'url',
        'description',
        'agency_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'property_id' => 'integer',
        'agency_id' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PropertyOfferPriceHistory::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
