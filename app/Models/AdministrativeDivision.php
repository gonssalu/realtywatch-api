<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\AdministrativeDivision
 *
 * @property int $id
 * @property string $name
 * @property int $level
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereParentId($value)
 *
 * @mixin \Eloquent
 */
class AdministrativeDivision extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name',
    //     'level',
    // ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'level' => 'integer',
        'parent_id' => 'integer',
    ];

    public function addresses1(): HasMany
    {
        return $this->hasMany(PropertyAddress::class, 'adm1_id');
    }

    public function addresses2(): HasMany
    {
        return $this->hasMany(PropertyAddress::class, 'adm2_id');
    }

    public function addresses3(): HasMany
    {
        return $this->hasMany(PropertyAddress::class, 'adm3_id');
    }

    public function addresses()
    {
        switch ($this->level) {
            case 1:
                return $this->addresses1();
            case 2:
                return $this->addresses2();
            case 3:
                return $this->addresses3();
        }
    }

    public function children(): HasMany
    {
        return $this->hasMany(AdministrativeDivision::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'parent_id');
    }
}
