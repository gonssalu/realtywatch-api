<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Property
 *
 * @property int $id
 * @property int $user_id
 * @property string $listing_type
 * @property int|null $quantity
 * @property string $title
 * @property string|null $description
 * @property string|null $cover_url
 * @property string|null $gross_area
 * @property string|null $useful_area
 * @property string|null $type
 * @property string|null $typology
 * @property int|null $wc
 * @property int|null $rating
 * @property string|null $current_price_sale
 * @property string|null $current_price_rent
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\PropertyAddress|null $address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyCharacteristic> $characteristics
 * @property-read int|null $characteristics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyList> $lists
 * @property-read int|null $lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyMedia> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyOffer> $offers
 * @property-read int|null $offers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyOfferPriceHistory> $priceHistories
 * @property-read int|null $price_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCoverUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCurrentPriceRent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCurrentPriceSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereGrossArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereListingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereTypology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUsefulArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereWc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'quantity',
        'listing_type',
        'title',
        'description',
        'cover_url',
        'gross_area',
        'useful_area',
        'type',
        'typology',
        'wc',
        'rating',
        'current_price_sale',
        'current_price_rent',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'quantity' => 'integer',
        'rating' => 'integer',
    ];

    public function getFullCoverUrlAttribute(): string|null
    {
        return $this->cover_url == null ? null : asset('storage/properties/' . $this->cover_url);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(PropertyList::class, 'list_property', 'property_id', 'list_id')->withPivot('order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function photos()
    {
        return $this->media->where('type', 'image');
    }

    public function videos()
    {
        return $this->media->where('type', 'video');
    }

    public function blueprints()
    {
        return $this->media->where('type', 'blueprint');
    }

    public function address(): HasOne
    {
        return $this->hasOne(PropertyAddress::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PropertyOffer::class);
    }

    public function offersSale()
    {
        return $this->offers->where('listing_type', 'sale');
    }

    public function offersRent()
    {
        return $this->offers->where('listing_type', 'rent');
    }

    public function priceHistories(): HasManyThrough
    {
        return $this->hasManyThrough(PropertyOfferPriceHistory::class, PropertyOffer::class, 'property_id', 'offer_id');
    }

    public function characteristics(): BelongsToMany
    {
        return $this->belongsToMany(Characteristic::class, 'property_characteristics', 'property_id', 'characteristic_id')->withPivot('value');
    }
}
