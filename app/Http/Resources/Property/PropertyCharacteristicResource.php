<?php

namespace App\Http\Resources\Property;;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCharacteristicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'name' => $this->name,
                'type' => $this->type,
                'value' => $this->pivot->value,
            ];
    }
}
