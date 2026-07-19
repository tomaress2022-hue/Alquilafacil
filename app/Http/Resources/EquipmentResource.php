<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'category_id' => $this->category_id,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'name'        => $this->name,
            'code'        => $this->code,
            'description' => $this->description,
            'daily_price' => (float) $this->daily_price,
            'status'      => $this->status,
            'is_available' => $this->isAvailable(),
            'image_url'   => $this->imageUrl(),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
