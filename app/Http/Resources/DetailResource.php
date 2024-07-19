<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
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
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'security_deposit' => $this->security_deposit,
            'additional_notes' => $this->additional_notes,
        ];
    }
}
