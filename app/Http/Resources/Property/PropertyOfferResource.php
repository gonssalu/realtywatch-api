<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $priceHistory = PropertyOfferHistoryResource::collection($this->priceHistory->sortByDesc('datetime')->sortByDesc('latest'));

        return
            [
                'id' => $this->id,
                'url' => $this->url,
                'description' => $this->description,
                'price_history' => $priceHistory,
                'price' => $priceHistory->first()->price,
            ];
    }
}
