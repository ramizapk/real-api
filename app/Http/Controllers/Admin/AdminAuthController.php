<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponse;

class AdminAuthController extends Controller
{
    use ApiResponse;

    // تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = admins::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', 422);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return $this->successResponse(['token' => $token], 'Login successful');
    }

    // تسجيل حساب إدمن جديد
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins',
            'phone' => 'required|string|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = admins::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'avatar' => $request->avatar,
            'role' => 'admin',
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse(['admin' => $admin], 'Admin registered successfully', 201);
    }
}
