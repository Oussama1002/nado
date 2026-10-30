{{-- Arabic web font + RTL base styles.
     Include inside the <head> of any page that should support Arabic. --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
@if(app()->getLocale() === 'ar')
<style>
    html[dir="rtl"] body { direction: rtl; text-align: right; font-family: 'Cairo', 'Tajawal', 'Segoe UI', sans-serif; }
    html[dir="rtl"] .container,
    html[dir="rtl"] section,
    html[dir="rtl"] footer { text-align: right; }

    /* Navbars keep their natural LTR structure in Arabic too: logo always on
       the LEFT and the action cluster on the right (same as the login navbar).
       Only the link text is Arabic — bidi renders the Arabic glyphs correctly
       inside the LTR-ordered bar. */
    html[dir="rtl"] .nado-navbar,
    html[dir="rtl"] .nado-navbar *,
    html[dir="rtl"] .main-nav,
    html[dir="rtl"] .main-nav * { direction: ltr !important; }

    /* Keep the right-side action cluster pinned to the right edge (LTR). */
    html[dir="rtl"] .nado-navbar .nv-right-section,
    html[dir="rtl"] .nado-navbar .nv-right,
    html[dir="rtl"] .main-nav .right-section { margin-left: auto !important; margin-right: 0 !important; }

    /* The FR | AR switch reads left-to-right in any layout. */
    html[dir="rtl"] .lang-switch { direction: ltr !important; }

    /* Arabic (Cairo) renders visually smaller than Latin at the same px,
       so enlarge the navbar links/buttons in RTL for readability. */
    html[dir="rtl"] .nav-link,
    html[dir="rtl"] .nv-nav-link,
    html[dir="rtl"] .desktop-menu a,
    html[dir="rtl"] .nv-desktop-menu a,
    html[dir="rtl"] .drawer-links a,
    html[dir="rtl"] .nv-drawer-links a,
    html[dir="rtl"] .nav-dropdown-menu a,
    html[dir="rtl"] .nv-user-dropdown-menu a,
    html[dir="rtl"] .user-dropdown-menu a,
    html[dir="rtl"] .main-nav .nav-item > a,
    html[dir="rtl"] .cta-button,
    html[dir="rtl"] .nv-cta-button {
        font-size: 16.5px !important;
        font-weight: 700;
    }

    /* Numbers, prices, km and phone numbers must stay left-to-right even in
       RTL, otherwise the digit groups bidi-reorder (e.g. "163 000" -> "000 163").
       Isolating keeps them LTR without affecting the surrounding Arabic flow. */
    html[dir="rtl"] .pp-price-overlay,
    html[dir="rtl"] .pp-monthly-val,
    html[dir="rtl"] .pp-spec,
    html[dir="rtl"] .pp-status,
    html[dir="rtl"] .pp-slider-val,
    html[dir="rtl"] .pp-slider-ticks,
    html[dir="rtl"] .pp-results-count,
    html[dir="rtl"] .result-amount,
    html[dir="rtl"] .slider-value,
    html[dir="rtl"] .stat-item-num,
    html[dir="rtl"] .hero-float-num,
    html[dir="rtl"] .dmd-meta span,
    html[dir="rtl"] .dmd-phone,
    html[dir="rtl"] .result-amount,
    html[dir="rtl"] .nv-info,
    html[dir="rtl"] .info-item,
    /* Front homepage listing & auction cards (price, year, km, monthly, bid). */
    html[dir="rtl"] .listing-price-text,
    html[dir="rtl"] .listing-spec-label,
    html[dir="rtl"] .monthly-amount,
    html[dir="rtl"] .bid-amount,
    /* Car detail page pricing card — keep "163 000 MAD" in order. */
    html[dir="rtl"] .price-row,
    html[dir="rtl"] .price-amount,
    html[dir="rtl"] .price-currency,
    html[dir="rtl"] a[href^="tel:"] {
        direction: ltr !important;
        unicode-bidi: isolate;
    }
</style>
@endif
