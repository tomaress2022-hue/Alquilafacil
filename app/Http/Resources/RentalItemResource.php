<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'equipment_id' => $this->equipment_id,
            'equipment'    => new EquipmentResource($this->whenLoaded('equipment')),
            'daily_price'  => (float) $this->daily_price,
            'days'         => $this->days,
            'subtotal'     => (float) $this->subtotal,
        ];
    }
}
