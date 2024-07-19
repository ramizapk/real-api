<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Traits\ApiResponse;

class OffersController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $offers = Offer::with('property')->get();
        return $this->successResponse(OfferResource::collection($offers));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'offer_title' => 'required|string|max:255',
            'offer_description' => 'required|string',
            'discount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $offer = Offer::create($validatedData);
        return $this->successResponse(new OfferResource($offer), 'Offer created successfully.');
    }

    public function show($id)
    {
        $offer = Offer::with('property')->findOrFail($id);
        return $this->successResponse(new OfferResource($offer));
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $validatedData = $request->validate([
            'property_id' => 'sometimes|required|exists:properties,id',
            'offer_title' => 'sometimes|required|string|max:255',
            'offer_description' => 'sometimes|required|string',
            'discount' => 'sometimes|required|numeric',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
        ]);

        $offer->update($validatedData);
        return $this->successResponse(new OfferResource($offer), 'Offer updated successfully.');
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();
        return $this->successResponse(null, 'Offer deleted successfully.');
    }
}
