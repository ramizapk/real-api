<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'gender' => 'nullable|in:male,female',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'level' => 'nullable|integer|min:1',
        ];
    }
}
