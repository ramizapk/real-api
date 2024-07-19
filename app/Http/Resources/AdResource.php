<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdResource extends JsonResource
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
            'main_text' => $this->main_text,
            'sub_text' => $this->sub_text,
            'button_text' => $this->button_text,
            'image' => $baseUrl . Storage::url($this->image),
            'ad_type' => $this->ad_type,
            'ad_url' => $this->ad_url,
            'expiration_date' => $this->expiration_date,
        ];
    }
}
