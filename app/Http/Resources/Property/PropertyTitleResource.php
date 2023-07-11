<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTitleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lists = $this->lists;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'collection_ids' => $lists->pluck('id'),
        ];
    }
}
