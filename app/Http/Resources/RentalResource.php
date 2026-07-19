<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'client_id'    => $this->client_id,
            'client'       => new UserResource($this->whenLoaded('client')),
            'status'       => $this->status,
            'status_label' => $this->statusLabel(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'start_date'   => $this->start_date?->toDateString(),
            'end_date'     => $this->end_date?->toDateString(),
            'days'         => $this->start_date && $this->end_date ? $this->calculateDays() : null,
            'total_price'  => (float) $this->total_price,
            'notes'        => $this->notes,
            'items'        => RentalItemResource::collection($this->whenLoaded('items')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
