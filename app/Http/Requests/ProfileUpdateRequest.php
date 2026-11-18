<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        // Cibler le client connecté (guard "client") : le guard par défaut
        // ("web") pointe sur les admins, donc $this->user()?->id pouvait valoir
        // null ou l'id d'un admin → la règle unique n'ignorait pas la bonne
        // ligne et refusait l'e-mail actuel du client (échec silencieux).
        $clientId = Auth::guard('client')->id();

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
