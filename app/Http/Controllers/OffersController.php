<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    use ApiResponse;

    public function propertiesWithActiveOffers()
    {
        $properties = Property::whereHas('offers', function ($query) {
            $query->where('end_date', '>=', now());
        })->with(['pools', 'images', 'sessions', 'details', 'ratings', 'offers'])->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }
}
