<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'type_id' => 'sometimes|required|exists:property_types,id',
            'city_id' => 'sometimes|required|exists:cities,id',
            'address' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'bathrooms' => 'sometimes|required|integer',
            'bedrooms' => 'sometimes|required|integer',
            'capacity' => 'sometimes|required|integer',
            'amenities' => 'sometimes|required|array',
            'kitchen_amenities' => 'sometimes|required|array',
            'property_status' => 'sometimes|required|in:sale,rent',
            'availability_status' => 'sometimes|required|in:available,unavailable,rented,sold',
            'request_status' => 'sometimes|required|in:pending,approved,rejected',

            'pools' => 'nullable|array',
            'pools.*.type' => 'nullable|in:indoor,outdoor,water_park,heated',
            'pools.*.fence' => 'nullable|in:with_fence,without_fence',
            'pools.*.is_graduated' => 'nullable|boolean',
            'pools.*.depth' => 'nullable|numeric',
            'pools.*.length' => 'nullable|numeric',
            'pools.*.width' => 'nullable|numeric',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            'sessions' => 'nullable|array',
            'sessions.*.type' => 'nullable|in:main_hall,outdoor_session,dining_table,external_annex,outdoor_seating',
            'sessions.*.capacity' => 'nullable|integer',

            'details' => 'nullable|array',
            'details.check_in_time' => 'nullable|date_format:H:i',
            'details.check_out_time' => 'nullable|date_format:H:i',
            'details.security_deposit' => 'nullable|numeric',
            'details.additional_notes' => 'nullable|string',
        ];
    }
}
