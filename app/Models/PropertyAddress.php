<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'region',
        'locality',
        'postal_code',
        'street',
        'coordinates',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
