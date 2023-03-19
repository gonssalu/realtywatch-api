<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PropertyCharacteristic
 *
 * @property int $id
 * @property int $property_id
 * @property int $characteristic_id
 * @property string $value
 * @property-read \App\Models\Characteristic $characteristic
 * @property-read \App\Models\Property $property
 * @method static \Database\Factories\PropertyCharacteristicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyCharacteristic whereValue($value)
 * @mixin \Eloquent
 */
class PropertyCharacteristic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'characteristic_id',
        'value',
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

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(Characteristic::class);
    }
}
