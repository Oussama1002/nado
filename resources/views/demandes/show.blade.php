@extends('layouts.app')

@section('title', __('messages.demande_show.title_prefix') . ' #' . $demande->id . ' — NADO')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .dmd-show { max-width:960px; margin:40px auto 60px; padding:0 20px; font-family:'Inter',sans-serif; color:#0f172a; }
    .dmd-back { display:inline-flex; align-items:center; gap:6px; font-size:14px; color:#64748b; text-decoration:none; margin-bottom:16px; }
    .dmd-back:hover { color:#1459DD; }

    .dmd-main {
        background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:28px;
        box-shadow:0 4px 16px -4px rgba(15,23,42,.06);
    }
    .dmd-top { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; }
    .dmd-top h1 { font-size:26px; font-weight:800; margin:0 0 6px; letter-spacing:-0.02em; }
    .dmd-top .dmd-sub { color:#64748b; font-size:14px; }
    .dmd-badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; padding:5px 12px; border-radius:999px; background:#dbeafe; color:#1459DD; text-transform:uppercase; letter-spacing:.03em; }

    .dmd-specs { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin:22px 0 10px; }
    .dmd-spec { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
    .dmd-spec dt { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.05em; font-weight:600; margin-bottom:4px; }
    .dmd-spec dd { margin:0; font-size:15px; font-weight:700; color:#0f172a; }

    .dmd-note { margin-top:16px; padding:16px; background:#fef9e7; border:1px solid #fde68a; border-radius:12px; }
    .dmd-note strong { color:#92400e; }

    .dmd-contact-box {
        margin-top:24px; padding:20px; background:linear-gradient(135deg,#f8fafc,#eff6ff);
        border:1px solid #c7d2fe; border-radius:16px;
        display:flex; align-items:center; gap:16px; flex-wrap:wrap; justify-content:space-between;
    }
    .dmd-user { display:flex; align-items:center; gap:14px; min-width:0; }
    .dmd-avatar {
        width:52px; height:52px; border-radius:50%;
        background:linear-gradient(135deg,#1459DD,#F37021);
        color:#fff; display:flex; align-items:center; justify-content:center;
        font-weight:700; font-size:20px; flex-shrink:0;
    }
    .dmd-user-info { min-width:0; }
    .dmd-user-info .lbl { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
    .dmd-user-info .nm { font-size:17px; font-weight:700; color:#0f172a; }
    .dmd-user-info .ph { font-size:14px; color:#1459DD; font-weight:600; font-variant-numeric:tabular-nums; margin-top:2px; }

    .dmd-reveal-btn {
        padding:10px 18px; background:#1459DD; color:#fff; border:0; border-radius:10px;
        font-weight:700; cursor:pointer; font-family:inherit; font-size:13px;
        display:inline-flex; align-items:center; gap:6px; transition:transform .15s, box-shadow .15s;
    }
    .dmd-reveal-btn:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(20,89,221,.3); }
    .dmd-login-link { font-size:13px; color:#1459DD; text-decoration:none; font-weight:600; }
    .dmd-login-link:hover { text-decoration:underline; }

    .dmd-section { margin-top:30px; }
    .dmd-section h2 { font-size:18px; font-weight:700; margin:0 0 14px; }

    .dmd-matches { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; }
    .dmd-match {
        display:block; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px;
        text-decoration:none; color:#0f172a; transition:transform .15s, border-color .15s;
    }
    .dmd-match:hover { transform:translateY(-2px); border-color:#1459DD; }
    .dmd-match strong { display:block; font-size:14px; margin-bottom:2px; }
    .dmd-match small { color:#64748b; font-size:12px; }

    .dmd-reply-form {
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:14px;
    }
    .dmd-reply-form textarea, .dmd-reply-form input {
        width:100%; padding:11px 14px; border:1px solid #e2e8f0; border-radius:10px;
        font-family:inherit; font-size:14px; outline:none; background:#fff;
    }
    .dmd-reply-form textarea:focus, .dmd-reply-form input:focus { border-color:#1459DD; box-shadow:0 0 0 3px rgba(20,89,221,.1); }
    .dmd-reply-row { display:flex; gap:10px; margin-top:10px; flex-wrap:wrap; }
    .dmd-reply-row input { flex:1; min-width:200px; }
    .dmd-reply-row button {
        padding:11px 20px; background:#1459DD; color:#fff; border:0; border-radius:10px;
        font-weight:700; cursor:pointer; font-family:inherit; font-size:14px;
    }
    .dmd-reply-row button:hover { background:#1047b8; }

    .dmd-reply {
        background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px; margin-bottom:10px;
    }
    .dmd-reply .meta { font-size:12px; color:#94a3b8; margin-bottom:6px; }
    .dmd-reply p { margin:0; font-size:14px; line-height:1.5; color:#334155; white-space:pre-line; }

    /* Carte proposition de véhicule */
    .prop-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.05),0 8px 24px rgba(0,0,0,.05); margin-bottom:16px; }
    .prop-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 16px; border-bottom:1px solid #f1f5f9; }
    .prop-badge { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#1459DD; background:#eff4ff; padding:4px 10px; border-radius:999px; }
    .prop-time { font-size:12px; color:#94a3b8; }
    .prop-gallery { display:flex; gap:8px; padding:12px 16px 0; overflow-x:auto; }
    .prop-gallery img { width:160px; height:110px; object-fit:cover; border-radius:12px; flex-shrink:0; cursor:pointer; border:1px solid #e2e8f0; }
    .prop-body { padding:14px 16px 16px; }
    .prop-title { margin:0; font-size:17px; font-weight:800; color:#0f172a; }
    .prop-price { font-size:18px; font-weight:900; color:#1459DD; margin:2px 0 10px; }
    .prop-specs { display:flex; flex-wrap:wrap; gap:8px 14px; font-size:13px; color:#475569; font-weight:600; margin-bottom:10px; }
    .prop-desc { margin:0 0 12px; font-size:13.5px; color:#334155; line-height:1.55; white-space:pre-line; }
    .prop-contact { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; border-top:1px solid #f1f5f9; padding-top:12px; }
    .prop-contact-name { font-size:13px; font-weight:800; color:#0f172a; }
    .prop-phone { font-size:14px; font-weight:800; color:#334155; letter-spacing:.04em; }
    .prop-reveal { background:#1459DD; color:#fff; border:0; border-radius:12px; padding:9px 16px; font-size:13px; font-weight:800; cursor:pointer; white-space:nowrap; }
    .prop-reveal:hover { background:#1047b8; }

    .dmd-report { margin-top:20px; }
    .dmd-report button {
        background:none; border:0; color:#94a3b8; cursor:pointer; font-size:12px;
        font-family:inherit; text-decoration:underline;
    }
    .dmd-report button:hover { color:#dc2626; }

    @media (max-width:640px) {
        .dmd-top h1 { font-size:22px; }
        .dmd-main { padding:20px; }
    }
</style>

@php
    $initials = mb_strtoupper(mb_substr(trim($demande->nom), 0, 1));
    $digits = preg_replace('/\D+/', '', (string) $demande->telephone);
    $maskedPhone = strlen($digits) >= 6
        ? substr($digits, 0, 2) . ' •• •• ' . substr($digits, -2)
        : '•• •• •• ••';
@endphp

<div class="dmd-show">
    <a href="{{ route('demandes.index') }}" class="dmd-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Retour au forum
    </a>

    <div class="dmd-main">
        <div class="dmd-top">
            <div>
                <h1>{{ __('messages.demande_show.seek') }} {{ $demande->brand?->name ?? __('messages.demande_show.a_car') }} {{ $demande->modele?->name }}
                    @if($demande->ville) <span style="color:#64748b;font-weight:500;">{{ __('messages.demande_show.at') }} {{ $demande->ville->name }}</span>@endif
                </h1>
                <div class="dmd-sub">{{ __('messages.demande_show.published') }} {{ $demande->created_at->locale(app()->getLocale())->diffForHumans() }}</div>
            </div>
            <span class="dmd-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>
                {{ __('messages.demande_show.active') }}
            </span>
        </div>

        <dl class="dmd-specs">
            @if($demande->price_min || $demande->price_max)
                <div class="dmd-spec"><dt>{{ __('messages.demande_show.budget') }}</dt>
                    <dd><bdi dir="ltr">{{ number_format($demande->price_min ?? 0, 0, ',', ' ') }} – {{ number_format($demande->price_max ?? 0, 0, ',', ' ') }} DH</bdi></dd></div>
            @endif
            @if($demande->year_min || $demande->year_max)
                <div class="dmd-spec"><dt>{{ __('messages.demande_show.year') }}</dt>
                    <dd><bdi dir="ltr">{{ $demande->year_min ?? '…' }} – {{ $demande->year_max ?? '…' }}</bdi></dd></div>
            @endif
            @if($demande->km_max)
                <div class="dmd-spec"><dt>{{ __('messages.demande_show.km_max') }}</dt>
                    <dd><bdi dir="ltr">{{ number_format($demande->km_max, 0, ',', ' ') }}</bdi></dd></div>
            @endif
            @if($demande->boite && $demande->boite !== 'any')
                <div class="dmd-spec"><dt>{{ __('messages.demande_show.gearbox') }}</dt>
                    <dd>{{ $demande->boite === 'auto' ? __('messages.ventemodal.box_auto') : ($demande->boite === 'manuelle' ? __('messages.ventemodal.box_manual') : $demande->boite) }}</dd></div>
            @endif
            @if($demande->carburant)
                <div class="dmd-spec"><dt>{{ __('messages.ventemodal.fuel') }}</dt><dd>{{ $demande->carburant }}</dd></div>
            @endif
        </dl>

        @if($demande->comment)
            <div class="dmd-note">
                <strong>{{ __('messages.demande_show.note_label') }}</strong><br>
                {{ $demande->comment }}
            </div>
        @endif

        <div class="dmd-contact-box">
            <div class="dmd-user">
                <div class="dmd-avatar">{{ $initials ?: '?' }}</div>
                <div class="dmd-user-info">
                    <div class="lbl">{{ __('messages.demande_show.requester') }}</div>
                    <div class="nm">{{ $demande->nom }}</div>
                    <div class="ph" id="dmdPhone" data-full="{{ $demande->telephone }}">📞 {{ $maskedPhone }}</div>
                </div>
            </div>
            <button type="button" class="dmd-reveal-btn" onclick="document.getElementById('proposeModal').style.display='flex';">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                {{ __('messages.demande_show.propose') }}
            </button>
        </div>
    </div>

    @if($matches->count())
        <div class="dmd-section">
            <h2>{{ __('messages.demande_show.matches_title') }}</h2>
            <div class="dmd-matches">
                @foreach($matches as $a)
                    <a href="{{ route('achat.detail', ['marque' => $a->mark, 'modele' => $a->modele, 'id' => $a->id]) }}" class="dmd-match">
                        <strong>{{ $a->brand?->name ?? $a->mark }} {{ $a->modeleRelation?->name ?? $a->modele }}</strong>
                        <small>{{ $a->year }} · {{ number_format($a->prix, 0, ',', ' ') }} DH</small>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="dmd-section">
        <h2>{{ __('messages.demande_show.replies') }} ({{ $demande->replies_count }})</h2>
        <div class="dmd-reply-form" style="text-align:center;">
            <p style="margin:0 0 12px;color:#334155;font-size:14px;">
                {{ __('messages.demande_show.reply_cta') }}
            </p>
            <button type="button" class="dmd-reveal-btn" style="text-decoration:none;" onclick="document.getElementById('proposeModal').style.display='flex';">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                {{ __('messages.demande_show.propose') }}
            </button>
        </div>

        @forelse($demande->replies->where('status','visible') as $r)
            @php $meta = is_array($r->meta) ? $r->meta : null; @endphp
            @if($meta)
                @php
                    $pImgs   = collect($meta['images'] ?? [])->map(fn($p) => asset('storage/'.$p));
                    $pDigits = preg_replace('/\D+/', '', (string) $r->contact_phone);
                    $pMasked = strlen($pDigits) >= 6 ? substr($pDigits,0,2).' •• •• '.substr($pDigits,-2) : '•• •• ••';
                    $pTitle  = trim(($meta['mark'] ?? '').' '.($meta['modele'] ?? ''));
                @endphp
                <div class="prop-card">
                    <div class="prop-head">
                        <span class="prop-badge">{{ __('messages.demande_show.vehicle_proposal') }}</span>
                        <span class="prop-time">{{ $r->created_at->locale(app()->getLocale())->diffForHumans() }}</span>
                    </div>
                    @if($pImgs->count())
                        <div class="prop-gallery">
                            @foreach($pImgs as $u)
                                <img src="{{ $u }}" alt="{{ $pTitle }}" loading="lazy" onclick="window.open(this.src,'_blank')">
                            @endforeach
                        </div>
                    @endif
                    <div class="prop-body">
                        <h3 class="prop-title">{{ $pTitle ?: __('messages.demande_show.vehicle') }}</h3>
                        <div class="prop-price">{{ number_format((int)($meta['prix'] ?? 0), 0, ',', ' ') }} DH</div>
                        <div class="prop-specs">
                            @if(!empty($meta['year']))<span>📅 {{ $meta['year'] }}</span>@endif
                            @if(isset($meta['km']))<span>🛣️ {{ number_format((int)$meta['km'], 0, ',', ' ') }} km</span>@endif
                            @if(!empty($meta['carburant']))<span>⛽ {{ $meta['carburant'] }}</span>@endif
                            @if(!empty($meta['box']))<span>⚙️ {{ $meta['box'] }}</span>@endif
                            @if(!empty($meta['city']))<span>📍 {{ $meta['city'] }}</span>@endif
                        </div>
                        @if(!empty($meta['description']))
                            <p class="prop-desc">{{ $meta['description'] }}</p>
                        @endif
                        <div class="prop-contact">
                            <div>
                                <div class="prop-contact-name">{{ $meta['proposer_name'] ?? ($r->client?->name ?? __('messages.demande_show.member')) }}</div>
                                <div class="prop-phone" id="propPhone{{ $r->id }}">📞 {{ $pMasked }}</div>
                            </div>
                            <button type="button" class="prop-reveal" data-full="{{ $r->contact_phone }}" data-target="propPhone{{ $r->id }}" onclick="revealPropPhone(this)">
                                {{ __('messages.demande_show.show_number') }}
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="dmd-reply">
                    <div class="meta">{{ $r->created_at->locale(app()->getLocale())->diffForHumans() }} · {{ $r->client?->name ?? __('messages.demande_show.member') }}</div>
                    <p>{{ $r->message }}</p>
                    @if($r->annonce)
                        <a href="{{ route('achat.detail', ['marque' => $r->annonce->mark, 'modele' => $r->annonce->modele, 'id' => $r->annonce->id]) }}" style="font-size:13px;color:#1459DD;">{{ __('messages.demande_show.see_ad') }}</a>
                    @endif
                </div>
            @endif
        @empty
            <p style="color:#64748b;">{{ __('messages.demande_show.no_replies') }}</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('demandes.report', $demande->id) }}" class="dmd-report">
        @csrf
        <button type="submit">{{ __('messages.demande_show.report') }}</button>
    </form>
</div>

{{-- ============== PROPOSE VEHICLE MODAL ============== --}}
<div id="proposeModal" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);align-items:flex-start;justify-content:center;padding:40px 16px;overflow-y:auto;font-family:'Inter',sans-serif;">
    <div style="background:#fff;max-width:620px;width:100%;border-radius:16px;box-shadow:0 30px 60px -20px rgba(15,23,42,.4);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #e2e8f0;">
            <div>
                <h2 style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">{{ __('messages.demande_show.propose') }}</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#64748b;">{{ __('messages.demande_show.modal_sub') }}</p>
            </div>
            <button type="button" onclick="document.getElementById('proposeModal').style.display='none';"
                    style="background:none;border:0;color:#94a3b8;font-size:24px;cursor:pointer;line-height:1;">×</button>
        </div>

        @if($errors->any() && old('_propose'))
            <div style="margin:16px 22px 0;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:10px;font-size:13px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
            <script>document.getElementById('proposeModal').style.display='flex';</script>
        @endif

        <form method="POST" action="{{ route('demandes.propose', $demande->id) }}"
              enctype="multipart/form-data"
              style="padding:20px 22px 22px;display:grid;gap:14px;">
            @csrf
            <input type="hidden" name="_propose" value="1">
            <input type="text" name="website" value="" style="display:none;" tabindex="-1" autocomplete="off">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.demande_show.fullname') }} *
                    <input type="text" name="proposer_name" required
                           value="{{ old('proposer_name', auth('client')->user()?->name) }}"
                           style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.ventemodal.phone') }} *
                    <input type="tel" name="proposer_phone" required
                           value="{{ old('proposer_phone', auth('client')->user()?->phone) }}"
                           placeholder="06XXXXXXXX"
                           style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">
                </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.ventemodal.marque') }} *
                    <select name="mark" id="proposeBrand" required
                            style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;background:#fff;">
                        <option value="">—</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" @selected(old('mark', $demande->brand_id) == $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.ventemodal.modele') }} *
                    <select name="modele" id="proposeModele" required
                            style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;background:#fff;">
                        <option value="">—</option>
                        @foreach(($prefillModeles ?? collect()) as $m)
                            <option value="{{ $m->id }}" @selected(old('modele', $demande->modele_id) == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <script>
                document.getElementById('proposeBrand').addEventListener('change', function () {
                    var brandId = this.value;
                    var sel = document.getElementById('proposeModele');
                    sel.innerHTML = '<option value="">—</option>';
                    if (!brandId) return;
                    fetch('{{ route('achat.models.by.brand', ['brandId' => '__ID__']) }}'.replace('__ID__', brandId))
                        .then(r => r.json())
                        .then(models => {
                            models.forEach(function (m) {
                                var opt = document.createElement('option');
                                opt.value = m.id;
                                opt.textContent = m.name;
                                sel.appendChild(opt);
                            });
                        });
                });
            </script>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.demande_show.year') }} *
                    <input type="number" name="year" required min="1980" max="{{ date('Y')+1 }}" value="{{ old('year') }}"
                           style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.demande_show.km') }} *
                    <input type="number" name="km" required min="0" value="{{ old('km') }}"
                           style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.demande_show.price') }} *
                    <input type="number" name="prix" required min="0" value="{{ old('prix') }}"
                           style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">
                </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.ventemodal.city') }}
                    <select name="city" style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;background:#fff;">
                        <option value="">—</option>
                        @foreach($villes as $v)
                            <option value="{{ $v->name }}" @selected(old('city', $demande->ville?->name) === $v->name)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.ventemodal.fuel') }}
                    <select name="carburant" style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;background:#fff;">
                        <option value="">—</option>
                        <option value="Essence"  @selected(old('carburant')==='Essence')>{{ __('messages.ventemodal.fuel_essence') }}</option>
                        <option value="Diesel"   @selected(old('carburant')==='Diesel')>{{ __('messages.ventemodal.fuel_diesel') }}</option>
                        <option value="Hybride"  @selected(old('carburant')==='Hybride')>{{ __('messages.ventemodal.fuel_hybrid') }}</option>
                        <option value="Électrique" @selected(old('carburant')==='Électrique')>{{ __('messages.ventemodal.fuel_electric') }}</option>
                    </select>
                </label>
                <label style="font-size:13px;font-weight:600;color:#334155;">
                    {{ __('messages.demande_show.gearbox') }}
                    <select name="box" style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;background:#fff;">
                        <option value="">—</option>
                        <option value="Automatique" @selected(old('box')==='Automatique')>{{ __('messages.ventemodal.box_auto') }}</option>
                        <option value="Manuelle"    @selected(old('box')==='Manuelle')>{{ __('messages.ventemodal.box_manual') }}</option>
                    </select>
                </label>
            </div>

            <label style="font-size:13px;font-weight:600;color:#334155;">
                {{ __('messages.demande_show.description') }}
                <textarea name="description" rows="3"
                          style="width:100%;margin-top:4px;padding:10px 12px;border:1px solid #e2e8f0;border-radius:9px;font-family:inherit;font-size:14px;">{{ old('description') }}</textarea>
            </label>

            <div>
                <label style="font-size:13px;font-weight:600;color:#334155;display:block;margin-bottom:6px;">
                    {{ __('messages.demande_show.photos_label') }} <span style="color:#94a3b8;font-weight:500;">{{ __('messages.demande_show.photos_hint') }}</span>
                </label>

                <div id="proposeDropzone" style="position:relative;border:2px dashed #cbd5e1;border-radius:12px;background:#f8fafc;padding:26px 20px;text-align:center;cursor:pointer;transition:border-color .15s, background .15s;">
                    <input type="file" name="images[]" multiple accept="image/*" id="proposeImages"
                           style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1459DD" stroke-width="1.8" style="margin:0 auto 8px;display:block;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <div style="font-size:14px;font-weight:600;color:#0f172a;">
                        {{ __('messages.demande_show.dropzone_title') }}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">
                        {{ __('messages.demande_show.or') }} <span style="color:#1459DD;font-weight:600;text-decoration:underline;">{{ __('messages.demande_show.browse') }}</span>
                    </div>
                    <div id="proposeCount" style="font-size:11px;color:#94a3b8;margin-top:6px;"></div>
                </div>

                <div id="proposePreview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
            </div>

            <button type="submit" class="dmd-reveal-btn" style="justify-content:center;">
                {{ __('messages.demande_show.submit') }}
            </button>
        </form>

        <style>
            #proposeDropzone.dz-hover { border-color:#1459DD !important; background:#eff6ff !important; }
            .dz-thumb { position:relative; width:76px; height:76px; }
            .dz-thumb img { width:100%; height:100%; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; display:block; }
            .dz-thumb .dz-remove {
                position:absolute; top:-6px; right:-6px; width:20px; height:20px;
                border-radius:50%; background:#dc2626; color:#fff; border:0;
                font-size:12px; line-height:18px; cursor:pointer; padding:0;
                box-shadow:0 2px 6px rgba(0,0,0,.2);
            }
        </style>

        <script>
            // Révèle le numéro de téléphone masqué d'une proposition de véhicule.
            function revealPropPhone(btn) {
                var full = btn.getAttribute('data-full');
                var el   = document.getElementById(btn.getAttribute('data-target'));
                if (el && full) {
                    el.textContent = '📞 ' + full;
                    el.setAttribute('dir', 'ltr');
                    el.style.display = 'inline-block';
                }
                btn.style.display = 'none';
            }
        </script>
        <script>
            (function () {
                var dz      = document.getElementById('proposeDropzone');
                var input   = document.getElementById('proposeImages');
                var preview = document.getElementById('proposePreview');
                var counter = document.getElementById('proposeCount');
                if (!dz || !input) return;

                var files = [];                       // selected files (DataTransfer-managed)
                var MAX = 8;
                var MAX_SIZE = 4 * 1024 * 1024;

                function render() {
                    preview.innerHTML = '';
                    files.forEach(function (f, idx) {
                        var wrap = document.createElement('div');
                        wrap.className = 'dz-thumb';
                        var img = document.createElement('img');
                        img.src = URL.createObjectURL(f);
                        var btn = document.createElement('button');
                        btn.type = 'button'; btn.className = 'dz-remove'; btn.textContent = '×';
                        btn.onclick = function (e) { e.stopPropagation(); files.splice(idx, 1); sync(); };
                        wrap.appendChild(img); wrap.appendChild(btn);
                        preview.appendChild(wrap);
                    });
                    counter.textContent = files.length
                        ? files.length + ' / ' + MAX + ' ' + @json(__('messages.demande_show.photos_selected'))
                        : '';
                }
                function sync() {
                    var dt = new DataTransfer();
                    files.forEach(function (f) { dt.items.add(f); });
                    input.files = dt.files;
                    render();
                }
                function add(list) {
                    Array.prototype.slice.call(list).forEach(function (f) {
                        if (!f.type.startsWith('image/')) return;
                        if (f.size > MAX_SIZE) return;
                        if (files.length >= MAX) return;
                        files.push(f);
                    });
                    sync();
                }

                input.addEventListener('change', function () { files = []; add(this.files); });
                ['dragenter', 'dragover'].forEach(function (ev) {
                    dz.addEventListener(ev, function (e) { e.preventDefault(); e.stopPropagation(); dz.classList.add('dz-hover'); });
                });
                ['dragleave', 'drop'].forEach(function (ev) {
                    dz.addEventListener(ev, function (e) { e.preventDefault(); e.stopPropagation(); dz.classList.remove('dz-hover'); });
                });
                dz.addEventListener('drop', function (e) {
                    if (e.dataTransfer && e.dataTransfer.files) add(e.dataTransfer.files);
                });
            })();
        </script>
    </div>
</div>
{{-- ============ /PROPOSE VEHICLE MODAL ============== --}}

{{-- ============== PROPOSE SUCCESS POPUP ============== --}}
@if(session('propose_success'))
<div id="proposeSuccess" style="position:fixed;inset:0;z-index:10001;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;padding:20px;font-family:'Inter',sans-serif;">
    <div style="background:#fff;max-width:420px;width:100%;border-radius:16px;box-shadow:0 30px 60px -20px rgba(15,23,42,.4);padding:28px 24px;text-align:center;">
        <div style="width:72px;height:72px;border-radius:50%;background:#ecfdf5;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-6"/>
            </svg>
        </div>
        <h3 style="margin:0 0 8px;font-size:20px;font-weight:800;color:#0f172a;">Merci !</h3>
        <p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.5;">
            {{ session('propose_success') }}
        </p>
        <button type="button" onclick="document.getElementById('proposeSuccess').remove();"
                style="padding:12px 28px;background:#1459DD;color:#fff;border:0;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;font-family:inherit;box-shadow:0 6px 18px rgba(20,89,221,.3);">
            D’accord
        </button>
    </div>
</div>
@endif
{{-- ============ /PROPOSE SUCCESS POPUP ============== --}}
@endsection
