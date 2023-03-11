<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'quantity',
        'title',
        'description',
        'cover_url',
        'total_area',
        'gross_area',
        'type',
        'typology',
        'rating',
        'current_price',
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
        'total_area' => 'decimal',
        'gross_area' => 'decimal',
        'rating' => 'integer',
        'current_price' => 'decimal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
