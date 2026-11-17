<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'country'       => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'          => 'This email is already taken by another account.',
            'profile_photo.mimes'   => 'Photo must be a jpg, jpeg, png, or webp file.',
            'profile_photo.max'     => 'Photo must be under 1MB.',
        ];
    }
}