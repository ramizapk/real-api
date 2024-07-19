<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    use ApiResponse;
    public function index()
    {

        $properties = Property::where('availability_status', 'available')
            ->where('request_status', 'approved')
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'offers'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }

    public function show($id)
    {

        $property = Property::with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])->findOrFail($id);
        return $this->successResponse(new PropertyResource($property));
    }
}
