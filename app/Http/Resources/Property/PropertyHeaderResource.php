<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyHeaderResource extends JsonResource
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
            /*'quantity' => $this->quantity,*/
            'listing_type' => $this->listing_type,
            'title' => $this->title,
            'cover_url' => $this->full_cover_url,
            'type' => $this->type,
            'typology' => $this->typology,
            'wc' => $this->wc,
            'rating' => $this->rating,
            'current_price_sale' => $this->current_price_sale,
            'current_price_rent' => $this->current_price_rent,
            'status' => $this->status,
        ];
    }
}
