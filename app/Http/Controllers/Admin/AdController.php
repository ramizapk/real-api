<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ads = Ad::all();
        return $this->successResponse(AdResource::collection($ads));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'main_text' => 'nullable|string',
            'sub_text' => 'nullable|string',
            'button_text' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'ad_type' => 'required|in:internal,external',
            'ad_url' => 'nullable|url',
            'expiration_date' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $path = $this->storeUniqueImage($request->file('image'), 'ads');
            $validatedData['image'] = $path;
        }

        $ad = Ad::create($validatedData);

        return $this->successResponse(new AdResource($ad), 'Ad created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        return $this->successResponse(new AdResource($ad));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAd(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        $validatedData = $request->validate([
            'main_text' => 'sometimes|nullable|string',
            'sub_text' => 'sometimes|nullable|string',
            'button_text' => 'sometimes|nullable|string',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ad_type' => 'sometimes|required|in:internal,external',
            'ad_url' => 'sometimes|nullable|url',
            'expiration_date' => 'sometimes|nullable|date',
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $path = $this->storeUniqueImage($request->file('image'), 'ads');
            $validatedData['image'] = $path;
        }

        $ad->update($validatedData);

        return $this->successResponse(new AdResource($ad), 'Ad updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        // حذف الصورة إذا وجدت
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        return $this->successResponse(null, 'Ad deleted successfully.');
    }
}
