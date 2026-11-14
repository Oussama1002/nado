<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $clientId = $this->user()?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($clientId)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'company'  => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', 'min:6'],
            'logo'     => ['nullable', 'image', 'max:4096'],
        ];
    }
}
