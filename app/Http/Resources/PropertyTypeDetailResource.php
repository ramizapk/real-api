<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyTypeDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseUrl = config('app.url');
        return [
            'id' => $this->id,
            'property_type' => new PropertyTypeResource($this->type),
            'image_url' => $baseUrl . Storage::url($this->image),
            'property_count' => $this->type->properties->count(), // عدد العقارات
        ];
    }
}
