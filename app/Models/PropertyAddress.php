<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PropertyAddress
 *
 * @property-read \App\Models\AdministrativeDivision|null $adm1
 * @property-read \App\Models\AdministrativeDivision|null $adm2
 * @property-read \App\Models\AdministrativeDivision|null $adm3
 * @property-read \App\Models\Property|null $property
 * @method static \Database\Factories\PropertyAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress query()
 * @mixin \Eloquent
 */
class PropertyAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'country',
        'adm1_id',
        'adm2_id',
        'adm3_id',
        'full_address',
        'coordinates',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'adm1_id' => 'integer',
        'adm2_id' => 'integer',
        'adm3_id' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function adm1(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class);
    }

    public function adm2(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class);
    }

    public function adm3(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class);
    }
}
