<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyTypeDetail;
use App\Traits\ApiResponse;
use App\Http\Resources\PropertyTypeDetailResource;

class PropertyTypeDetailController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $propertyTypeDetails = PropertyTypeDetail::with('type.properties')->get();
        return $this->successResponse(PropertyTypeDetailResource::collection($propertyTypeDetails), 'Property type details retrieved successfully');

    }

    public function store(Request $request)
    {
        // التحقق من عدد الأنواع الحالية
        if (PropertyTypeDetail::count() >= 10) {
            return $this->errorResponse('Cannot add more than 10 property types', 403);
        }

        $request->validate([
            'type_id' => 'required|exists:property_types,id|unique:property_type_details,type_id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        // رفع الصورة وتخزينها
        $imagePath = $this->storeUniqueImage($request->file('image'), 'property_type_images');

        $propertyTypeDetail = PropertyTypeDetail::create([
            'type_id' => $request->type_id,
            'image' => $imagePath,
        ]);

        return $this->successResponse(new PropertyTypeDetailResource($propertyTypeDetail), 'Property type detail created successfully', 201);
    }

    public function show($id)
    {
        $propertyTypeDetail = PropertyTypeDetail::with('type.properties')->find($id);

        if (!$propertyTypeDetail) {
            return $this->errorResponse('Property type detail not found', 404);
        }

        return $this->successResponse(new PropertyTypeDetailResource($propertyTypeDetail), 'Property type detail retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $propertyTypeDetail = PropertyTypeDetail::find($id);

        if (!$propertyTypeDetail) {
            return $this->errorResponse('Property type detail not found', 404);
        }

        $request->validate([
            'type_id' => 'required|exists:property_types,id|unique:property_type_details,type_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        // إذا كانت الصورة موجودة في الطلب، نقوم برفعها وتخزينها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($propertyTypeDetail->image && Storage::disk('public')->exists($propertyTypeDetail->image)) {
                Storage::disk('public')->delete($propertyTypeDetail->image);
            }

            $imagePath = $this->storeUniqueImage($request->file('image'), 'property_type_images');
            $propertyTypeDetail->update(['image' => $imagePath]);
        }

        $propertyTypeDetail->update([
            'type_id' => $request->type_id,
        ]);

        return $this->successResponse(new PropertyTypeDetailResource($propertyTypeDetail), 'Property type detail updated successfully');
    }

    public function destroy($id)
    {
        $propertyTypeDetail = PropertyTypeDetail::find($id);

        if (!$propertyTypeDetail) {
            return $this->errorResponse('Property type detail not found', 404);
        }

        // حذف الصورة إذا كانت موجودة
        if ($propertyTypeDetail->image && Storage::disk('public')->exists($propertyTypeDetail->image)) {
            Storage::disk('public')->delete($propertyTypeDetail->image);
        }

        $propertyTypeDetail->delete();
        return $this->successResponse(null, 'Property type detail deleted successfully', 204);
    }
}
