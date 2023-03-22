<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'quantity' => $this->quantity,
            'listing_type' => $this->listing_type,
            'title' => $this->title,
            'description' => $this->description,
            'cover_url' => $this->cover_url,
            'useful_area' => $this->useful_area,
            'gross_area' => $this->gross_area,
            'type' => $this->type,
            'typology' => $this->typology,
            'wc' => $this->wc,
            'rating' => $this->rating,
            'current_price_sale' => $this->email,
            'current_price_rent' => $this->photo_url,
            'status' => $this->status,
        ];
    }
}
