<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'property' => [
                'id' => $this->property->id,
                'property_name' => $this->property->property_name,
                'price' => $this->property->price,
                'type' => new PropertyTypeResource($this->property->type), // Assuming you have a TypeResource for Type model
                'images' => ImageResource::collection($this->property->images),
            ],
        ];
    }
}
