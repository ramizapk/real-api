<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyTypeResource;
use App\Models\PropertyType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $propertyTypes = PropertyType::all();
        return $this->successResponse(PropertyTypeResource::collection($propertyTypes), 'Property types retrieved successfully');
    }
}
