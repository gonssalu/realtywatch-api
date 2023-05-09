<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAddressResource extends JsonResource
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
                'adm1_id' => $this->adm1_id,
                'adm1' => ($this->adm1_id ? $this->adm1->name : null),
                'adm2_id' => $this->adm2_id,
                'adm2' => ($this->adm2_id ? $this->adm2->name : null),
                'adm3_id' => $this->adm3_id,
                'adm3' => ($this->adm3_id ? $this->adm3->name : null),
                'full_address' => $this->full_address,
                'coordinates' => $this->coordinates,
            ];
    }
}
