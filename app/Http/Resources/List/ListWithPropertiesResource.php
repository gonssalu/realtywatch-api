<?php

namespace App\Http\Resources\List;

use App\Http\Resources\Property\PropertyHeaderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListWithPropertiesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $properties = $this->properties()->paginate(12);
        PropertyHeaderResource::collection($properties); //This line is required
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'num_properties' => $this->properties->count(),
            'properties' => $properties,
        ];
    }
}
