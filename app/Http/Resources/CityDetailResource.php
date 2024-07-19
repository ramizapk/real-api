<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CityDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseUrl = config('app.url'); // Assuming your app URL is configured in Laravel's config
        return [
            'id' => $this->id,
            'city' => new CityResource($this->city),
            'image_url' => $baseUrl . Storage::url($this->image),
            'property_count' => $this->city->properties->count(), // عدد العقارات
        ];
    }
}
