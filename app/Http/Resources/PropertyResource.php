<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        $ownerResource = $this->owner_type === 'App\Models\admins' ? new AdminResource($this->owner) : new UserResource($this->owner);

        // الحصول على العروض الغير منتهية
        $activeOffer = $this->offers->where('end_date', '>=', now())->first();

        // حساب نسبة الخصم والسعر بعد الخصم إذا كان هناك عرض نشط
        $discount = $activeOffer ? $activeOffer->discount : null;
        $discountedPrice = $activeOffer ? $this->price - ($this->price * ($discount / 100)) : null;
        $endDate = $activeOffer ? $activeOffer->end_date : null; // تاريخ انتهاء العرض
        return [
            'id' => $this->id,
            'property_name' => $this->property_name,
            'description' => $this->description,
            'price' => $this->price,
            $this->mergeWhen($activeOffer, [
                'discount' => $discount,
                'discounted_price' => $discountedPrice,
                'offer_end_date' => $endDate,
            ]),
            'type' => new PropertyTypeResource($this->type), // Assuming you have a TypeResource for Type model
            'city' => new CityResource($this->city), // Assuming you have a CityResource for City model
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'bathrooms' => $this->bathrooms,
            'bedrooms' => $this->bedrooms,
            'capacity' => $this->capacity,
            'amenities' => json_decode($this->amenities),
            'kitchen_amenities' => json_decode($this->kitchen_amenities),
            'property_status' => $this->property_status,
            'availability_status' => $this->availability_status,
            'request_status' => $this->request_status,
            'average_rating' => $this->average_rating,
            'ratings_count' => $this->ratings_count,
            'is_favorited' => $user ? $this->isFavoritedBy($user->id) : false,


            'reviews' => $this->whenLoaded('reviews', function () {
                return ReviewResource::collection($this->reviews);
            }),

            'pools' => $this->whenLoaded('pools', function () {
                return PoolResource::collection($this->pools); // Transform pools using PoolResource
            }),

            'images' => $this->whenLoaded('images', function () {
                return ImageResource::collection($this->images); // Transform images using ImageResource
            }),

            'sessions' => $this->whenLoaded('sessions', function () {
                return SessionResource::collection($this->sessions); // Transform sessions using SessionResource
            }),

            'detail' => $this->whenLoaded('details', function () {
                return new DetailResource($this->details); // Transform details using DetailResource
            }),

            'owner_type' => $this->owner_type === 'App\Models\admins' ? 'admin' : 'user',
            'owner' => $ownerResource,
            'created_date' => $this->created_at,
            'updated_date' => $this->updated_at,
        ];
    }
}
