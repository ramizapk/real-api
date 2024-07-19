<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Traits\ApiResponse;

class UserAdController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the available ads.
     */
    public function index()
    {
        // جلب الإعلانات المتاحة فقط والتي لم تنتهي صلاحيتها
        $currentDate = now();
        $ads = Ad::where('expiration_date', '>=', $currentDate)
            ->orWhereNull('expiration_date')
            ->get();

        return $this->successResponse(AdResource::collection($ads));
    }
}
