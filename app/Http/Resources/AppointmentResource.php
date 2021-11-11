<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'host' => new HostResource($this->host),
            'guest' => new GuestResource($this->guest),
            'purpose'=> $this->purpose,
            'notes' => $this->notes,
            'status' => $this->status,
            'date_time' => [
                $this->date,
                $this->time
            ],
            'created_at' => $this->created_at
        ];
    }
}
