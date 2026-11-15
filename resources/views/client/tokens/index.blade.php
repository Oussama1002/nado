@extends('layouts.client')

@section('title', __('messages.tokens.my_tokens'))

@section('client_title', '')
@section('client_subtitle', '')
@section('client_content')
@php($rtl = app()->getLocale() === 'ar')
<style>
    .tk-wrap { padding: 0 0 10px; }
    .tk-head { margin-bottom: 20px; }
    .tk-title { font-size: 26px; font-weight: 900; color:#0f172a; letter-spacing:-.02em; margin:0; }
    .tk-sub { margin:6px 0 0; color:#64748b; font-size:15px; font-weight:600; }

    .tk-balance {
        background: linear-gradient(135deg,#1459DD,#0b3fa6);
        color:#fff; border-radius:18px; padding:22px 24px; margin-bottom:22px;
        display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
        box-shadow:0 10px 30px rgba(20,89,221,.25);
    }
    .tk-balance .lbl { font-size:14px; font-weight:700; opacity:.85; margin:0 0 6px; }
    .tk-balance .amt { font-size:40px; font-weight:900; line-height:1; margin:0; }
    .tk-balance .amt small { font-size:16px; font-weight:700; opacity:.85; }

    .tk-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; margin-bottom:22px; }
    .tk-card h3 { margin:0 0 14px; font-size:17px; font-weight:800; color:#0f172a; }

    .tk-presets { display:flex; flex-wrap:wrap; gap:10px; }
    .tk-presets button {
        border:1.5px solid #cbd5e1; background:#f8fafc; color:#0f172a; border-radius:12px;
        padding:12px 18px; font-weight:800; font-size:15px; cursor:pointer; transition:.15s;
    }
    .tk-presets button:hover { border-color:#1459DD; background:#eef4ff; color:#1459DD; }

    .tk-note { margin-top:12px; font-size:13px; color:#64748b; }

    .tk-table { width:100%; border-collapse:collapse; font-size:14px; }
    .tk-table th, .tk-table td { padding:10px 12px; text-align:{{ $rtl ? 'right' : 'left' }}; border-bottom:1px solid #eef2f7; }
    .tk-table th { color:#64748b; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:.03em; }
    .tk-amt-pos { color:#16a34a; font-weight:800; }
    .tk-amt-neg { color:#dc2626; font-weight:800; }
    .tk-empty { color:#94a3b8; padding:24px 0; text-align:center; font-weight:600; }

    .tk-flash { border-radius:12px; padding:12px 16px; margin-bottom:18px; font-weight:700; font-size:14px; }
    .tk-flash.info { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
</style>

<div class="tk-wrap">
    <div class="tk-head">
        <h1 class="tk-title">{{ __('messages.tokens.my_tokens') }}</h1>
        <p class="tk-sub">{{ __('messages.tokens.subtitle') }}</p>
    </div>

    @if(session('info'))
        <div class="tk-flash info">{{ session('info') }}</div>
    @endif

    <div class="tk-balance">
        <div>
            <p class="lbl">{{ __('messages.tokens.current_balance') }}</p>
            @if($client->hasUnlimitedTokens())
                <p class="amt">∞ <small>{{ __('messages.tokens.unlimited') }}</small></p>
            @else
                <p class="amt">{{ number_format($balance, 0, ',', ' ') }} <small>{{ __('messages.tokens.unit') }}</small></p>
            @endif
        </div>
        <div style="font-size:48px;opacity:.9;">🪙</div>
    </div>

    @if($enabled && !$client->hasUnlimitedTokens())
    <div class="tk-card">
        <h3>{{ __('messages.tokens.topup_title') }}</h3>
        <form method="POST" action="{{ route('client.tokens.checkout') }}" id="tkForm">
            @csrf
            <input type="hidden" name="tokens" id="tkTokens" value="">
            <div class="tk-presets">
                @foreach($presets as $p)
                    <button type="button" onclick="document.getElementById('tkTokens').value={{ $p }};document.getElementById('tkForm').submit();">
                        {{ number_format($p, 0, ',', ' ') }} {{ __('messages.tokens.unit') }}
                        <span style="opacity:.6;font-weight:600;">· {{ number_format($p, 0, ',', ' ') }} {{ $currency }}</span>
                    </button>
                @endforeach
            </div>
            <p class="tk-note">{{ __('messages.tokens.topup_note') }}</p>
        </form>
    </div>
    @endif

    <div class="tk-card">
        <h3>{{ __('messages.tokens.history') }}</h3>
        @if($transactions->count())
            <table class="tk-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.tokens.col_date') }}</th>
                        <th>{{ __('messages.tokens.col_detail') }}</th>
                        <th>{{ __('messages.tokens.col_amount') }}</th>
                        <th>{{ __('messages.tokens.col_balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td>{{ $t->created_at?->format('d/m/Y H:i') }}</td>
                            @php($rk = 'messages.tokens.reason.'.($t->reason ?? $t->type))
                            <td>{{ \Illuminate\Support\Facades\Lang::has($rk) ? __($rk) : ($t->reason ?? $t->type) }}</td>
                            <td class="{{ $t->amount < 0 ? 'tk-amt-neg' : 'tk-amt-pos' }}">
                                {{ $t->amount > 0 ? '+' : '' }}{{ number_format($t->amount, 0, ',', ' ') }}
                            </td>
                            <td>{{ number_format($t->balance_after, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:16px;">{{ $transactions->links() }}</div>
        @else
            <p class="tk-empty">{{ __('messages.tokens.no_history') }}</p>
        @endif
    </div>
</div>
@endsection
