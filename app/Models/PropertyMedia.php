<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PropertyMedia
 *
 * @property int $id
 * @property int $property_id
 * @property string $type
 * @property string $url
 * @property-read \App\Models\Property $property
 *
 * @method static \Database\Factories\PropertyMediaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyMedia whereUrl($value)
 *
 * @mixin \Eloquent
 */
class PropertyMedia extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'type',
        'order',
        'url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'property_id' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
