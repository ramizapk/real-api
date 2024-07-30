<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\PoolResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\SessionResource;
use App\Models\admins;
use App\Models\Property;
use App\Models\User;
use App\Notifications\NewPropertyAdded;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyDetail;
use App\Models\Pool;
use App\Models\PropertyImage;
use App\Models\Session;
use App\Http\Requests\StorePropertyRequest;
use Illuminate\Support\Facades\Notification;

class PropertyController extends Controller
{
    use ApiResponse;
    public function index()
    {

        $properties = Property::where('availability_status', 'available')
            ->where('request_status', 'approved')
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'offers'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }

    public function show($id)
    {

        $property = Property::with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])->findOrFail($id);
        return $this->successResponse(new PropertyResource($property));
    }

    public function store(StorePropertyRequest $request)
    {
        $user = Auth::user();

        // تحقق من عدد العقارات المضافة للمستخدم ذو المستوى 0
        if ($user->level == 0 && Property::where('owner_id', $user->id)->count() >= 1) {
            return $this->errorResponse('Users with level 0 can only add one property.', 403);
        }

        $validatedData = $request->validated();
        $validatedData['owner_id'] = $user->id;
        $validatedData['owner_type'] = 'App\Models\User';
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
        $adminUsers = admins::all();
        Notification::send($adminUsers, new NewPropertyAdded($property));
        return $this->successResponse(new PropertyResource($property->load(['pools', 'images', 'sessions', 'details'])), 'Property created successfully', 201);
    }


    private function ensureUserOwnsProperty($propertyId)
    {
        $user = Auth::user();
        $property = Property::where('owner_id', $user->id)
            ->where('owner_type', User::class)
            ->where('id', $propertyId)
            ->firstOrFail();
        return $property;
    }
    // إنشاء حمام سباحة جديد لعقار محدد
    public function storePool(Request $request, $propertyId)
    {
        $property = $this->ensureUserOwnsProperty($propertyId);

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

    public function storeImage(Request $request, $propertyId)
    {
        $property = $this->ensureUserOwnsProperty($propertyId);

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

    public function storeSession(Request $request, $propertyId)
    {
        $property = $this->ensureUserOwnsProperty($propertyId);

        $data = $request->validate([
            'session_type' => 'required|in:main_hall,outdoor_session,dining_table,external_annex,outdoor_seating',
            'capacity' => 'required|integer',
        ]);

        $data['property_id'] = $propertyId;
        $session = Session::create($data);

        return $this->successResponse(new SessionResource($session), 'Session added successfully', 201);
    }

    public function storeDetail(Request $request, $propertyId)
    {
        $property = $this->ensureUserOwnsProperty($propertyId);

        $existingDetail = PropertyDetail::where('property_id', $propertyId)->first();
        if ($existingDetail) {
            return response()->json(['message' => 'The property already has details.'], 400);
        }

        $data = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'security_deposit' => 'nullable|numeric',
            'additional_notes' => 'nullable|string',
        ]);

        $data['property_id'] = $propertyId;
        $detail = PropertyDetail::create($data);

        return $this->successResponse(new DetailResource($detail), 'Detail added successfully', 201);
    }



    public function userProperties()
    {
        $user = Auth::user();
        $properties = Property::where('owner_id', $user->id)
            ->where('owner_type', User::class)
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }


    public function approvedProperties()
    {
        $user = Auth::user();
        $properties = Property::where('owner_id', $user->id)
            ->where('owner_type', User::class)
            ->where('request_status', 'approved')
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }

    public function rejectedProperties()
    {
        $user = Auth::user();
        $properties = Property::where('owner_id', $user->id)
            ->where('owner_type', User::class)
            ->where('request_status', 'rejected')
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }


    public function deleteRejectedProperty($id)
    {
        $user = Auth::user();
        $property = Property::where('id', $id)
            ->where('owner_id', $user->id)
            ->where('owner_type', User::class)
            ->where('request_status', 'rejected')
            ->firstOrFail();

        $property->delete();

        return $this->successResponse(null, 'Property deleted successfully');
    }
}
