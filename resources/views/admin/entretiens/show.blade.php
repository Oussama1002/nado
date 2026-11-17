@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-800">Détails de l'Entretien</h1>

    <div class="space-y-4 text-gray-700">
        <div>
            <strong>Annonce :</strong> 
            {{ $entretien->annonce->titre ?? 'Non défini' }} (ID: {{ $entretien->annonce_id }})
        </div>

        <div>
            <strong>Date :</strong> {{ \Carbon\Carbon::parse($entretien->date)->format('d/m/Y') ?? '—' }}
        </div>

        <div>
            <strong>Révision :</strong> {{ $entretien->revision ?? '—' }}
        </div>

        <div>
            <strong>Entretien :</strong> {{ $entretien->entretien ?? '—' }}
        </div>

        <div>
            <strong>Réparation :</strong> {{ $entretien->reparation ?? '—' }}
        </div>

        <div>
            <strong>Carrosserie :</strong> {{ $entretien->carrosserie ?? '—' }}
        </div>

        <div>
            <strong>Professionnels :</strong> {{ $entretien->professionnels ?? '—' }}
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('admin.entretiens.index') }}" 
           class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded shadow">
            Retour à la liste
        </a>
        @if(admin_can('annonces','modify'))
        <a href="{{ route('admin.entretiens.edit', $entretien->id) }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded shadow ml-4">
            Modifier
        </a>
        @endif
    </div>
</div>
@endsection
