<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAppartementRequest extends FormRequest
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
            'title'       => 'required|string|max:30',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'rating'      => 'nullable|numeric|min:0|max:5',
            'images'      => 'required|array',
            'images.*'    => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'space'       => 'required|integer|min:1',
            'rooms'       => 'required|integer|min:1',
            'floor'       => 'required|integer|min:0',
            'city'        => 'required|string',
            'area'        => 'required|string',
            'address'     => 'nullable|string',
        ];
    }
}
