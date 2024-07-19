<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'avatar' => $baseUrl . Storage::url($this->avatar),
            'gender' => $this->gender,
            'dob' => $this->dob ? Carbon::parse($this->dob)->format('Y-m-d') : null,
            'address' => $this->address,
            'level' => $this->level,
            'Registration_date' => $this->created_at,
        ];
    }
}
