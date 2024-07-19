<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
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
            'property_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'type_id' => 'required|exists:property_types,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'bathrooms' => 'required|integer',
            'bedrooms' => 'required|integer',
            'capacity' => 'required|integer',
            'amenities' => 'array',
            'kitchen_amenities' => 'array',
            'property_status' => 'required|in:sale,rent',

            'pools' => 'nullable|array',
            'pools.*.type' => 'required|in:indoor,outdoor,water_park,heated',
            'pools.*.fence' => 'required|in:with_fence,without_fence',
            'pools.*.is_graduated' => 'required|boolean',
            'pools.*.depth' => 'required|numeric',
            'pools.*.length' => 'required|numeric',
            'pools.*.width' => 'required|numeric',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            'sessions' => 'nullable|array',
            'sessions.*.session_type' => 'nullable|in:main_hall,outdoor_session,dining_table,external_annex,outdoor_seating',
            'sessions.*.capacity' => 'nullable|integer',

            'details' => 'nullable|array',
            'details.check_in_time' => 'nullable|date_format:H:i',
            'details.check_out_time' => 'nullable|date_format:H:i',
            'details.security_deposit' => 'nullable|numeric',
            'details.additional_notes' => 'nullable|string',
        ];
    }
}
