<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $city = Property::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'review' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $review = Review::create([
            'property_id' => $request->id,
            'user_id' => $user->id,
            'review' => $request->review,
        ]);

        return $this->successResponse($review, 'Review created successfully.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // التأكد من أن المستخدم هو صاحب التعليق
        if ($review->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $review->delete();

        return $this->successResponse(null, 'Review deleted successfully.');
    }
}
