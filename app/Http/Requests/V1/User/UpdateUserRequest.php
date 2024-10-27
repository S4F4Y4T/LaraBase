<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore(auth()->user()->id)
            ],
            'mobile' => ['nullable', Rule::unique('users', 'mobile')->ignore(auth()->user()->id)],
            'password' => 'nullable|min:8',
        ];
    }
}
