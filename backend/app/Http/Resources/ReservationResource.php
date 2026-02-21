<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'reservation_number'   => $this->reservation_number,
            'status'               => $this->status,
            'payment_status'       => $this->payment_status,
            'payment_method'       => $this->payment_method,
            'scheduled_date'       => $this->scheduled_date->format('Y-m-d'),
            'scheduled_time'       => $this->scheduled_time,
            'duration_minutes'     => $this->duration_minutes,
            'price'                => $this->price,
            'special_instructions' => $this->special_instructions,
            'source'               => $this->source,
            'started_at'           => $this->started_at?->toISOString(),
            'completed_at'         => $this->completed_at?->toISOString(),
            'cancelled_at'         => $this->cancelled_at?->toISOString(),
            'cancelled_reason'     => $this->cancelled_reason,
            'created_at'           => $this->created_at->toISOString(),
            'user' => $this->whenLoaded('user', fn() => [
                'id'    => $this->user->id,
                'name'  => $this->user->first_name . ' ' . $this->user->last_name,
                'phone' => $this->user->phone,
                'email' => $this->user->email,
            ]),
            'vehicule' => $this->whenLoaded('vehicule', fn() => [
                'id'           => $this->vehicule->id,
                'type'         => $this->vehicule->type,
                'size'         => $this->vehicule->size,
                'brand'        => $this->vehicule->brand,
                'model'        => $this->vehicule->model,
                'color'        => $this->vehicule->color,
                'plate_number' => $this->vehicule->plate_number,
            ]),
            'service' => $this->whenLoaded('service', fn() => [
                'id'   => $this->service->id,
                'name' => $this->service->name,
            ]),
        ];
    }
}
