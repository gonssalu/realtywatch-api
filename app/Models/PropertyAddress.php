<?php

namespace App\Models;

use DB;
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
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\PropertyAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyAddress whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PropertyAddress extends Model
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
        'postal_code',
        'adm1_id',
        'adm2_id',
        'adm3_id',
        'full_address',
        'user_id',
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
        'full_address' => 'string',
    ];

    protected $geometry = ['coordinates'];

    /**
     * Select geometrical attributes as text from database.
     *
     * @var bool
     */
    protected $geometryAsText = true;

    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param  bool  $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery($excludeDeleted = true)
    {
        if (!empty($this->geometry) && $this->geometryAsText === true) {
            $raw = '';
            foreach ($this->geometry as $column) {
                $raw .= 'ST_X(`' . $this->table . '`.`' . $column . '`) as `' . $column . '_lat`, ST_Y(`' . $this->table . '`.`' . $column . '`) as `' . $column . '_lon`, ';
            }
            $raw = substr($raw, 0, -2);

            return parent::newQuery($excludeDeleted)->addSelect('*', DB::raw($raw));
        }

        return parent::newQuery($excludeDeleted);
    }

    // Override coordinates attribute
    public function getCoordinatesAttribute()
    {
        return ['latitude' => $this->coordinates_lat, 'longitude' => $this->coordinates_lon];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function adm1(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'adm1_id');
    }

    public function adm2(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'adm2_id');
    }

    public function adm3(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'adm3_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
