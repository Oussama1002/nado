@extends('layouts.client')

@section('title', 'Modifier mon profil')

@section('client_title', 'Mon Profil')
@section('client_subtitle', 'Gérez vos informations personnelles.')
@section('client_content')
<style>
    .profile-container {
        max-width: 700px;
        margin: 0 auto;
        background-color: #fff7f3;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-container h2 {
        color: #1f519b;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .form-actions {
        text-align: right;
    }

    .form-actions button {
        background-color: #1f519b;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-actions button:hover {
        background-color: #163a6d;
    }
</style>

<div class="profile-container">
    <h2>Modifier mon profil</h2>

    {{-- ✅ Show success message --}}
    @if (session('status') === 'profile-updated')
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            ✅ Les modifications ont été enregistrées avec succès.
        </div>
    @endif

    {{-- ⚠️ Erreurs de validation (sinon l'échec passe inaperçu) --}}
    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-right: 18px; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

   <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nom</label>
        <input type="text" id="name" name="name" 
               value="{{ old('name', Auth::guard('client')->user()->name) }}" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" 
               value="{{ old('email', Auth::guard('client')->user()->email) }}" required>
    </div>

    <div class="form-group">
        <label for="phone">Téléphone</label>
        <input type="text" id="phone" name="phone" 
               value="{{ old('phone', Auth::guard('client')->user()->phone) }}" required>
    </div>

    {{-- Champs spécifiques selon type --}}
    @php $type = Auth::guard('client')->user()->type; @endphp

    @if(in_array($type, ['flotte&enchere', 'pro']))
        <div class="form-group">
            <label for="company">Société</label>
            <input type="text" id="company" name="company" 
                   value="{{ old('company', Auth::guard('client')->user()->company) }}">
        </div>

        <div class="form-group">
            <label for="logo">Logo de l’entreprise</label>
            <input type="file" id="logo" name="logo" accept="image/*">
            @if(Auth::guard('client')->user()->logo)
                <img src="{{ asset('storage/nadoautoenchere/logo/' . Auth::guard('client')->user()->logo) }}" 
                     alt="Logo entreprise" class="mt-2 w-24 h-24 object-cover rounded">
            @endif
        </div>
    @elseif($type === 'particulier')
        <div class="form-group">
            <label for="logo">Photo de profil</label>
            <input type="file" id="logo" name="logo" accept="image/*">
            @if(Auth::guard('client')->user()->logo)
                <img src="{{ asset('storage/nadoautoenchere/logo/' . Auth::guard('client')->user()->logo) }}" 
                     alt="Photo de profil" class="mt-2 w-24 h-24 object-cover rounded-full">
            @endif
        </div>
    @endif

    <div class="form-group">
        <label for="password">Mot de passe <small>(laisser vide pour ne pas changer)</small></label>
        <input type="password" id="password" name="password">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation">
    </div>

    <div class="form-actions">
        <button type="submit">Enregistrer</button>
    </div>
</form>

</div>

@endsection
