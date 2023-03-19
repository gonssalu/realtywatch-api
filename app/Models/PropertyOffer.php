<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PropertyOffer
 *
 * @property int $id
 * @property int $property_id
 * @property string $url
 * @property string|null $description
 * @property string $listing_type
 * @property int|null $agency_id
 * @property-read \App\Models\Agency|null $agency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyOfferPriceHistory> $priceHistory
 * @property-read int|null $price_history_count
 * @property-read \App\Models\Property $property
 * @method static \Database\Factories\PropertyOfferFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer whereAgencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer whereListingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOffer whereUrl($value)
 * @mixin \Eloquent
 */
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
        'listing_type',
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
