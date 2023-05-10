<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = [
            'photos' => PropertyMediaResource::collection($this->photos()),
            'videos' => PropertyMediaResource::collection($this->videos()),
            'blueprints' => PropertyMediaResource::collection($this->blueprints()),
        ];
        $address = new PropertyAddressResource($this->address);
        $characteristics = PropertyCharacteristicResource::collection($this->characteristics);

        return [
            'id' => $this->id,
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
            'current_price_sale' => $this->current_price_sale,
            'current_price_rent' => $this->current_price_rent,
            'status' => $this->status,
            'address' => $address,
            'characteristics' => $characteristics,
            'media' => $media,
        ];
    }
}
