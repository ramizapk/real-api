<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoolResource extends JsonResource
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
            'type' => $this->type,
            'fence' => $this->fence,
            'is_graduated' => $this->is_graduated,
            'depth' => $this->depth,
            'length' => $this->length,
            'width' => $this->width,
        ];
    }
}
