{{--
    Shared site footer — self-contained (own styles + inline SVG icons), so it
    renders correctly on standalone pages that don't load the homepage CSS vars
    or Lucide. Prefixed `nf-` classes avoid colliding with per-page `footer`
    rules. Mirrors the homepage footer markup/labels.
--}}
<footer class="nf-footer">
    <style>
        .nf-footer {
            background-color: #0f172a;
            color: #ffffff;
            padding: 6rem 0;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }
        .nf-footer * { box-sizing: border-box; }
        .nf-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .nf-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        @media (min-width: 768px) {
            .nf-grid { grid-template-columns: repeat(4, 1fr); }
        }
        .nf-logo {
            font-size: 1.875rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            line-height: 1;
        }
        .nf-logo .nf-na { color: #1459DD; }
        .nf-logo .nf-do { color: #FDD817; }
        .nf-tagline {
            color: #94a3b8;
            font-weight: 500;
            line-height: 1.625;
            margin: 0;
        }
        .nf-heading {
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: #FDD817;
        }
        .nf-links { list-style: none; margin: 0; padding: 0; }
        .nf-links li { margin-bottom: 1rem; }
        .nf-links a {
            font-size: 0.875rem;
            font-weight: 700;
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }
        .nf-links a:hover { color: #ffffff; }
        .nf-links a.nf-accent { color: #FDD817; }
        .nf-social-note {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }
        .nf-social { display: flex; gap: 1rem; }
        .nf-social-btn {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            cursor: pointer;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: background-color 0.2s;
        }
        .nf-social-btn:hover { background-color: #1459DD; }
        .nf-social-btn svg { width: 16px; height: 16px; }
        .nf-bottom {
            padding-top: 2rem;
            border-top: 1px solid #475569;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    </style>

    <div class="nf-container">
        <div class="nf-grid">
            <div>
                <div class="nf-logo">
                    <span class="nf-na">NA</span><span class="nf-do">DO</span>
                </div>
                <p class="nf-tagline">{{ __('messages.extra.footer_tagline') }}</p>
            </div>
            <div>
                <h4 class="nf-heading">{{ __('messages.extra.nav_heading') }}</h4>
                <ul class="nf-links">
                    <li><a href="{{ url('/') }}">{{ __('messages.extra.footer_home') }}</a></li>
                    <li><a href="{{ url('/achat') }}">{{ __('messages.extra.footer_buy') }}</a></li>
                    <li><a href="{{ url('/vente') }}">{{ __('messages.extra.footer_sell') }}</a></li>
                    <li><a href="{{ url('/about') }}">{{ __('messages.extra.about_nado') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="nf-heading">{{ __('messages.extra.services_heading') }}</h4>
                <ul class="nf-links">
                    <li><a href="#" class="nf-accent">NADO Connect</a></li>
                    <li><a href="{{ url('/nadoautoenchere') }}">Auto Enchère</a></li>
                    <li><a href="#">{{ __('messages.extra.footer_vente_cash') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="nf-heading">{{ __('messages.extra.social_networks') }}</h4>
                <p class="nf-social-note">{{ __('messages.extra.footer_social_note') }}</p>
                <div class="nf-social">
                    <button type="button" class="nf-social-btn" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </button>
                    <button type="button" class="nf-social-btn" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </button>
                    <button type="button" class="nf-social-btn" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="nf-bottom">{{ __('messages.extra.footer_rights') }}</div>
    </div>
</footer>
