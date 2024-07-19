<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    use ApiResponse;
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        // يحاول البحث عن التقييم الحالي للمستخدم لنفس العقار
        $rating = Rating::updateOrCreate(
            [
                'property_id' => $request->property_id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return $this->successResponse($rating, 'Rating created or updated successfully.');
    }
}
