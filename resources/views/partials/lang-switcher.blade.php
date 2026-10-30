@php $current = app()->getLocale(); @endphp
<div class="lang-switch" style="display:inline-flex;align-items:center;gap:4px;font-weight:800;font-size:12px;line-height:1;white-space:nowrap;">
    <a href="{{ route('locale.switch', 'fr') }}" aria-label="Français"
       style="text-decoration:none;padding:5px 10px;border-radius:8px;transition:.15s;{{ $current === 'fr' ? 'background:#1459DD;color:#fff;' : 'color:#334155;background:#f1f5f9;' }}">Français</a>
    <a href="{{ route('locale.switch', 'ar') }}" aria-label="العربية"
       style="text-decoration:none;padding:5px 10px;border-radius:8px;transition:.15s;{{ $current === 'ar' ? 'background:#1459DD;color:#fff;' : 'color:#334155;background:#f1f5f9;' }}">العربية</a>
</div>
