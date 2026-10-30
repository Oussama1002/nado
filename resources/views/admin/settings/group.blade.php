@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</h3>
    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Tous les groupes
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update', $group) }}">
                @csrf

                @forelse($settings as $setting)
                    @php $val = $setting->typed_value; @endphp
                    <div class="row align-items-center py-3 border-bottom">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold mb-0" for="set-{{ $setting->id }}">
                                {{ $setting->label }}
                            </label>
                            @if($setting->description)
                                <div class="text-muted small">{{ $setting->description }}</div>
                            @endif
                            <code class="small text-muted">{{ $setting->key }}</code>
                        </div>
                        <div class="col-12 col-md-6">
                            @switch($setting->type)
                                @case('bool')
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox"
                                               id="set-{{ $setting->id }}"
                                               name="settings[{{ $setting->key }}]"
                                               value="1" {{ $val ? 'checked' : '' }}>
                                    </div>
                                    @break

                                @case('percent')
                                    <div class="input-group" style="max-width:220px">
                                        <input type="number" step="0.01" min="0" max="100"
                                               class="form-control" id="set-{{ $setting->id }}"
                                               name="settings[{{ $setting->key }}]" value="{{ $val }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @break

                                @case('int')
                                    <input type="number" step="1"
                                           class="form-control" style="max-width:220px"
                                           id="set-{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]" value="{{ $val }}">
                                    @break

                                @case('decimal')
                                    <input type="number" step="0.01"
                                           class="form-control" style="max-width:220px"
                                           id="set-{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]" value="{{ $val }}">
                                    @break

                                @case('json')
                                    <textarea class="form-control font-monospace" rows="3"
                                              id="set-{{ $setting->id }}"
                                              name="settings[{{ $setting->key }}]">{{ is_array($val) ? json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $val }}</textarea>
                                    @break

                                @default
                                    <input type="{{ $setting->is_secret ? 'password' : 'text' }}"
                                           class="form-control"
                                           id="set-{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->is_secret ? '' : $val }}"
                                           placeholder="{{ $setting->is_secret ? '•••••• (laisser vide pour ne pas changer)' : '' }}">
                            @endswitch
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucun réglage dans ce groupe.</p>
                @endforelse

                @if($settings->isNotEmpty())
                    <div class="pt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
