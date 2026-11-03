<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.estimate.page_title') }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1459DD;
            --secondary: #F59E0B;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-900);
            min-height: 100vh;
        }

        /* NAV — handled by partials.nado-navbar */

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--slate-900) 0%, #1e3a8a 100%);
            padding: 5rem 2rem 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at center, rgba(20,89,221,0.3) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.3);
            color: var(--secondary);
            font-size: 0.7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            padding: 0.4rem 1rem;
            border-radius: 99px;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            color: var(--white);
            line-height: 0.9;
            letter-spacing: -0.04em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .hero h1 span { color: var(--secondary); }
        .hero p {
            font-size: 1.125rem;
            color: rgba(255,255,255,0.7);
            font-weight: 400;
            max-width: 36rem;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* MAIN LAYOUT */
        .page-body {
            max-width: 960px;
            margin: 0 auto;
            padding: 3rem 1.5rem 6rem;
        }

        /* RESULT CARD */
        .result-card {
            background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%);
            border-radius: 2rem;
            padding: 3rem;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(20,89,221,0.4);
        }
        .result-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(ellipse, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .result-label {
            font-size: 0.7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.75rem;
        }
        .result-value {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 900;
            color: var(--white);
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .result-currency {
            font-size: 1.5rem;
            font-weight: 400;
            color: rgba(255,255,255,0.7);
        }
        .result-subtitle {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.6);
            margin-top: 1rem;
        }
        .result-tags {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 2rem;
        }
        .result-tag {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.85);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.9rem;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* SECTION HEADING */
        .section-heading {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .section-icon {
            width: 44px;
            height: 44px;
            background: rgba(20,89,221,0.1);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .section-heading h2 {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.03em;
        }
        .section-heading p {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-top: 0.2rem;
        }

        /* FORM CARD */
        .form-card {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--slate-100);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }
        .form-grid .full { grid-column: 1 / -1; }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group label {
            font-size: 0.95rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--slate-900);
        }
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--slate-50);
            border: 1px solid var(--slate-100);
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--slate-700);
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }
        .form-group select:focus,
        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20,89,221,0.1);
            background: var(--white);
        }

        .btn-submit {
            width: 100%;
            padding: 1.1rem 2rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 0.875rem;
            font-size: 0.875rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 10px 20px -5px rgba(20,89,221,0.35);
        }
        .btn-submit:hover {
            background: #1248c4;
            transform: translateY(-1px);
            box-shadow: 0 15px 25px -5px rgba(20,89,221,0.4);
        }
        .btn-submit:active { transform: translateY(0); }

        /* DISCLAIMER */
        .disclaimer {
            background: rgba(20,89,221,0.05);
            border: 1px solid rgba(20,89,221,0.1);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            margin-top: 2rem;
            display: flex;
            gap: 0.875rem;
            align-items: flex-start;
        }
        .disclaimer p {
            font-size: 0.8rem;
            color: var(--slate-500);
            line-height: 1.6;
        }
        .disclaimer p strong { color: var(--primary); }

        /* FORM SECTIONS */
        .form-section-title {
            grid-column: 1 / -1;
            font-size: 0.75rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--primary);
            padding-top: 1rem;
            border-top: 1px solid var(--slate-100);
            margin-top: 0.5rem;
        }
        .form-section-title:first-child { border-top: 0; padding-top: 0; margin-top: 0; }
        .form-hint { font-size: 0.72rem; color: var(--slate-500); }
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 0.5rem;
        }
        .option-check {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 0.85rem;
            background: var(--slate-50);
            border: 1px solid var(--slate-100);
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--slate-700);
            cursor: pointer;
        }
        .form-group label.option-check { text-transform: none; letter-spacing: 0; font-size: 0.8125rem; font-weight: 600; color: var(--slate-700); }
        .form-group .option-check input {
            appearance: auto; -webkit-appearance: checkbox;
            width: 16px; height: 16px; padding: 0; margin: 0;
            border: 0; box-shadow: none; background: none;
            accent-color: var(--primary); flex-shrink: 0;
        }
        .option-check:has(input:checked) { border-color: var(--primary); background: rgba(20,89,221,0.06); }

        /* CONFIDENCE BADGE */
        .confidence-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1rem;
            padding: 0.35rem 0.9rem;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .confidence-badge .dot { width: 8px; height: 8px; border-radius: 50%; }
        .confidence-HIGH .dot { background: #22c55e; }
        .confidence-MEDIUM .dot { background: #F59E0B; }
        .confidence-LOW .dot { background: #ef4444; }

        /* ACTIONS */
        .result-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
            margin: -1.5rem 0 3rem;
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.4rem;
            background: var(--white);
            color: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: 0.875rem;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-secondary:hover { background: rgba(20,89,221,0.06); }

        /* BREAKDOWN */
        .report-card {
            background: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .report-print-header { display: none; }
        .report-title { font-size: 1.25rem; font-weight: 900; letter-spacing: -0.02em; margin-bottom: 1.25rem; }
        .calc-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid var(--slate-100);
        }
        .calc-row .calc-label { font-weight: 700; font-size: 0.9rem; color: var(--slate-900); }
        .calc-row .calc-source { font-size: 0.75rem; color: var(--slate-500); font-weight: 500; margin-top: 2px; }
        .calc-amount { font-weight: 800; font-size: 0.95rem; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .calc-amount.pos { color: #16a34a; }
        .calc-amount.neg { color: #dc2626; }
        .calc-amount.zero { color: var(--slate-400); }
        .calc-details { list-style: none; margin-top: 0.4rem; }
        .calc-details li { font-size: 0.75rem; color: var(--slate-500); line-height: 1.6; }
        .calc-details li::before { content: '· '; }
        .calc-lines { margin-top: 0.4rem; }
        .calc-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.78rem;
            color: var(--slate-700);
            padding: 0.15rem 0;
        }
        .calc-line span:last-child { white-space: nowrap; font-variant-numeric: tabular-nums; color: var(--slate-500); }
        .calc-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.1rem 0 0.25rem;
            font-size: 1.1rem;
            font-weight: 900;
        }
        .calc-total .calc-amount { font-size: 1.35rem; color: var(--primary); }
        .report-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.9rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--slate-100);
        }
        .report-meta dt { font-size: 0.65rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: var(--slate-400); }
        .report-meta dd { font-size: 0.82rem; font-weight: 600; color: var(--slate-700); margin-top: 2px; }
        .report-note { margin-top: 1.25rem; font-size: 0.75rem; color: var(--slate-500); line-height: 1.6; }

        @media (max-width: 600px) {
            .report-card { padding: 1.25rem; }
            .result-card { padding: 2rem 1.25rem; }
            .form-card { padding: 1.5rem 1.25rem; }
        }

        /* PRINT → "Enregistrer en PDF" */
        @media print {
            @page { margin: 14mm; }
            body { padding-top: 0 !important; background: #fff; }
            body * { visibility: hidden; }
            #valuation-print, #valuation-print * { visibility: visible; }
            #valuation-print { position: absolute; left: 0; top: 0; width: 100%; }
            #valuation-print [data-noprint] { display: none !important; }
            #valuation-print .result-card { box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; margin-bottom: 1rem; padding: 1.5rem; }
            #valuation-print .report-card { border: 0; box-shadow: none; padding: 0; }
            .report-print-header { display: flex !important; justify-content: space-between; margin-bottom: 1rem; font-size: 0.8rem; color: var(--slate-500); }
            .calc-row { break-inside: avoid; }
        }
    </style>
</head>
<body style="padding-top:110px;">

@include('partials.nado-navbar')

<!-- HERO -->
<div class="hero">
    <div class="hero-badge">
        <i data-lucide="zap" style="width:12px;height:12px;"></i>
        {{ __('messages.estimate.hero_badge') }}
    </div>
    <h1>{{ __('messages.estimate.hero_h1_1') }}<br><span>{{ __('messages.estimate.hero_h1_2') }}</span></h1>
    <p>{{ __('messages.estimate.hero_lead') }}</p>
</div>

<div class="page-body">

    @php
        $mad = fn ($n) => number_format(round($n), 0, ',', ' ') . ' MAD';
        $signed = fn ($n) => (round($n) > 0 ? '+' : (round($n) < 0 ? '−' : '')) . number_format(abs(round($n)), 0, ',', ' ') . ' MAD';
        $amountClass = fn ($n) => round($n) > 0 ? 'pos' : (round($n) < 0 ? 'neg' : 'zero');
        $pctText = fn ($p) => $p === null ? '' : (($p > 0 ? '+' : ($p < 0 ? '−' : '')) . rtrim(rtrim(number_format(abs($p), 1, ',', ''), '0'), ',') . ' %');
    @endphp

    {{-- RESULT --}}
    @isset($valuation)
    <div id="valuation-print">
        <div class="report-print-header">
            <strong style="color:var(--slate-900);">{{ __('messages.estimate.report_header') }}</strong>
            <span>{{ \Carbon\Carbon::parse($valuation['date'])->format('d/m/Y') }}</span>
        </div>

        <div class="result-card">
            <div class="result-label">{{ __('messages.estimate.result_label') }}</div>
            <div class="result-value">
                {{ number_format($valuation['final_value'], 0, ',', ' ') }}
                <span class="result-currency">MAD</span>
            </div>
            <div class="result-subtitle">
                {{ __('messages.estimate.range_label') }}
                <strong style="color:#fff;">
                    {{ number_format($valuation['range']['min'], 0, ',', ' ') }}
                    –
                    {{ number_format($valuation['range']['max'], 0, ',', ' ') }} MAD
                </strong>
            </div>
            <div class="confidence-badge confidence-{{ $valuation['confidence']['level'] }}">
                <span class="dot"></span> {{ __('messages.estimate.confidence_word') }} {{ __('messages.estimate.conf_'.strtolower($valuation['confidence']['level'])) }}
            </div>
            <div class="result-tags">
                <span class="result-tag">{{ $input['brand'] }}</span>
                <span class="result-tag">{{ $input['model'] }}</span>
                @if(!empty($input['version']))
                <span class="result-tag">{{ $input['version'] }}</span>
                @endif
                <span class="result-tag">{{ $input['year'] }}</span>
                <span class="result-tag">{{ number_format($input['km'], 0, ',', ' ') }} km</span>
                <span class="result-tag">{{ __('messages.estimate.fuel_'.$input['fuel']) }}</span>
                @if(!empty($input['gearbox']))
                <span class="result-tag">{{ $input['gearbox'] }}</span>
                @endif
                @if(!empty($input['power_cv']))
                <span class="result-tag">{{ $input['power_cv'] }} CV</span>
                @endif
                @if(!empty($input['origin']) && isset($originChoices[$input['origin']]))
                <span class="result-tag">{{ __('messages.estimate.orig_'.$input['origin']) }}</span>
                @endif
            </div>
        </div>

        <div class="result-actions" data-noprint>
            <a href="#estimation-form" class="btn-secondary">
                <i data-lucide="refresh-cw" style="width:16px;height:16px;"></i> {{ __('messages.estimate.recalculate') }}
            </a>
            <button type="button" class="btn-secondary" id="btn-download-pdf">
                <i data-lucide="file-down" style="width:16px;height:16px;"></i> {{ __('messages.estimate.download_pdf') }}
            </button>
        </div>

        <div class="report-card">
            <h2 class="report-title">{{ __('messages.estimate.report_title') }}</h2>

            <div class="calc-row">
                <div>
                    <div class="calc-label">{{ __('messages.estimate.base_value') }}</div>
                    <div class="calc-source">{{ __('messages.estimate.source') }} {{ $valuation['base']['source_label'] }}</div>
                </div>
                <div class="calc-amount">{{ $mad($valuation['base']['value']) }}</div>
            </div>

            <div class="calc-total">
                <span>{{ __('messages.estimate.estimated_value') }}</span>
                <span class="calc-amount">{{ $mad($valuation['final_value']) }}</span>
            </div>

            <dl class="report-meta">
                <div>
                    <dt>{{ __('messages.estimate.date_label') }}</dt>
                    <dd>{{ \Carbon\Carbon::parse($valuation['date'])->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt>{{ __('messages.estimate.methodology_label') }}</dt>
                    <dd>{{ $valuation['methodology'] }}</dd>
                </div>
                <div>
                    <dt>{{ __('messages.estimate.data_source') }}</dt>
                    <dd>{{ $valuation['base']['source_label'] }}</dd>
                </div>
                <div>
                    <dt>{{ __('messages.estimate.confidence_level') }}</dt>
                    <dd>
                        {{ __('messages.estimate.conf_'.strtolower($valuation['confidence']['level'])) }} —
                        {{ $valuation['confidence']['comparables'] }} {{ __('messages.estimate.comparables_unit') }}
                        @if($valuation['comparables_median'])
                            ({{ __('messages.estimate.median_price') }} {{ $mad($valuation['comparables_median']) }})
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>{{ __('messages.estimate.performed_by_label') }}</dt>
                    <dd>{{ $valuation['performed_by'] }}</dd>
                </div>
                <div>
                    <dt>{{ __('messages.estimate.age_label') }}</dt>
                    <dd>
                        {{ str_replace('.', ',', $valuation['age_years']) }} {{ __('messages.estimate.years_suffix') }}
                        @if($valuation['first_registration_estimated'])
                            <span class="form-hint">{{ __('messages.estimate.first_reg_est', ['year' => $input['year']]) }}</span>
                        @endif
                    </dd>
                </div>
            </dl>

            <p class="report-note">
                {!! __('messages.estimate.report_note') !!}
            </p>
        </div>
    </div>
    @endisset

    {{-- FORM --}}
    <div class="section-heading">
        <div class="section-icon">
            <i data-lucide="car" style="width:20px;height:20px;color:var(--primary);"></i>
        </div>
        <div>
            <h2>{{ __('messages.estimate.form_q_title') }}</h2>
            <p>{{ __('messages.estimate.form_q_sub') }}</p>
        </div>
    </div>

    @php
        // Value to show in a field: old input (validation error) → last estimate → default
        $val = fn ($key, $default = null) => old($key, $input[$key] ?? $default);
        $selectedOptions = (array) old('options', $input['options'] ?? []);
    @endphp
    <div class="form-card" id="estimation-form">
        <form method="POST" action="{{ route('estimate.value') }}">
            @csrf
            <div class="form-grid">

                <div class="form-section-title">{{ __('messages.estimate.sec1') }}</div>

                {{-- Marque --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.marque') }}</label>
                    <select name="brand" id="est-brand" required>
                        <option value="">{{ __('messages.estimate.select_brand') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->name }}"
                                data-id="{{ $brand->id }}"
                                {{ $val('brand') === $brand->name ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Modèle --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.modele') }}</label>
                    <select name="model" id="est-model" required>
                        <option value="">{{ __('messages.estimate.select_model') }}</option>
                        @if($val('model'))
                            <option value="{{ $val('model') }}" selected>{{ $val('model') }}</option>
                        @endif
                    </select>
                    @error('model')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Version --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.version') }} <span style="font-weight:400;font-size:0.78rem;color:var(--slate-500);">{{ __('messages.estimate.optional') }}</span></label>
                    {{-- Filled by JS from the chosen brand (trimsByBrand) --}}
                    <select name="version" id="est-version">
                        <option value="">{{ __('messages.estimate.choose_brand_first') }}</option>
                    </select>
                    <input type="text" name="version_other" id="est-version-other" maxlength="100"
                        placeholder="{{ __('messages.estimate.version_ph') }}" style="display:none;margin-top:0.5rem;"
                        value="{{ old('version_other') }}">
                    @error('version')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Date de 1ère mise en circulation --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.first_reg') }}</label>
                    <input type="date" name="first_registration" id="est-first-registration" required
                        min="1990-01-01" max="{{ date('Y-m-d') }}" value="{{ $val('first_registration') }}">
                    <span class="form-hint">{{ __('messages.estimate.see_cg') }}</span>
                    @error('first_registration')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Kilométrage --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.mileage') }}</label>
                    <input type="number" name="km" placeholder="{{ __('messages.estimate.mileage_ph') }}" min="0" required value="{{ $val('km') }}">
                    @error('km')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Carburant --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.fuel') }}</label>
                    <select name="fuel" required>
                        <option value="">{{ __('messages.estimate.fuel_type') }}</option>
                        @foreach($fuelChoices as $value => $label)
                            <option value="{{ $value }}" {{ $val('fuel') === $value ? 'selected' : '' }}>{{ __('messages.estimate.fuel_'.$value) }}</option>
                        @endforeach
                    </select>
                    @error('fuel')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Puissance fiscale --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.fiscal_power') }} <span style="font-weight:400;font-size:0.78rem;color:var(--slate-500);">{{ __('messages.estimate.recommended') }}</span></label>
                    <input type="number" name="power_cv" min="1" max="200" placeholder="{{ __('messages.estimate.fiscal_ph') }}" value="{{ $val('power_cv') }}">
                    <span class="form-hint">{{ __('messages.estimate.fiscal_hint') }}</span>
                    @error('power_cv')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                {{-- Boîte --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.gearbox') }}</label>
                    <select name="gearbox">
                        <option value="">{{ __('messages.estimate.select') }}</option>
                        @foreach(['Manuelle','Automatique'] as $g)
                            <option value="{{ $g }}" {{ $val('gearbox') === $g ? 'selected' : '' }}>{{ $g === 'Manuelle' ? __('messages.ventemodal.box_manual') : __('messages.ventemodal.box_auto') }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Origine --}}
                <div class="form-group">
                    <label>{{ __('messages.estimate.origin') }}</label>
                    <select name="origin">
                        <option value="">{{ __('messages.estimate.dont_know') }}</option>
                        @foreach($originChoices as $value => $label)
                            <option value="{{ $value }}" {{ $val('origin') === $value ? 'selected' : '' }}>{{ __('messages.estimate.orig_'.$value) }}</option>
                        @endforeach
                    </select>
                    <span class="form-hint">{{ __('messages.estimate.origin_hint') }}</span>
                    @error('origin')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

                <div class="form-section-title">{{ __('messages.estimate.sec2') }}</div>

                @foreach([
                    'condition_mechanical' => 'cond_mechanical',
                    'condition_body'       => 'cond_body',
                    'condition_interior'   => 'cond_interior',
                ] as $field => $lkey)
                    <div class="form-group">
                        <label>{{ __('messages.estimate.'.$lkey) }}</label>
                        <select name="{{ $field }}" required>
                            @foreach($conditionChoices as $value => $choice)
                                <option value="{{ $value }}" {{ $val($field, 'bon') === $value ? 'selected' : '' }}>{{ __('messages.estimate.cond_'.$value) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div class="form-group">
                    <label>{{ __('messages.estimate.maintenance') }}</label>
                    <select name="maintenance" required>
                        @foreach($maintenanceChoices as $value => $choice)
                            <option value="{{ $value }}" {{ $val('maintenance', 'partiel') === $value ? 'selected' : '' }}>{{ __('messages.estimate.maint_'.$value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.estimate.owners') }} <span style="font-weight:400;font-size:0.78rem;color:var(--slate-500);">{{ __('messages.estimate.optional') }}</span></label>
                    <select name="owners">
                        <option value="">{{ __('messages.estimate.dont_know') }}</option>
                        @foreach($ownerChoices as $value => $choice)
                            <option value="{{ $value }}" {{ (string) $val('owners') === (string) $value ? 'selected' : '' }}>{{ __('messages.estimate.owners_'.$value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.estimate.accident') }}</label>
                    <select name="accident" required>
                        @foreach($accidentChoices as $value => $choice)
                            <option value="{{ $value }}" {{ $val('accident', 'aucun') === $value ? 'selected' : '' }}>{{ __('messages.estimate.acc_'.$value) }}</option>
                        @endforeach
                    </select>
                    <span class="form-hint">{{ __('messages.estimate.accident_hint') }}</span>
                </div>

                <div class="form-section-title">{{ __('messages.estimate.sec3') }}</div>

                <div class="form-group full">
                    <div class="options-grid">
                        @foreach($optionChoices as $value => $label)
                            <label class="option-check">
                                <input type="checkbox" name="options[]" value="{{ $value }}"
                                    {{ in_array($value, $selectedOptions, true) ? 'checked' : '' }}>
                                {{ __('messages.estimate.opt_'.$value) }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-section-title">{{ __('messages.estimate.sec4') }}</div>

                <div class="form-group full">
                    <label>{{ __('messages.estimate.ref_value') }} <span style="font-weight:400;font-size:0.78rem;color:var(--slate-500);">{{ __('messages.estimate.optional') }}</span></label>
                    <input type="number" name="reference_value" placeholder="{{ __('messages.estimate.ref_ph') }}" min="0" step="500"
                        value="{{ $val('reference_value') }}">
                    <span class="form-hint">{{ __('messages.estimate.ref_hint') }}</span>
                    @error('reference_value')<span style="font-size:0.75rem;color:#ef4444;">{{ $message }}</span>@enderror
                </div>

            </div>

            <button type="submit" class="btn-submit">
                <i data-lucide="calculator" style="width:18px;height:18px;"></i>
                {{ isset($valuation) ? __('messages.estimate.recalculate') : __('messages.estimate.submit_estimate') }}
            </button>
        </form>
    </div>

    <div class="disclaimer">
        <i data-lucide="info" style="width:18px;height:18px;color:var(--primary);flex-shrink:0;margin-top:1px;"></i>
        <p>{!! __('messages.estimate.disclaimer') !!}</p>
    </div>

</div>

<script>
    lucide.createIcons();

    // i18n strings injected from messages.php so JS follows the active locale
    var EST_I18N = {
        dontKnow:     @json(__('messages.estimate.dont_know')),
        chooseBrand:  @json(__('messages.estimate.choose_brand_first')),
        other:        @json(__('messages.estimate.js_other')),
        selectModel:  @json(__('messages.estimate.select_model')),
        generating:   @json(__('messages.estimate.js_generating')),
        pdfError:     @json(__('messages.estimate.js_pdf_error')),
    };

    var modelsUrl = '{{ route('achat.models.by.brand', '') }}/';

    // ── Version / finition list, based on the brand ──
    var trimsByBrand = @json($trimsByBrand ?? []);
    var versionSelect = document.getElementById('est-version');
    var versionOther = document.getElementById('est-version-other');

    function fillVersions(brand, selected) {
        var trims = trimsByBrand[brand] || [];
        versionSelect.innerHTML = '';
        var first = document.createElement('option');
        first.value = '';
        first.textContent = brand ? EST_I18N.dontKnow : EST_I18N.chooseBrand;
        versionSelect.appendChild(first);
        if (!brand) { toggleVersionOther(); return; }

        var found = !selected;
        trims.forEach(function (t) {
            var opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            if (t === selected) { opt.selected = true; found = true; }
            versionSelect.appendChild(opt);
        });
        var other = document.createElement('option');
        other.value = '__other';
        other.textContent = EST_I18N.other;
        versionSelect.appendChild(other);

        // A previous value that is not in the list → "Autre" with the text filled in
        if (!found) {
            other.selected = true;
            versionOther.value = selected;
        }
        toggleVersionOther();
    }

    function toggleVersionOther() {
        var isOther = versionSelect.value === '__other';
        versionOther.style.display = isOther ? 'block' : 'none';
        versionOther.required = isOther;
    }

    versionSelect.addEventListener('change', function () {
        toggleVersionOther();
        if (versionSelect.value === '__other') versionOther.focus();
    });
    fillVersions(document.getElementById('est-brand').value, @json(old('version') === '__other' ? old('version_other') : $val('version')));

    document.getElementById('est-brand').addEventListener('change', function () {
        fillVersions(this.value, '');
        var selectedOption = this.options[this.selectedIndex];
        var brandId = selectedOption.getAttribute('data-id');
        var modelSelect = document.getElementById('est-model');
        modelSelect.innerHTML = '<option value="">' + EST_I18N.selectModel + '</option>';
        if (!brandId) return;
        fetch(modelsUrl + brandId)
            .then(function(res){ return res.json(); })
            .then(function(models){
                models.forEach(function(m){
                    var opt = document.createElement('option');
                    opt.value = m.name;
                    opt.textContent = m.name;
                    modelSelect.appendChild(opt);
                });
            });
    });

    // If brand is pre-selected (after form submit), reload models
    (function(){
        var brandSelect = document.getElementById('est-brand');
        var selectedOption = brandSelect.options[brandSelect.selectedIndex];
        var brandId = selectedOption ? selectedOption.getAttribute('data-id') : null;
        var preselectedModel = @json($val('model'));
        if (brandId) {
            fetch(modelsUrl + brandId)
                .then(function(res){ return res.json(); })
                .then(function(models){
                    var modelSelect = document.getElementById('est-model');
                    modelSelect.innerHTML = '<option value="">' + EST_I18N.selectModel + '</option>';
                    models.forEach(function(m){
                        var opt = document.createElement('option');
                        opt.value = m.name;
                        opt.textContent = m.name;
                        if (m.name === preselectedModel) opt.selected = true;
                        modelSelect.appendChild(opt);
                    });
                });
        }
    })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Downloads the report as a real PDF file.
    //
    // html2pdf's own .save() relies on a programmatic <a download> click, which iOS
    // Safari and several Android browsers ignore without raising an error - the button
    // then appeared to do nothing. We build the blob ourselves and pick the delivery
    // method per browser, with a visible message if anything fails.
    (function () {
        var btn = document.getElementById('btn-download-pdf');
        var report = document.getElementById('valuation-print');
        if (!btn || !report) return;

        var fileName = @json(
            'estimation-nado-'
            . \Illuminate\Support\Str::slug(($input['brand'] ?? '') . '-' . ($input['model'] ?? '') . '-' . ($input['year'] ?? ''))
            . '-' . date('Y-m-d') . '.pdf'
        );

        var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        var isSmall = window.innerWidth < 900;

        function deliver(blob) {
            var url = URL.createObjectURL(blob);

            // iOS cannot save a blob from a link: show it in the PDF viewer, where the
            // share sheet offers "Save to Files".
            if (isIOS) {
                var win = window.open(url, '_blank');
                if (!win) location.href = url;
            } else {
                var a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                a.rel = 'noopener';
                document.body.appendChild(a);
                a.click();
                a.remove();
            }

            setTimeout(function () { URL.revokeObjectURL(url); }, 30000);
        }

        btn.addEventListener('click', function () {
            if (typeof html2pdf === 'undefined') {
                window.print();          // library blocked: fall back to the print dialog
                return;
            }

            var original = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = EST_I18N.generating;

            var hidden = report.querySelectorAll('[data-noprint]');
            Array.prototype.forEach.call(hidden, function (el) {
                el.dataset.prevDisplay = el.style.display;
                el.style.display = 'none';
            });

            function restore() {
                Array.prototype.forEach.call(hidden, function (el) {
                    el.style.display = el.dataset.prevDisplay || '';
                });
                btn.disabled = false;
                btn.innerHTML = original;
            }

            html2pdf()
                .set({
                    margin:      [10, 10, 12, 10],
                    filename:    fileName,
                    image:       { type: 'jpeg', quality: 0.92 },
                    html2canvas: {
                        // A phone would otherwise render the narrow mobile layout, and
                        // scale 2 can exhaust memory on older devices.
                        scale: isSmall ? 1.5 : 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        scrollY: 0,
                        windowWidth: 1100
                    },
                    jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
                    pagebreak:   { mode: ['css', 'legacy'], avoid: ['.calc-row', '.result-card'] }
                })
                .from(report)
                .toPdf()
                .get('pdf')
                .then(function (pdf) {
                    deliver(pdf.output('blob'));
                    restore();
                })
                .catch(function (err) {
                    restore();
                    console.error('PDF generation failed:', err);
                    alert(EST_I18N.pdfError);
                });
        });
    })();
</script>
</body>
</html>
