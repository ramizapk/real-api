<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Resources\PropertyTypeDetailResource;
use App\Models\PropertyTypeDetail;

class PropertyTypeDetailController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $propertyTypeDetails = PropertyTypeDetail::with('type.properties')->get();
        return $this->successResponse(PropertyTypeDetailResource::collection($propertyTypeDetails), 'Property type details retrieved successfully');

    }
}
