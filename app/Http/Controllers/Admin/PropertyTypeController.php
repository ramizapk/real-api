<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Requests\StorePropertyTypeRequest;
use App\Http\Requests\UpdatePropertyTypeRequest;
use App\Http\Resources\PropertyTypeResource;

class PropertyTypeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $propertyTypes = PropertyType::all();
        return $this->successResponse(PropertyTypeResource::collection($propertyTypes), 'Property types retrieved successfully');
    }
    public function store(StorePropertyTypeRequest $request)
    {
        $propertyType = PropertyType::create($request->validated());
        return $this->successResponse(new PropertyTypeResource($propertyType), 'Property type created successfully', 201);

    }

    public function show($id)
    {
        $propertyType = PropertyType::findOrFail($id);
        return $this->successResponse(new PropertyTypeResource($propertyType), 'Property type retrieved successfully');
    }

    public function update(UpdatePropertyTypeRequest $request, $id)
    {
        $propertyType = PropertyType::findOrFail($id);
        $propertyType->update($request->validated());
        return $this->successResponse(new PropertyTypeResource($propertyType), 'Property type updated successfully');
    }

    public function destroy($id)
    {
        $propertyType = PropertyType::findOrFail($id);
        $propertyType->delete();
        return $this->successResponse(null, 'Property type deleted successfully');
    }
}
