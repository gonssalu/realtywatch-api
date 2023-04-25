<?php

namespace App\Http\Resources;

use App\Http\Resources\Property\PropertyHeaderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            /*'properties' => PropertyHeaderResource::collection($this->properties),*/
            'num_properties' => $this->properties->count(),
        ];
    }
}
