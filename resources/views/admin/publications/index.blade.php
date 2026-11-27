@extends('admin.layouts.app')

@php
    // Mappe chaque placement à son libellé humain + la page où il apparaît.
    $placementMap = [
        'center-banner'              => ['label' => 'Bannière Centre (1000×500)',               'page' => 'Page d\'accueil',                       'url' => url('/')],
        'first-banner'               => ['label' => '1er bannière sur page achat',              'page' => 'Page achat',                            'url' => url('/achat')],
        'second-banner'              => ['label' => '2ème bannière sur page achat',             'page' => 'Page achat',                            'url' => url('/achat')],
        'third-banner'               => ['label' => '3ème bannière sur page achat',             'page' => 'Page achat',                            'url' => url('/achat')],
        'rightonindexenchere-banner' => ['label' => 'Bannière Droite sur page Enchères',        'page' => 'Auto-Enchère',                          'url' => url('/nadoautoenchere')],
        'vente-hero'                 => ['label' => 'Image hero — Vente',                       'page' => 'Vente — Déposer une annonce',           'url' => url('/vente/formulaire')],
        'ae-expertise'               => ['label' => 'Image « Notre expertise » — Auto-Enchère', 'page' => 'Auto-Enchère — Pré-login',              'url' => url('/nadoautoenchere/prelogin')],
        'reprise-hero'               => ['label' => 'Image hero — Reprise',                     'page' => 'Reprise — Formulaire',                  'url' => url('/reprises/formulaire')],
        'front-estimation'           => ['label' => 'Image section « Estimation »',             'page' => 'Page d\'accueil',                       'url' => url('/')],
        'front-mid-banner'           => ['label' => 'Bannière entre logos & Derniers véhicules', 'page' => 'Page d\'accueil',                       'url' => url('/')],
        // Anciens placements (lignes existantes en base qu'on n'expose plus dans le dropdown).
        'left-banner'                => ['label' => 'Bannière Gauche (obsolète)',               'page' => '—',                                     'url' => null],
        'right-banner'               => ['label' => 'Bannière Droite (obsolète)',               'page' => '—',                                     'url' => null],
        'leftondetail-banner'        => ['label' => 'Bannière Gauche détail (obsolète)',        'page' => '—',                                     'url' => null],
    ];
@endphp

@section('content')
<div class="container">
    <h1>Publications</h1>
    @if(admin_can('publicite','add'))<a href="{{ route('admin.publications.create') }}" class="btn btn-primary mb-3">Nouvelle publication</a>@endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul id="publication-list" class="list-group">
        @foreach($publications as $pub)
            @php
                $meta = $placementMap[$pub->placement] ?? ['label' => $pub->placement, 'page' => '—', 'url' => null];
            @endphp
            <li class="list-group-item d-flex justify-content-between align-items-center gap-3" data-id="{{ $pub->id }}">
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate">{{ $pub->title }}</div>
                    <div class="small text-muted d-flex flex-wrap gap-2 align-items-center mt-1">
                        <span class="badge bg-info text-dark">
                            <i class="bi bi-geo-alt"></i> {{ $meta['label'] }}
                        </span>
                        <span>
                            <i class="bi bi-link-45deg"></i>
                            @if($meta['url'])
                                <a href="{{ $meta['url'] }}" target="_blank" rel="noopener">{{ $meta['page'] }}</a>
                            @else
                                {{ $meta['page'] }}
                            @endif
                        </span>
                        @if(! $pub->status)
                            <span class="badge bg-secondary">Désactivée</span>
                        @endif
                    </div>
                </div>
                <span class="flex-shrink-0">
                    @if(admin_can('publicite','modify'))<a href="{{ route('admin.publications.edit', $pub) }}" class="btn btn-sm btn-warning">Modifier</a>@endif
                    @if(admin_can('publicite','delete'))
                    <form action="{{ route('admin.publications.destroy', $pub) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                    @endif
                </span>
            </li>
        @endforeach
    </ul>
</div>

<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script>
    $(function() {
        $('#publication-list').sortable({
            update: function(event, ui) {
                let positions = $(this).sortable('toArray', {attribute: 'data-id'});
                $.post('{{ route('admin.publications.reorder') }}', {
                    positions: positions,
                    _token: '{{ csrf_token() }}'
                });
            }
        });
    });
</script>
@endsection
