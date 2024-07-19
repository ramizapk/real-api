<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;

use App\Models\admins;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = admins::all();
        return $this->successResponse(AdminResource::collection($admins));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']); // تأكد من تشفير كلمة المرور

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeUniqueImage($request->file('avatar'), 'avatars');
        }
        $data['role'] = 'admin';

        $admin = admins::create($data);

        return $this->successResponse(new AdminResource($admin), 'Admin created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(admins $admin)
    {
        return $this->successResponse(new AdminResource($admin));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, admins $admin)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $this->storeUniqueImage($request->file('avatar'), 'avatars');
        }

        $admin->update($data);

        return $this->successResponse(new AdminResource($admin), 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(admins $admin)
    {
        if ($admin->avatar) {
            Storage::disk('public')->delete($admin->avatar);
        }
        $admin->delete();

        return $this->successResponse(null, 'Admin deleted successfully');
    }
}
