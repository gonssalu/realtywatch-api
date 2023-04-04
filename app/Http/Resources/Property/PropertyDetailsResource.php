<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDetailsResource extends JsonResource
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
            'description' => $this->description,
            'useful_area' => $this->useful_area,
            'gross_area' => $this->gross_area,
        ];
    }
}
