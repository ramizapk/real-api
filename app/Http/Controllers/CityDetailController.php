<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityDetailResource;
use App\Models\CityDetail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CityDetailController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $cityDetails = CityDetail::with('city.properties')->get();
        return $this->successResponse(CityDetailResource::collection($cityDetails), 'City details retrieved successfully');
    }
}
