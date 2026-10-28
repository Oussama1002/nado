@props([
    'filters' => [],
    'title'   => null,
    'message' => null,
])
@php
    $title   = $title   ?? __('messages.catalog.empty_title');
    $message = $message ?? __('messages.catalog.empty_message');
@endphp

<div class="pp-empty demande-empty" style="text-align:center;padding:40px 20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;margin:16px 0;">
    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"
         style="margin:0 auto 16px;display:block;">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>

    <h3 style="margin:0 0 8px;font-size:18px;color:#0f172a;">
        {!! $title !!}
    </h3>
    <p style="margin:0 0 16px;color:#475569;">
        {!! $message !!}
    </p>

    <a href="{{ route('demandes.create', array_filter($filters)) }}"
       class="btn btn-primary"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#0d6efd;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        {{ __('messages.catalog.publish_request') }}
    </a>

    <p style="margin-top:12px;font-size:13px;color:#64748b;">
        {{ __('messages.catalog.already_q') }} <a href="{{ route('demandes.index') }}">{{ __('messages.catalog.forum_link') }}</a>.
    </p>
</div>
