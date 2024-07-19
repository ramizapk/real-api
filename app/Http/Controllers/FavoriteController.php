<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Http\Resources\PropertyResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)->with('property')->get();

        return $this->successResponse(FavoriteResource::collection($favorites), 'Favorites retrieved successfully');
    }

    /**
     * Add a property to favorites.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $request->property_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return $this->successResponse(null, 'Property removed from favorites successfully');
        } else {
            $favorite = Favorite::create([
                'user_id' => $user->id,
                'property_id' => $request->property_id,
            ]);
            return $this->successResponse($favorite, 'Property added to favorites successfully');
        }
    }

    /**
     * Remove a property from favorites.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)->where('id', $id)->first();

        if (!$favorite) {
            return $this->errorResponse('Favorite not found', 404);
        }

        $favorite->delete();

        return $this->successResponse(null, 'Property removed from favorites successfully');
    }
}
