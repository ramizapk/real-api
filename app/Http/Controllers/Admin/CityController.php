<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CityController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        return $this->successResponse(CityResource::collection($cities), 'Cities retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->validated());
        return $this->successResponse(new CityResource($city), 'City created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $city = City::findOrFail($id);
        return $this->successResponse(new CityResource($city), 'City retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->validated());
        return $this->successResponse(new CityResource($city), 'City updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return $this->successResponse(null, 'City deleted successfully');
    }
}
