<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && ! auth()->user()->business()->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'name' => ['required', 'string', 'max:255'],
            'front_image' => ['required', 'image', 'max:2048'],
            'description' => ['required', 'string', 'min:25'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'website_url' => ['required', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'geo_location' => ['nullable', 'string'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id']
        ];
    }
}
