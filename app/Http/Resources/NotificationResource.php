<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = $this->data;

        // تحويل البيانات من JSON إلى مصفوفة
        $data = json_decode($data, true);


        if ($this->type === "App\\Notifications\\NewPropertyAdded" && $this->notifiable_type === "App\\Models\\admins") {
            return [
                'id' => $this->id,
                'title' => "new property added",
                'property_id' => $data['property_id'] ?? null,
                'property_name' => $data['property_name'] ?? null,
                'message' => $data['message'] ?? null,
                'read_at' => $this->read_at ?? "unread",
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
            ];
        } elseif ($this->type === "App\\Notifications\\PropertyStatusChanged" && $this->notifiable_type === "App\\Models\\User") {
            return [
                'id' => $this->id,
                'title' => "your property status changed",
                'property_id' => $data['property_id'] ?? null,
                'property_name' => $data['property_name'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => $data['status'] ?? null,
                'read_at' => $this->read_at ?? "unread",
                'created_at' => $this->created_at->toDateTimeString(),
            ];

        } else {
            return [
                "message" => null,
            ];
        }
    }
}
