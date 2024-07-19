<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\DetailResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\PoolResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\SessionResource;
use App\Models\admins;
use App\Models\Property;
use App\Models\Pool;
use App\Models\PropertyImage;
use App\Models\Session;
use App\Models\PropertyDetail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    // Property CRUD operations

    /**
     * Display a listing of the resource.
     */
    use ApiResponse; // استخدام تريت ApiResponse
    public function index()
    {
        $properties = Property::with(['pools', 'images', 'sessions', 'details'])->get();
        return $this->successResponse(PropertyResource::collection($properties));
    }

    public function show($id)
    {
        $property = Property::with(['pools', 'images', 'sessions', 'details'])->findOrFail($id);
        return $this->successResponse(new PropertyResource($property));
    }

    public function store(StorePropertyRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['owner_id'] = Auth::id();
        $validatedData['owner_type'] = admins::class;
        $validatedData['amenities'] = json_encode($validatedData['amenities']);
        $validatedData['kitchen_amenities'] = json_encode($validatedData['kitchen_amenities']);
        $property = Property::create($validatedData);

        if ($request->has('pools')) {
            foreach ($request->input('pools') as $pool) {
                $property->pools()->create($pool);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('property_images', $filename, 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => $path,
                ]);
            }
        }

        if ($request->has('sessions')) {
            foreach ($request->input('sessions') as $session) {
                $property->sessions()->create($session);
            }
        }

        if ($request->has('details')) {
            $property->details()->create($request->input('details'));
        }

        return $this->successResponse(new PropertyResource($property->load(['pools', 'images', 'sessions', 'details'])), 'Property created successfully', 201);
    }

    public function update(UpdatePropertyRequest $request, $id)
    {
        $validatedData = $request->validated();
        // Encode amenities only if they exist in the request
        if (array_key_exists('amenities', $validatedData)) {
            $validatedData['amenities'] = json_encode($validatedData['amenities']);
        }

        // Encode kitchen_amenities only if they exist in the request
        if (array_key_exists('kitchen_amenities', $validatedData)) {
            $validatedData['kitchen_amenities'] = json_encode($validatedData['kitchen_amenities']);
        }

        $property = Property::findOrFail($id);
        $property->update($validatedData);
        // return $this->successResponse($request->images, 'Property updated successfully');
        if ($request->has('pools')) {
            $property->pools()->delete();
            foreach ($request->input('pools') as $pool) {
                $property->pools()->create($pool);
            }
        }

        if ($request->hasFile('images')) {

            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();

            }
            $property->images()->delete();




            foreach ($request->file("images") as $image) {

                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('property_images', $filename, 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_url' => $path,
                ]);
            }
        }

        if ($request->has('sessions')) {
            $property->sessions()->delete();
            foreach ($request->input('sessions') as $session) {
                $property->sessions()->create($session);
            }
        }

        if ($request->has('details')) {
            $property->details()->delete();
            $property->details()->create($request->input('details'));
        }

        return $this->successResponse(new PropertyResource($property->load(['pools', 'images', 'sessions', 'details'])), 'Property updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_url);
            $image->delete();
        }
        $property->delete();
        return $this->successResponse(null, 'Property deleted successfully');
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $request->validate([
            'request_status' => 'required|in:pending,approved,rejected',
        ]);

        $property = Property::findOrFail($id);
        $property->request_status = $request->input('request_status');
        $property->save();
        return $this->successResponse(new PropertyResource($property->load(['pools', 'images', 'sessions', 'details'])), 'Request status updated successfully');
    }
    public function updateAvailabilityStatus(Request $request, $id)
    {
        $request->validate([
            'availability_status' => 'required|in:available,unavailable,rented,sold',
        ]);

        $property = Property::findOrFail($id);
        $property->availability_status = $request->input('availability_status');
        $property->save();

        return $this->successResponse(new PropertyResource($property->load(['pools', 'images', 'sessions', 'details'])), 'Availability status updated successfully');
    }




    // Additional CRUD operations for Pools, Images, Sessions, and Details

    // Pools

    public function showPools($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $pools = Pool::where('property_id', $propertyId)->get();
        return $this->successResponse(PoolResource::collection($pools));
    }

    public function storePool(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'type' => 'required|in:indoor,outdoor,water_park,heated',
            'fence' => 'required|in:with_fence,without_fence',
            'is_graduated' => 'required|boolean',
            'depth' => 'required|numeric',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
        ]);

        $data['property_id'] = $propertyId;
        $pool = Pool::create($data);

        return $this->successResponse(new PoolResource($pool), 'Pool added successfully', 201);
    }
    public function updatePool(Request $request, $propertyId, $poolId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'type' => 'required|in:indoor,outdoor,water_park,heated',
            'fence' => 'required|in:with_fence,without_fence',
            'is_graduated' => 'nullable|boolean',
            'depth' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
        ]);

        $pool = Pool::where('property_id', $propertyId)->findOrFail($poolId);
        $pool->update($data);

        return $this->successResponse(new PoolResource($pool), 'Pool updated successfully');
    }

    public function destroyPool($propertyId, $poolId)
    {
        $property = Property::findOrFail($propertyId);
        $pool = Pool::where('property_id', $propertyId)->findOrFail($poolId);
        $pool->delete();

        return $this->successResponse(null, 'Pool deleted successfully');
    }

    public function destroyAllPools($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $pools = Pool::where('property_id', $propertyId)->get();

        foreach ($pools as $pool) {
            $pool->delete();
        }

        return $this->successResponse(null, 'All pools deleted successfully');
    }







    // Property Images
    public function showImages($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $images = PropertyImage::where('property_id', $propertyId)->get();
        return $this->successResponse(ImageResource::collection($images));
    }


    public function storeImage(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $filename = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
        $path = $request->file('image')->storeAs('property_images', $filename, 'public');
        $data['image_url'] = $path;
        $data['property_id'] = $propertyId;

        $PropertyImage = PropertyImage::create($data);

        return $this->successResponse(new ImageResource($PropertyImage), 'Image added successfully', 201);
    }
    public function updateImage(Request $request, $propertyId, $imageId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = PropertyImage::where('property_id', $propertyId)->findOrFail($imageId);

        Storage::disk('public')->delete($image->image_url);
        $filename = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
        $path = $request->file('image')->storeAs('property_images', $filename, 'public');
        $data['image_url'] = $path;

        $image->update($data);

        return $this->successResponse(new ImageResource($image), 'Image updated successfully');
    }

    public function destroyImage($propertyId, $imageId)
    {
        $property = Property::findOrFail($propertyId);
        $image = PropertyImage::where('property_id', $propertyId)->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_url);
        $image->delete();

        return $this->successResponse(null, 'Image deleted successfully');
    }

    public function destroyAllImages($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $images = PropertyImage::where('property_id', $propertyId)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_url);
            $image->delete();
        }

        return $this->successResponse(null, 'All images deleted successfully');
    }







    // Sessions
    public function showSessions($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $session = Session::where('property_id', $propertyId)->get();
        return $this->successResponse(SessionResource::collection($session));
    }



    public function storeSession(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'session_type' => 'required|in:main_hall,outdoor_session,dining_table,external_annex,outdoor_seating',
            'capacity' => 'required|integer',
        ]);

        $data['property_id'] = $propertyId;
        $session = Session::create($data);

        return $this->successResponse(new SessionResource($session), 'session added successfully', 201);
    }





    public function updateSession(Request $request, $propertyId, $sessionId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'session_type' => 'required|in:main_hall,outdoor_session,dining_table,external_annex,outdoor_seating',
            'capacity' => 'required|integer',
        ]);

        $session = Session::where('property_id', $propertyId)->findOrFail($sessionId);
        $session->update($data);

        return $this->successResponse(new SessionResource($session), 'Session updated successfully');
    }

    public function destroySession($propertyId, $sessionId)
    {
        $property = Property::findOrFail($propertyId);
        $session = Session::where('property_id', $propertyId)->findOrFail($sessionId);
        $session->delete();

        return $this->successResponse(null, 'Session deleted successfully');
    }


    public function destroyAllSessions($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $sessions = Session::where('property_id', $propertyId)->get();

        foreach ($sessions as $session) {
            $session->delete();
        }

        return $this->successResponse(null, 'All Sessions deleted successfully');
    }





    // Property Details
    public function showDetail($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $detail = PropertyDetail::where('property_id', $propertyId)->get();
        return $this->successResponse(DetailResource::collection($detail));
    }

    public function storeDetail(Request $request, $propertyId)
    {
        // تحقق مما إذا كانت هناك تفاصيل موجودة للعقار المحدد
        $property = Property::findOrFail($propertyId);
        $existingDetail = PropertyDetail::where('property_id', $propertyId)->first();

        if ($existingDetail) {
            return response()->json([
                'message' => 'The property already has details.',
            ], 400);
        }

        // تحقق من صحة البيانات المدخلة
        $data = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'security_deposit' => 'nullable|numeric',
            'additional_notes' => 'nullable|string',
        ]);

        // إضافة معرف العقار إلى البيانات
        $data['property_id'] = $propertyId;

        // إنشاء تفاصيل العقار الجديدة
        $detail = PropertyDetail::create($data);

        // إرجاع استجابة بنجاح العملية
        return $this->successResponse(new DetailResource($detail), 'Detail added successfully', 201);
    }


    public function updateDetail(Request $request, $propertyId, $detailId)
    {
        $property = Property::findOrFail($propertyId);
        $data = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'security_deposit' => 'nullable|numeric',
            'additional_notes' => 'nullable|string',
        ]);

        $detail = PropertyDetail::where('property_id', $propertyId)->findOrFail($detailId);
        $detail->update($data);

        return $this->successResponse(new DetailResource($detail), 'Detail updated successfully');
    }

    public function destroyDetail($propertyId, $detailId)
    {
        $property = Property::findOrFail($propertyId);
        $detail = PropertyDetail::where('property_id', $propertyId)->findOrFail($detailId);
        $detail->delete();

        return $this->successResponse(null, 'Detail deleted successfully');
    }
}
