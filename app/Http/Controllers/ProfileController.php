<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateProfileRequest;
use App\Http\Resources\UserResource;
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
}
