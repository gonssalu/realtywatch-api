<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PropertyOfferPriceHistory
 *
 * @property int $offer_id
 * @property \Illuminate\Support\Carbon $datetime
 * @property string|null $price
 * @property bool $online
 * @property bool $latest
 * @property-read \App\Models\PropertyOffer $offer
 *
 * @method static \Database\Factories\PropertyOfferPriceHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory whereLatest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyOfferPriceHistory wherePrice($value)
 *
 * @mixin \Eloquent
 */
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
