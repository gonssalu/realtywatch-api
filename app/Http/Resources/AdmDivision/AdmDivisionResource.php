<?php

namespace App\Http\Resources\AdmDivision;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmDivisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $has_children = !$this->children->isEmpty();
        $adm = [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'has_children' => $has_children,
        ];

        if ($has_children) {
            $children = $this->children()->whereHas('addresses' . ($this->level + 1), function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })->get();
            return array_merge($adm, ['children' => AdmDivisionResource::collection($children)]);
        }

        return $adm;
    }
}
