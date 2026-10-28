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
</style>
@endif
