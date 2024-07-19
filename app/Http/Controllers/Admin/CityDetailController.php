<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityDetailResource;
use App\Models\CityDetail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CityDetailController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $cityDetails = CityDetail::with('city.properties')->get();
        return $this->successResponse(CityDetailResource::collection($cityDetails), 'City details retrieved successfully');
    }

    public function store(Request $request)
    {
        // التحقق من عدد المدن الحالية
        if (CityDetail::count() >= 10) {
            return $this->errorResponse('Cannot add more than 10 cities', 403);
        }

        $request->validate([
            'city_id' => 'required|exists:cities,id|unique:city_details,city_id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        // رفع الصورة وتخزينها
        $imagePath = $this->storeUniqueImage($request->file('image'), 'city_images');

        $cityDetail = CityDetail::create([
            'city_id' => $request->city_id,
            'image' => $imagePath,
        ]);

        return $this->successResponse(new CityDetailResource($cityDetail), 'City detail created successfully', 201);
    }


    public function show($id)
    {
        $cityDetail = CityDetail::with('city.properties')->find($id);

        if (!$cityDetail) {
            return $this->errorResponse('City detail not found', 404);
        }

        return $this->successResponse(new CityDetailResource($cityDetail), 'City detail retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $cityDetail = CityDetail::find($id);

        if (!$cityDetail) {
            return $this->errorResponse('City detail not found', 404);
        }

        $request->validate([
            'city_id' => 'nullable|exists:cities,id|unique:city_details,city_id,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        // إذا كانت الصورة موجودة في الطلب، نقوم برفعها وتخزينها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($cityDetail->image && Storage::disk('public')->exists($cityDetail->image)) {
                Storage::disk('public')->delete($cityDetail->image);
            }

            $imagePath = $this->storeUniqueImage($request->file('image'), 'city_images');
            $cityDetail->update(['image' => $imagePath]);
        }

        $cityDetail->update([
            'city_id' => $request->city_id,
        ]);

        return $this->successResponse(new CityDetailResource($cityDetail), 'City detail updated successfully');
    }

    public function destroy($id)
    {
        $cityDetail = CityDetail::find($id);

        if (!$cityDetail) {
            return $this->errorResponse('City detail not found', 404);
        }

        $cityDetail->delete();
        return $this->successResponse(null, 'City detail deleted successfully');
    }
}
