<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExportResource extends JsonResource
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
            'host' => [
                $this->host->name,
                $this->host->nip
            ],
            'guest' => [
                $this->guest->name,
                $this->guest->nik
            ],
            'purpose'=> $this->purpose,
            'status' => $this->status,
            'notes' => $this->notes,
            'date_time' => [
                $this->date,
                $this->time
            ]
        ];
    }
}
