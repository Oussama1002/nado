{{-- Arabic web font + RTL base styles.
     Include inside the <head> of any page that should support Arabic. --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
@if(app()->getLocale() === 'ar')
<style>
    html[dir="rtl"] body { direction: rtl; text-align: right; font-family: 'Cairo', 'Tajawal', 'Segoe UI', sans-serif; }
    html[dir="rtl"] .container,
    html[dir="rtl"] section,
    html[dir="rtl"] footer { text-align: right; }

    /* The two custom navbars hard-set direction:ltr — let them follow RTL. */
    html[dir="rtl"] .nado-navbar,
    html[dir="rtl"] .nado-navbar *,
    html[dir="rtl"] .main-nav,
    html[dir="rtl"] .main-nav * { direction: rtl !important; }

    /* Keep the right-side action cluster on the inline-end edge after flipping. */
    html[dir="rtl"] .nado-navbar .nv-right { margin-left: 0 !important; margin-right: auto !important; }
    html[dir="rtl"] .main-nav .right-section { margin-left: 0 !important; margin-right: auto !important; }

    /* The FR | AR switch reads left-to-right in any layout. */
    html[dir="rtl"] .lang-switch { direction: ltr !important; }

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
    html[dir="rtl"] a[href^="tel:"] {
        direction: ltr !important;
        unicode-bidi: isolate;
    }
</style>
@endif
