@extends('layouts.client')

@section('title', __('messages.discovery.saved_searches'))

@section('client_content')
<style>
    .ss-wrap { padding: 0 0 20px; }
    .ss-title { font-size:26px; font-weight:900; color:#0f172a; margin:0 0 20px; }
    .ss-list { display:flex; flex-direction:column; gap:14px; }
    .ss-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:16px 18px;
        box-shadow:0 1px 3px rgba(0,0,0,.05), 0 10px 30px rgba(0,0,0,.05); }
    .ss-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    .ss-label { font-size:17px; font-weight:900; color:#0f172a; margin:0; }
    .ss-meta { color:#64748b; font-size:13px; margin:4px 0 0; }
    .ss-tags { display:flex; flex-wrap:wrap; gap:6px; margin:10px 0 0; }
    .ss-tag { background:#f1f5f9; color:#334155; font-size:12px; font-weight:700; padding:4px 10px; border-radius:9999px; }
    .ss-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:14px; }
    .ss-btn { display:inline-flex; align-items:center; gap:6px; border:1px solid #e2e8f0; background:#f8fafc; color:#0f172a;
        border-radius:10px; padding:8px 12px; font-size:13px; font-weight:800; cursor:pointer; text-decoration:none; }
    .ss-btn.primary { background:#1459DD; color:#fff; border-color:#1459DD; }
    .ss-btn.danger { color:#e11d48; border-color:#fecdd3; background:#fff1f2; }
    .ss-badge { font-size:12px; font-weight:800; padding:4px 10px; border-radius:9999px; }
    .ss-badge.on { background:#dcfce7; color:#166534; }
    .ss-badge.off { background:#f1f5f9; color:#64748b; }
    .ss-empty { background:#fff; border:1px dashed #e2e8f0; border-radius:16px; padding:50px 16px; text-align:center; color:#64748b; font-weight:700; }
    .ss-rename { display:none; margin-top:10px; gap:8px; }
    .ss-rename.open { display:flex; }
    .ss-rename input { flex:1; border:1.5px solid #cbd5e1; border-radius:10px; padding:8px 12px; font-size:14px; }
</style>

<div class="ss-wrap">
    <div class="container">
        <h1 class="ss-title">{{ __('messages.discovery.saved_searches') }}</h1>

        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:700;">{{ session('success') }}</div>
        @endif

        @if($searches->isEmpty())
            <div class="ss-empty">
                {{ __('messages.discovery.saved_none') }}<br>
                <a href="{{ route('achat.index') }}" class="ss-btn primary" style="margin-top:14px;">{{ __('messages.catalog.search') }}</a>
            </div>
        @else
            <div class="ss-list">
                @foreach($searches as $s)
                    @php $params = (array) $s->params; @endphp
                    <div class="ss-card">
                        <div class="ss-head">
                            <div>
                                <h3 class="ss-label">{{ $s->displayLabel() }}</h3>
                                <p class="ss-meta">{{ $s->created_at?->format('d/m/Y') }}</p>
                            </div>
                            <span class="ss-badge {{ $s->notify_new ? 'on' : 'off' }}">
                                {{ $s->notify_new ? __('messages.discovery.alerts_on') : __('messages.discovery.alerts_off') }}
                            </span>
                        </div>

                        @if(!empty($params))
                        <div class="ss-tags">
                            @foreach($params as $k => $v)
                                <span class="ss-tag">{{ $k }}: {{ $v }}</span>
                            @endforeach
                        </div>
                        @endif

                        <div class="ss-actions">
                            <a class="ss-btn primary" href="{{ route('achat.index', $params) }}">{{ __('messages.discovery.view_results') }}</a>

                            <button type="button" class="ss-btn" onclick="this.closest('.ss-card').querySelector('.ss-rename').classList.toggle('open')">{{ __('messages.discovery.rename') }}</button>

                            <form action="{{ route('saved-searches.toggle', $s->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="ss-btn">{{ $s->notify_new ? __('messages.discovery.alerts_off') : __('messages.discovery.alerts_on') }}</button>
                            </form>

                            <form action="{{ route('saved-searches.destroy', $s->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.discovery.delete') }} ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ss-btn danger">{{ __('messages.discovery.delete') }}</button>
                            </form>
                        </div>

                        <form class="ss-rename" action="{{ route('saved-searches.rename', $s->id) }}" method="POST">
                            @csrf
                            <input type="text" name="label" maxlength="120" value="{{ $s->label }}" placeholder="{{ __('messages.discovery.new_label') }}">
                            <button type="submit" class="ss-btn primary">{{ __('messages.discovery.rename') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
