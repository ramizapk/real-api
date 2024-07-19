<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UserUpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return $this->successResponse(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeUniqueImage($request->file('avatar'), 'avatars');
        }

        $user = User::create($data);

        return $this->successResponse(new UserResource($user), 'User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->successResponse(new UserResource($user));
    }



    /**
     * Update the specified resource in storage.
     */
    public function updateUser(UserUpdateProfileRequest $request, User $user)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->storeUniqueImage($request->file('avatar'), 'avatars');
        }

        $user->update($data);

        return $this->successResponse(new UserResource($user), 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }
}
