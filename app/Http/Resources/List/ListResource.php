<?php

namespace App\Http\Resources\List;

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
            'num_properties' => $this->properties->count(),
            'tags' => $this->tags->pluck('name'),
            'covers' => $this->properties->where('cover_url', '!=', null)->pluck('full_cover_url')->shuffle()->take(4),
        ];
    }
}
