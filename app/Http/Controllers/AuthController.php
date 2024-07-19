<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Register or login with phone number and send verification code.
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = User::where('phone', $request->phone)->first();

        // Generate random verification code
        $verificationCode = mt_rand(100000, 999999);

        if (!$user) {
            // User does not exist, create new user
            $user = User::create([
                'phone' => $request->phone,
                'verification_code' => $verificationCode,
            ]);
        } else {
            // User exists, update verification code
            $user->verification_code = $verificationCode;
            $user->save();
        }

        // Send verification code via WhatsApp
        $recipientNumber = $request->phone;
        $message = $verificationCode;
        $response = $this->whatsAppService->sendWhatsAppMessage($recipientNumber, $message);


        if ($response === true) {
            $resposeMessage = [
                'message' => 'Verification code sent successfully',
                'recipientNumber' => $recipientNumber,
                'verificationCode' => $verificationCode,
            ];

            return $this->successResponse($resposeMessage, 'Verification code sent successfully');
        } else {
            return $this->errorResponse('Failed to send verification code', 422, $response);

        }


    }

    /**
     * Verify user with verification code.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = User::where('phone', $request->phone)->where('verification_code', $request->code)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid verification code'], 401);
        }

        // Perform authentication and generate token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'messagme' => "your login now",
            'phoneNumber' => $user->phone,
            'token' => $token,
        ], 200);
    }
}
