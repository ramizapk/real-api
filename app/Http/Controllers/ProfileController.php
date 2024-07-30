<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateProfileRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\UserResource;
use App\Models\admins;
use App\Models\Property;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponse;
    public function update(UserUpdateProfileRequest $request)
    {
        $user = Auth::user();

        // Update profile data
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('gender')) {
            $user->gender = $request->input('gender');
        }
        if ($request->has('bd')) {
            $user->dob = $request->input('bd');
        }
        if ($request->has('address')) {
            $user->address = $request->input('address');
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // Store the new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return $this->successResponse(new UserResource($user), "Profile updated successfully");

    }

    public function userProperties($ownerId, $ownerType)
    {
        $user = $ownerType == "user" ? User::class : admins::class;

        $properties = Property::where('owner_id', $ownerId)
            ->where('owner_type', $user)
            ->with(['pools', 'images', 'sessions', 'details', 'ratings', 'reviews'])
            ->get();

        return $this->successResponse(PropertyResource::collection($properties));
    }

    public function userReviews($ownerId, $ownerType)
    {

        $user = $ownerType == "user" ? User::class : admins::class;
        // الحصول على العقارات التي يملكها المستخدم أو الإداري
        $properties = Property::where('owner_id', $ownerId)
            ->where('owner_type', $user)
            ->with(['reviews', 'ratings'])
            ->get();

        // استخراج جميع التعليقات (المراجعات) من هذه العقارات
        $reviews = $properties->flatMap(function ($property) {
            return $property->reviews;
        });

        // حساب متوسط التقييمات من مجموعة التقييمات
        $averageRating = $properties->flatMap(function ($property) {
            return $property->ratings;
        })->avg('rating');

        return $this->successResponse([
            'reviews' => $reviews,
            'average_rating' => $averageRating
        ]);
    }
}
