<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $cities = City::all();
        return $this->successResponse(CityResource::collection($cities), 'Cities retrieved successfully');
    }
}
