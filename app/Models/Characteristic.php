<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Characteristic
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyCharacteristic> $valuesForProperties
 * @property-read int|null $values_for_properties_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic query()
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Characteristic whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Characteristic extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function valuesForProperties(): HasMany
    {
        return $this->hasMany(PropertyCharacteristic::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_characteristics', 'characteristic_id', 'property_id')->withPivot('value');
    }

    public function genRandomValue($faker)
    {
        return match ($this->type) {
            'numerical' => $faker->boolean() ? $faker->numberBetween(1, 10000) : $faker->randomFloat(2, 1, 10000),
            'textual' => implode(' ', $faker->words($faker->numberBetween(2, 6))),
            'other' => $faker->dateTime(),
            default => null,
        };
    }
}
