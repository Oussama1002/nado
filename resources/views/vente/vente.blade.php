{{-- ?embed=1 (client dashboard popup) uses the bare layouts/embed.blade.php; falls back
     to the normal layout if that file is missing instead of crashing the page --}}
@extends(request()->boolean('embed') && view()->exists('layouts.embed') ? 'layouts.embed' : 'layouts.app')

@section('title', 'Vendre ma voiture – NADO')

@section('link')
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* =============================================
   VENTE MARKETING PAGE — DESIGN SYSTEM
   Primary : #1459DD | Accent : #FDD817
============================================= */
:root {
    --vp-blue   : #1459DD;
    --vp-yellow : #FDD817;
    --vp-slate  : #0f172a;
    --vp-muted  : #64748b;
    --vp-slate50: #f8fafc;
    --vp-border : #e2e8f0;
    --vp-white  : #ffffff;
}
.vente-mkt { font-family: 'Inter', -apple-system, sans-serif; color: var(--vp-slate); overflow-x: hidden; }
.vente-mkt *, .vente-mkt *::before, .vente-mkt *::after { box-sizing: border-box; }

/* ── NAVBAR (match home page sizing) ──
   On most pages, Bootstrap/theme styles affect `.container`.
   Home page defines its own `.container` sizing, so we normalize it here
   only inside the global navbar to get the same look. */
.nado-navbar .container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 16px;
    width: 100%;
}

/* ── CONTAINER ── */
.vmkt-wrap    { max-width: 1280px; margin: 0 auto; padding: 0 24px; }
.vmkt-wrap-sm { max-width: 1024px; margin: 0 auto; padding: 0 24px; }

/* ── BREADCRUMB ── */
.vp-breadcrumb { background: var(--vp-white); border-bottom: 1px solid var(--vp-border); padding: 12px 0; }
.vp-breadcrumb nav { display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--vp-muted); }
.vp-breadcrumb nav a { color: inherit; text-decoration: none; }
.vp-breadcrumb nav a:hover { color: var(--vp-blue); }
.vp-breadcrumb nav .bc-cur { color: var(--vp-slate); }

/* ── HERO ── */
.vp-hero {
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: var(--vp-white);
    /* This page already sits below the fixed global navbar + spacer */
    padding: 36px 0 60px;
}
.vp-hero-dots {
    position: absolute; inset: 0;
    opacity: .03;
    background-image: radial-gradient(#1459DD 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}
.vp-hero-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 48px;
    align-items: center;
    width: 100%;
}
@media (min-width: 1024px) { .vp-hero-grid { grid-template-columns: 1fr 1fr; } }

.vp-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    background: var(--vp-yellow);
    color: var(--vp-slate);
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .3em;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,.15);
    margin-bottom: 28px;
}
.vp-h1 {
    font-size: clamp(3.25rem, calc(8vw + 0.5rem), 5.75rem);
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -.04em;
    line-height: 0.92;
    margin-bottom: 24px;
}
.vp-h1-grad {
    background: linear-gradient(135deg, var(--vp-blue) 0%, #7ea9ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.vp-subtitle { font-size: 1.25rem; /* text-xl */ color: var(--vp-muted); font-weight: 500; line-height: 1.75; max-width: 480px; margin-bottom: 36px; }
.vp-subtitle strong { color: var(--vp-blue); }

.vp-props { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 44px; }
.vp-prop {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 18px;
    background: var(--vp-slate50);
    border-radius: 9999px;
    font-size: 13px; font-weight: 700; color: var(--vp-slate);
}
.vp-prop-check { color: #22c55e; }

.vp-cta-btn {
    display: inline-flex; align-items: center; gap: 12px;
    padding: 20px 48px;
    background: var(--vp-blue); color: var(--vp-white);
    border: none; border-radius: 9999px;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em;
    cursor: pointer;
    box-shadow: 0 25px 50px -12px rgba(20,89,221,.4);
    transition: background .2s, transform .1s;
}
.vp-cta-btn:hover { background: #0f46b1; }
.vp-cta-btn:active { transform: scale(.96); }
.vp-cta-arrow { transition: transform .2s; }
.vp-cta-btn:hover .vp-cta-arrow { transform: translateX(6px); }

/* Hero visual */
.vp-hero-visual { display: none; }
@media (min-width: 1024px) { .vp-hero-visual { display: block; } }
.vp-visual-card {
    border-radius: 3.5rem; overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.2);
    border: 1px solid var(--vp-border);
    aspect-ratio: 4/3;
    background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%);
    position: relative;
}
.vp-visual-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(15,23,42,.9) 0%, rgba(15,23,42,.5) 50%, transparent 100%);
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 40px;
}
.vp-offer-pill {
    display: inline-flex; align-items: center; gap: 10px;
    background: rgba(255,255,255,.08); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.15); border-radius: 9999px;
    padding: 12px 20px; margin-bottom: 20px; width: fit-content;
}
.vp-offer-zap  { color: var(--vp-yellow); }
.vp-offer-text { color: white; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; }
.vp-offer-price { font-size: 2rem; font-weight: 900; color: white; }
.vp-offer-price span { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.6); }
.vp-visual-car { font-size: 5rem; text-align: center; opacity: .18; margin-bottom: 20px; }

/* ── SECTION SHARED ── */
.vp-section-eyebrow { font-size: 10px; font-weight: 900; color: var(--vp-blue); text-transform: uppercase; letter-spacing: .5em; margin-bottom: 16px; }
.vp-section-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.04em; line-height: 1; margin-bottom: 20px; }
.vp-section-sub { font-size: 1.05rem; color: var(--vp-muted); font-weight: 500; max-width: 560px; margin: 0 auto 64px; }

/* ── BANNER ── */
.vp-banner {
    width: 100%; max-width: 1000px; height: 100px;
    margin: 48px auto;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, var(--vp-blue) 0%, #7ea9ff 100%);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.5);
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em;
    border: 1px dashed rgba(255,255,255,.2);
    overflow: hidden;
}
@media (min-width: 768px)  { .vp-banner { height: 140px; } }
@media (min-width: 1024px) { .vp-banner { height: 225px; font-size: 13px; } }
.vp-banner-inner { text-align: center; }
.vp-banner-inner p { font-size: 1.5rem; opacity: .2; margin-bottom: 4px; }

/* ── CLIENT TYPES ── */
.vp-clients { background: var(--vp-slate50); padding: 96px 0; }
.vp-clients-grid { display: grid; grid-template-columns: 1fr; gap: 32px; }
@media (min-width: 768px) { .vp-clients-grid { grid-template-columns: repeat(3, 1fr); } }

.vp-client-card {
    background: var(--vp-white);
    border-radius: 2.5rem; overflow: hidden;
    border: 1px solid var(--vp-border);
    cursor: pointer;
    transition: border-color .3s, box-shadow .3s, transform .3s;
}
.vp-client-card:hover { border-color: var(--vp-blue); box-shadow: 0 25px 50px -12px rgba(20,89,221,.15); transform: translateY(-4px); }
.vp-card-media { aspect-ratio: 16/9; overflow: hidden; position: relative; }
.vp-card-media-bg { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; transition: transform .7s; }
.vp-client-card:hover .vp-card-media-bg { transform: scale(1.08); }
.vp-card-hover-overlay { position: absolute; inset: 0; background: rgba(20,89,221,.8); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s; }
.vp-client-card:hover .vp-card-hover-overlay { opacity: 1; }
.vp-hover-pill {
    padding: 12px 28px; background: var(--vp-yellow); border-radius: 9999px;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .15em; color: var(--vp-slate);
    transform: translateY(16px); transition: transform .3s;
}
.vp-client-card:hover .vp-hover-pill { transform: translateY(0); }
.vp-card-body { padding: 28px; }
.vp-card-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 9999px;
    font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .15em;
    margin-bottom: 14px;
}
.badge-particulier { background: #dbeafe; color: var(--vp-blue); }
.badge-certifie    { background: #dcfce7; color: #166534; }
.badge-nado        { background: #fef9c3; color: #854d0e; }
.vp-card-title { font-size: 1.35rem; font-weight: 900; text-transform: uppercase; letter-spacing: -.02em; margin-bottom: 10px; }
.vp-card-desc  { font-size: 13px; color: var(--vp-muted); font-weight: 500; line-height: 1.55; }

/* ── HOW IT WORKS ── */
.vp-how { background: var(--vp-white); padding: 96px 0; }
.vp-how-grid { display: grid; grid-template-columns: 1fr; gap: 48px; }
@media (min-width: 1024px) { .vp-how-grid { grid-template-columns: repeat(2, 1fr); } }
.vp-how-card { border: 2px solid var(--vp-border); border-radius: 3rem; padding: 40px; transition: border-color .3s, box-shadow .3s; }
.vp-how-card:hover { border-color: var(--vp-blue); box-shadow: 0 25px 50px -12px rgba(20,89,221,.1); }
.vp-how-card-media { aspect-ratio: 16/9; border-radius: 2rem; overflow: hidden; border: 1px solid var(--vp-border); margin-bottom: 32px; display: flex; align-items: center; justify-content: center; font-size: 5rem; }
.vp-how-card-title { font-size: 1.5rem; font-weight: 900; text-transform: uppercase; letter-spacing: -.02em; margin-bottom: 12px; }
.vp-how-card-desc  { font-size: 14px; color: var(--vp-muted); line-height: 1.6; }
.vp-how-steps { list-style: none; padding: 0; margin: 20px 0 0; display: flex; flex-direction: column; gap: 12px; }
.vp-how-step  { display: flex; align-items: flex-start; gap: 12px; font-size: 13px; font-weight: 600; color: var(--vp-muted); }
.vp-step-num  { width: 28px; height: 28px; border-radius: 50%; background: var(--vp-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; flex-shrink: 0; }

/* ── HOW SECTION enhancements ── */
.vp-how-canal-num {
    width: 64px; height: 64px; background: var(--vp-blue); color: white;
    border-radius: 1rem; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; font-weight: 900; flex-shrink: 0;
}
.vp-how-check-list { list-style: none; padding: 0; margin: 24px 0 32px; display: flex; flex-direction: column; gap: 14px; }
.vp-how-check-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #334155; }
.vp-how-check-icon { color: var(--vp-blue); flex-shrink: 0; }
.vp-how-cta-btn {
    width: 100%; padding: 20px;
    background: var(--vp-blue); color: white;
    border: none; border-radius: 1rem;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
    transition: background .2s;
}
.vp-how-cta-btn:hover { background: #0f46b1; }
.vp-address-card {
    background: white; border-radius: 1rem; padding: 24px;
    border: 1px solid var(--vp-border); margin: 24px 0;
    display: flex; gap: 14px; align-items: flex-start;
}
.vp-address-pin { color: var(--vp-blue); flex-shrink: 0; margin-top: 2px; }

/* ── SOLUTIONS ── */
.vp-solutions { background: var(--vp-slate50); padding: 96px 0; }
.vp-solutions-list { display: flex; flex-direction: column; gap: 80px; }
.vp-solution-item { display: grid; grid-template-columns: 1fr; gap: 48px; align-items: center; }
@media (min-width: 1024px) { .vp-solution-item { grid-template-columns: repeat(2, 1fr); } }
.vp-sol-media {
    aspect-ratio: 4/3; border-radius: 3rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.15);
    border: 1px solid var(--vp-border);
    overflow: hidden; position: relative;
}
.vp-sol-media img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .7s ease;
}
.vp-solution-item:hover .vp-sol-media img { transform: scale(1.08); }
.vp-sol-float-badge {
    position: absolute; top: 20px; left: 20px;
    padding: 10px 20px; border-radius: 9999px;
    font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em;
    color: white; box-shadow: 0 8px 16px rgba(0,0,0,.2);
}
.vp-sol-float-badge.badge-yellow { color: var(--vp-slate); }
.vp-sol-body { text-align: left; }
.vp-sol-eyebrow { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .4em; color: var(--vp-blue); margin-bottom: 14px; }
.vp-sol-title   { font-size: clamp(1.6rem, 3.5vw, 2.5rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.03em; line-height: 1.05; margin-bottom: 16px; }
.vp-sol-desc    { font-size: 15px; color: var(--vp-muted); line-height: 1.65; margin-bottom: 28px; }
.vp-sol-feats   { list-style: none; padding: 0; margin-bottom: 36px; display: flex; flex-direction: column; gap: 12px; }
.vp-sol-feat    { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #334155; }
.vp-sol-cta-btn {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 20px 40px;
    background: #0f172a; color: white;
    border: none; border-radius: 1rem;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .15em;
    cursor: pointer; transition: background .2s;
}
.vp-sol-cta-btn:hover { background: var(--vp-blue); }
.vp-sol-cta-arrow { transition: transform .2s; }
.vp-sol-cta-btn:hover .vp-sol-cta-arrow { transform: translateX(5px); }

/* ── FINAL CTA ── */
.vp-final-cta { background: var(--vp-blue); padding: 96px 0; position: relative; overflow: hidden; }
.vp-final-dots { position: absolute; inset: 0; opacity: .08; background-image: radial-gradient(white 1px, transparent 1px); background-size: 30px 30px; pointer-events: none; }
.vp-final-content { text-align: center; position: relative; }
.vp-final-eyebrow { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .5em; color: var(--vp-yellow); margin-bottom: 16px; }
.vp-final-h2  { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.04em; color: white; margin-bottom: 20px; }
.vp-final-sub { font-size: 1.05rem; color: rgba(255,255,255,.7); margin-bottom: 48px; }
.vp-final-btns { display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; }
.vp-btn-white {
    padding: 18px 44px; background: white; color: var(--vp-blue);
    border: none; border-radius: 9999px;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em;
    cursor: pointer; display: flex; align-items: center; gap: 10px;
    transition: transform .1s, box-shadow .2s; text-decoration: none;
}
.vp-btn-white:hover { box-shadow: 0 20px 40px rgba(0,0,0,.25); transform: translateY(-2px); color: var(--vp-blue); }
.vp-btn-outline {
    padding: 18px 44px; background: transparent; color: white;
    border: 2px solid rgba(255,255,255,.4); border-radius: 9999px;
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em;
    cursor: pointer; display: flex; align-items: center; gap: 10px;
    transition: border-color .2s, background .2s; text-decoration: none;
}
.vp-btn-outline:hover { border-color: white; background: rgba(255,255,255,.1); color: white; }

/* ============================================================
   VENTE FORM MODAL
============================================================ */
.vp-modal-overlay {
    position: fixed; inset: 0; z-index: 1200;
    background: rgba(15,23,42,.85); backdrop-filter: blur(12px);
    display: none; align-items: flex-start; justify-content: center;
    padding: 24px 16px; overflow-y: auto;
}
.vp-modal-overlay.open { display: flex; }
.vp-modal-box {
    background: white; border-radius: 2.5rem;
    width: 100%; max-width: 900px; margin: auto;
    position: relative;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.4);
    overflow: hidden;
}
.vp-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 32px 48px 24px;
    background: linear-gradient(135deg, #f0f7ff, white);
    border-bottom: 1px solid var(--vp-border);
}
.vp-modal-title { font-size: 1.3rem; font-weight: 900; text-transform: uppercase; letter-spacing: -.02em; }
.vp-modal-close {
    width: 40px; height: 40px; border-radius: 50%;
    background: var(--vp-slate50); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--vp-muted); transition: background .2s, color .2s; flex-shrink: 0;
}
.vp-modal-close:hover { background: #fee2e2; color: #dc2626; }
.vp-modal-body { padding: 40px 48px 48px; }

/* Stepper */
.vm-stepper { display: flex; align-items: center; justify-content: space-between; max-width: 520px; margin: 0 auto 44px; position: relative; }
.vm-step-line { position: absolute; top: 20px; left: 10%; right: 10%; height: 1px; background: var(--vp-border); z-index: 1; }
.vm-step-prog { position: absolute; top: 20px; left: 10%; width: 0; height: 2px; background: var(--vp-blue); z-index: 2; transition: width .5s ease; }
.vm-step-node { display: flex; flex-direction: column; align-items: center; gap: 6px; z-index: 10; flex: 1; }
.vm-step-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid var(--vp-border); color: #cbd5e1; transition: .4s; }
.vm-step-label  { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #cbd5e1; letter-spacing: 1px; }
.vm-step-node.active .vm-step-circle { background: var(--vp-blue); color: white; border-color: var(--vp-blue); box-shadow: 0 8px 16px rgba(20,89,221,.3); }
.vm-step-node.active .vm-step-label  { color: var(--vp-slate); }

/* Form content */
.vm-step-content { display: none; animation: vmSlide .4s ease; }
.vm-step-content.active { display: block; }
@keyframes vmSlide { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
.vm-step-h2 { font-size: 18px; font-weight: 900; text-transform: uppercase; margin-bottom: 28px; text-align: left; }
.vm-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
.vm-img-grid  { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px; }
@media (max-width: 900px) { .vm-img-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .vm-img-grid { grid-template-columns: 1fr 1fr; } }

.vm-form-group { display: flex; flex-direction: column; gap: 6px; text-align: left; }
.vm-form-group label { font-size: 13px; font-weight: 900; color: var(--vp-slate); text-transform: uppercase; letter-spacing: 1.2px; margin-left: 14px; }
.vm-input {
    background: var(--vp-slate50); border: 2px solid transparent;
    border-radius: 16px; padding: 14px 20px;
    font-weight: 650; font-size: 13px; outline: none; width: 100%;
    font-family: 'Inter', sans-serif; color: var(--vp-slate);
    transition: border-color .2s, background .2s;
}
.vm-input:focus { border-color: var(--vp-blue); background: white; }
.vm-input.error { border-color: #ef4444; }

.vm-upload-box {
    height: 120px; background: var(--vp-slate50);
    border: 2px dashed var(--vp-border); border-radius: 24px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 8px; cursor: pointer; color: var(--vp-muted); transition: all .2s;
}
.vm-upload-box:hover { background: #eff6ff; border-color: rgba(20,89,221,.3); color: var(--vp-blue); }
.vm-upload-box.has-file { border-color: #22c55e; background: #f0fdf4; color: #22c55e; }
.vm-upload-text { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; }

/* Packs */
.vm-packs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin: 32px 0; }
.vm-pack-card { border: 2px solid var(--vp-border); border-radius: 2.5rem; padding: 32px; text-align: left; position: relative; cursor: pointer; transition: .3s; display: flex; flex-direction: column; }
.vm-pack-card.selected { border-color: var(--vp-blue); background: #f0f7ff; transform: scale(1.02); box-shadow: 0 20px 40px rgba(20,89,221,.1); }
.vm-pack-recommended { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--vp-blue); color: white; padding: 4px 16px; border-radius: 20px; font-size: 9px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
.vm-pack-title { font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 8px; }
.vm-pack-price { font-size: 28px; font-weight: 900; margin-bottom: 20px; }
.vm-pack-price span { font-size: 12px; color: var(--vp-muted); }
.vm-pack-feats { list-style: none; padding: 0; margin: 0 0 24px; flex-grow: 1; }
.vm-pack-feat  { display: flex; gap: 8px; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 10px; }
.vm-pack-feat-icon { color: #22c55e; flex-shrink: 0; }
.vm-select-btn { padding: 12px 20px; border-radius: 14px; border: none; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; cursor: pointer; width: 100%; transition: .2s; }
.vm-pack-card.selected .vm-select-btn     { background: var(--vp-blue); color: white; }
.vm-pack-card:not(.selected) .vm-select-btn { background: var(--vp-slate); color: white; }

/* Appointment box */
.vm-appt-box { margin-top: 40px; padding: 32px; background: var(--vp-slate50); border-radius: 2rem; text-align: left; display: none; }
.vm-appt-box.show { display: block; animation: vmSlide .3s ease; }
.vm-appt-h3 { font-size: 16px; font-weight: 900; text-transform: uppercase; margin-bottom: 20px; }

/* Nav buttons */
.vm-nav     { display: flex; align-items: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9; }
.vm-btn-prev { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; color: var(--vp-muted); background: none; border: none; cursor: pointer; display: none; }
.vm-btn-prev.show { display: block; }
.vm-btn-prev:hover { color: var(--vp-slate); }
.vm-spacer  { flex: 1; }
.vm-btn-next { display: flex; align-items: center; gap: 10px; padding: 16px 36px; background: var(--vp-blue); color: white; border: none; border-radius: 14px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; cursor: pointer; transition: background .2s; }
.vm-btn-next:hover { background: #0f46b1; }
.vm-btn-next.hidden { display: none; }

/* Mobile adjustments */
@media (max-width: 640px) {
    .vp-modal-header { padding: 20px 24px 16px; }
    .vp-modal-body   { padding: 24px 24px 32px; }
    .vm-pack-card    { padding: 24px; border-radius: 1.5rem; }
}

/* ── Free publish button ── */
.vm-free-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 32px;
    background: transparent;
    border: 2px solid var(--vp-border);
    border-radius: 9999px;
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .1em;
    color: var(--vp-muted); cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: border-color .2s, color .2s, background .2s;
}
.vm-free-btn:hover { border-color: var(--vp-blue); color: var(--vp-blue); background: #eff6ff; }

/* ── Confirmation page (full modal replacement) ── */
.vm-confirm-page {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 56px 48px 64px;
    min-height: 480px;
    background: linear-gradient(160deg, #f0fdf4 0%, white 55%);
    animation: vmFadeIn .4s ease;
}
.vm-confirm-page.show { display: flex !important; }
/* Center every child even if a theme stylesheet overrides the flex layout */
.vm-confirm-page > * { margin-left: auto !important; margin-right: auto !important; }
.vm-confirm-page > .vm-cp-title { width: 100%; }
@keyframes vmFadeIn { from { opacity:0; transform:scale(.97); } to { opacity:1; transform:scale(1); } }

/* Animated ring + check */
.vm-cp-icon-wrap {
    position: relative;
    width: 120px; height: 120px;
    margin-bottom: 32px;
}
.vm-cp-ring { width: 120px; height: 120px; transform: rotate(-90deg); }
.vm-cp-ring-fill {
    animation: vmRingDraw .7s ease forwards .15s;
}
@keyframes vmRingDraw {
    to { stroke-dashoffset: 0; }
}
.vm-cp-check {
    position: absolute;
    inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: #22c55e;
    border-radius: 50%;
    width: 76px; height: 76px;
    margin: auto;
    top: 0; bottom: 0; left: 0; right: 0;
    box-shadow: 0 16px 40px rgba(34,197,94,.3);
    animation: vmCheckPop .35s cubic-bezier(.34,1.56,.64,1) forwards .5s;
    transform: scale(0);
}
@keyframes vmCheckPop { to { transform:scale(1); } }

.vm-cp-title {
    font-size: clamp(1.8rem, 5vw, 2.8rem);
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -.04em;
    color: var(--vp-slate);
    margin-bottom: 16px;
    line-height: 1;
}
.vm-cp-pack-badge {
    display: flex; width: fit-content; align-items: center; gap: 8px;
    padding: 8px 20px;
    background: #dbeafe; color: var(--vp-blue);
    border-radius: 9999px;
    font-size: 12px; font-weight: 900;
    text-transform: uppercase; letter-spacing: .12em;
    margin-bottom: 24px;
    justify-content: center;
    text-align: center;
    max-width: 100%;
}
.vm-cp-sub {
    font-size: 15px; color: var(--vp-muted);
    font-weight: 500; line-height: 1.7;
    max-width: 380px; margin-bottom: 40px;
}
.vm-cp-actions {
    display: flex; flex-direction: column; align-items: center; gap: 14px; width: 100%;
    max-width: 360px;
    box-sizing: border-box;
}
.vm-cp-submit { width: 100%; justify-content: center; border-radius: 14px; }
.vm-cp-cancel {
    background: none; border: none; cursor: pointer;
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .1em;
    color: var(--vp-muted);
    font-family: 'Inter', sans-serif;
    transition: color .2s;
}
.vm-cp-cancel:hover { color: var(--vp-slate); }
</style>
@endsection

@section('content')
{{-- ?embed=1 (client dashboard popup): render only the modal, no marketing page --}}
@unless(request()->boolean('embed'))
<div class="vente-mkt">

    {{-- ════════ BREADCRUMB ════════ --}}
    <div class="vp-breadcrumb">
        <div class="vmkt-wrap">
            <nav>
                <a href="{{ route('index') }}">Accueil</a>
                <i data-lucide="chevron-right" style="width:10px;height:10px;"></i>
                <span class="bc-cur">Vendre ma voiture</span>
            </nav>
        </div>
    </div>

    {{-- ════════ Vérification situation légale (NARSA) — flux guidé ════════ --}}
    @if(feature_on('legalcheck.enabled'))
    <div class="vmkt-wrap" style="margin-top:18px;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;justify-content:space-between;
                    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:18px 22px;">
            <div style="display:flex;align-items:center;gap:14px;min-width:240px;">
                <i class="fa-solid fa-shield-halved" style="font-size:1.6rem;color:#16a34a;"></i>
                <div>
                    <div style="font-weight:900;color:#0f172a;">{{ __('messages.legalcheck.cta_title') }}</div>
                    <div style="color:#15803d;font-size:.92rem;">{{ __('messages.legalcheck.cta_text') }}</div>
                </div>
            </div>
            <a href="{{ route('legalcheck.form') }}"
               style="background:#16a34a;color:#fff;font-weight:800;text-decoration:none;
                      padding:.7rem 1.2rem;border-radius:10px;white-space:nowrap;">
                {{ __('messages.legalcheck.cta_button') }}
            </a>
        </div>
    </div>
    @endif

    {{-- ════════ HERO ════════ --}}
    <section class="vp-hero">
        <div class="vp-hero-dots"></div>
        <div class="vmkt-wrap" style="width:100%;">
            <div class="vp-hero-grid">

                {{-- Left: text --}}
                <div>
                    <div class="vp-badge">
                        <i data-lucide="zap" style="width:14px;height:14px;fill:currentColor;"></i>
                        {{ __('messages.vente.badge') }}
                    </div>
                    <h1 class="vp-h1">
                        {{ __('messages.vente.h1_1') }}<br>
                        <span class="vp-h1-grad">{{ __('messages.vente.h1_grad') }}</span>
                    </h1>
                    <p class="vp-subtitle">
                        {{ __('messages.vente.subtitle') }}
                    </p>
                    <div class="vp-props">
                        <div class="vp-prop"><i data-lucide="check-circle" class="vp-prop-check" style="width:16px;height:16px;"></i> {{ __('messages.vente.prop_free') }}</div>
                        <div class="vp-prop"><i data-lucide="check-circle" class="vp-prop-check" style="width:16px;height:16px;"></i> {{ __('messages.vente.prop_fast') }}</div>
                        <div class="vp-prop"><i data-lucide="check-circle" class="vp-prop-check" style="width:16px;height:16px;"></i> {{ __('messages.vente.prop_secure') }}</div>
                    </div>
                    <button class="vp-cta-btn" onclick="openVenteModal()">
                        {{ __('messages.vente.publish') }}
                        <i data-lucide="arrow-right" class="vp-cta-arrow" style="width:18px;height:18px;"></i>
                    </button>
                </div>

                {{-- Right: visual (desktop only) --}}
                <div class="vp-hero-visual">
                    <div class="vp-visual-card">
                        <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=900&q=80"
                             alt="Voiture premium NADO"
                             style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s ease;"
                             onmouseover="this.style.transform='scale(1.04)'"
                             onmouseout="this.style.transform='scale(1)'">
                        <div class="vp-visual-overlay">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
                                <div>
                                    <div style="color:white;font-size:1.2rem;font-weight:900;margin-bottom:6px;">{{ __('messages.vente.badge') }}</div>
                                    <div style="color:var(--vp-yellow);font-size:1.6rem;font-weight:900;letter-spacing:-.02em;">
                                        {{ __('messages.vente.prop_free') }}
                                    </div>
                                </div>
                                <div style="width:64px;height:64px;background:rgba(255,255,255,.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.2);border-radius:1rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i data-lucide="zap" style="width:32px;height:32px;color:white;fill:white;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ════════ BANNER 1 ════════ --}}
    @if (!request()->is('vente/formulaire*'))
        <div class="vmkt-wrap">
            <div class="vp-banner">
                <div class="vp-banner-inner"><p>📢</p>{{ __('messages.ventemodal.banner') }}</div>
            </div>
        </div>
    @endif

    {{-- ════════ CLIENT TYPES (3 cards + banner) ════════ --}}
    <section class="vp-clients">
        <div class="vmkt-wrap">
            <div style="text-align:center; margin-bottom:64px;">
                <p class="vp-section-eyebrow">{{ __('messages.vente.eyebrow_all') }}</p>
                <h2 class="vp-section-title">{!! __('messages.vente.profile_title') !!}</h2>
                <p class="vp-section-sub">{{ __('messages.vente.profile_sub') }}</p>
            </div>

            <div class="vp-clients-grid">

                {{-- 1. Particulier --}}
                <div class="vp-client-card" onclick="window.location='{{ route('vente.particulier') }}'" style="cursor:pointer;">
                    <div class="vp-card-media">
                        <div class="vp-card-media-bg" style="background:none;padding:0;display:block;height:100%;">
                            <img src="https://img.freepik.com/photos-gratuite/homme-choisissant-voiture-dans-salon-voitures_1303-25646.jpg?t=st=1771689579~exp=1771693179~hmac=27d8b3064b0ea231c0361774f5e7d814eb92f885efea1173cd7020cef385ca73&w=1480" alt="Particulier dans un salon automobile" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="vp-card-hover-overlay">
                            <div class="vp-hover-pill">{{ __('messages.vente.see_journey') }}</div>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <span class="vp-card-badge badge-particulier">
                            <i data-lucide="user" style="width:10px;height:10px;"></i> {{ __('messages.vente.badge_particulier') }}
                        </span>
                        <h3 class="vp-card-title">{{ __('messages.vente.card1_title') }}</h3>
                        <p class="vp-card-desc">{{ __('messages.vente.card1_desc') }}</p>
                    </div>
                </div>

                {{-- 2. Professionnel --}}
                <div class="vp-client-card" onclick="window.location='{{ route('vente.pro') }}'" style="cursor:pointer;">
                    <div class="vp-card-media">
                        <div class="vp-card-media-bg" style="background:none;padding:0;display:block;height:100%;">
                            <img src="https://img.freepik.com/photos-gratuite/personne-agee-voiture-salle-exposition-choisir-voiture_1303-18966.jpg?t=st=1771689722~exp=1771693322~hmac=57a87929be4fbc40ee9588c4f3caf6c8ba6aab3d3945ffc38cb26a5621a6de25&w=1480" alt="Professionnel en salle d'exposition" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="vp-card-hover-overlay">
                            <div class="vp-hover-pill">{{ __('messages.vente.see_journey') }}</div>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <span class="vp-card-badge badge-certifie">
                            <i data-lucide="shield-check" style="width:10px;height:10px;"></i> {{ __('messages.vente.badge_certified') }}
                        </span>
                        <h3 class="vp-card-title">{{ __('messages.vente.card2_title') }}</h3>
                        <p class="vp-card-desc">{{ __('messages.vente.card2_desc') }}</p>
                    </div>
                </div>

                {{-- 3. Reprise --}}
                <div class="vp-client-card" onclick="window.location='{{ route('reprise.form') }}'">
                    <div class="vp-card-media">
                        <div class="vp-card-media-bg" style="background:none;padding:0;display:block;height:100%;">
                            <img src="https://img.freepik.com/photos-gratuite/gros-plan-du-directeur-ventes-costume-noir-vendant-voiture-au-client_146671-14738.jpg?uid=R175071393&ga=GA1.1.1242906354.1770722826&semt=ais_related_payload_trends&w=740&q=80" alt="Conseiller commercial avec un client" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="vp-card-hover-overlay">
                            <div class="vp-hover-pill">{{ __('messages.vente.ask_tradein') }}</div>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <span class="vp-card-badge badge-nado">
                            <i data-lucide="refresh-cw" style="width:10px;height:10px;"></i> {{ __('messages.vente.badge_nado') }}
                        </span>
                        <h3 class="vp-card-title">{{ __('messages.vente.card3_title') }}</h3>
                        <p class="vp-card-desc">{{ __('messages.vente.card3_desc') }}</p>
                    </div>
                </div>

            </div>

            {{-- Banner after 3 cards --}}
            @if (!request()->is('vente/formulaire*'))
                <div class="vp-banner" style="margin-top:56px;">
                    <div class="vp-banner-inner"><p>📣</p>{{ __('messages.ventemodal.banner') }}</div>
                </div>
            @endif
        </div>
    </section>

    {{-- ════════ HOW IT WORKS ════════ --}}
    <section class="vp-how">
        <div class="vmkt-wrap">
            <div style="text-align:center; margin-bottom:80px;">
                <p class="vp-section-eyebrow">{{ __('messages.vente.eyebrow_mode') }}</p>
                <h2 class="vp-section-title">{!! __('messages.vente.how_title') !!}</h2>
                <p class="vp-section-sub">{{ __('messages.vente.how_sub') }}</p>
            </div>
            <div class="vp-how-grid">

                {{-- Canal 1: À distance --}}
                <div class="vp-how-card" style="background:linear-gradient(160deg,#eff6ff 0%,white 100%);border-color:#bfdbfe;">
                    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:28px;">
                        <div class="vp-how-canal-num">01</div>
                        <div>
                            <h3 class="vp-how-card-title" style="margin-bottom:6px;">{{ __('messages.vente.canal1_title') }}</h3>
                            <p style="font-size:13px;color:var(--vp-muted);font-weight:500;">{{ __('messages.vente.canal1_sub') }}</p>
                        </div>
                    </div>
                    <div class="vp-how-card-media" style="overflow:hidden;">
                        <img src="https://img.freepik.com/photos-gratuite/portrait-vendeur-dans-concessionnaire-voitures_23-2148130107.jpg?w=1480"
                             alt="Service à distance NADO" loading="lazy"
                             style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s;">
                    </div>
                    <ul class="vp-how-check-list">
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal1_f1') }}
                        </li>
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal1_f2') }}
                        </li>
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal1_f3') }}
                        </li>
                    </ul>
                    <button class="vp-how-cta-btn" onclick="openVenteModal()">
                        <i data-lucide="globe" style="width:16px;height:16px;"></i>
                        {{ __('messages.vente.canal1_cta') }}
                    </button>
                </div>

                {{-- Canal 2: Venir au Showroom --}}
                <div class="vp-how-card" style="background:linear-gradient(160deg,#f8fafc 0%,white 100%);">
                    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:28px;">
                        <div class="vp-how-canal-num" style="background:#0f172a;">02</div>
                        <div>
                            <h3 class="vp-how-card-title" style="margin-bottom:6px;">{{ __('messages.vente.canal2_title') }}</h3>
                            <p style="font-size:13px;color:var(--vp-muted);font-weight:500;">{{ __('messages.vente.canal2_sub') }}</p>
                        </div>
                    </div>
                    <div class="vp-how-card-media" style="overflow:hidden;">
                        <img src="https://img.freepik.com/photos-gratuite/couple-phares-voiture-faisant-affaire-concessionnaire-automobile-derriere_651396-1187.jpg?t=st=1771690179~exp=1771693779~hmac=c23a1102c38f7373c3c86a5a6d1ae8053f456eaf99876c9dfe57964b4ce567ac&w=1480"
                             alt="Showroom NADO" loading="lazy"
                             style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .7s;">
                    </div>
                    <ul class="vp-how-check-list">
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal2_f1') }}
                        </li>
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal2_f2') }}
                        </li>
                        <li class="vp-how-check-item">
                            <i data-lucide="check-circle-2" class="vp-how-check-icon" style="width:18px;height:18px;"></i>
                            {{ __('messages.vente.canal2_f3') }}
                        </li>
                    </ul>
                    <div class="vp-address-card">
                        <i data-lucide="map-pin" class="vp-address-pin" style="width:20px;height:20px;"></i>
                        <div>
                            <div style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--vp-slate);margin-bottom:4px;">{{ __('messages.vente.showroom_label') }}</div>
                            <div style="font-size:13px;font-weight:500;color:var(--vp-muted);">{{ __('messages.vente.showroom_addr') }}</div>
                        </div>
                    </div>
                    <button class="vp-how-cta-btn" style="background:#0f172a;" onmouseover="this.style.background='#1459DD'" onmouseout="this.style.background='#0f172a'" onclick="openVenteModal()">
                        <i data-lucide="map-pin" style="width:16px;height:16px;"></i>
                        {{ __('messages.vente.canal2_cta') }}
                    </button>
                </div>

            </div>
        </div>
    </section>

    {{-- ════════ LES PROPOSITIONS NADO ════════ --}}
    <section class="vp-solutions">
        <div class="vmkt-wrap">
            <div style="text-align:center; margin-bottom:80px;">
                <p class="vp-section-eyebrow">{{ __('messages.vente.eyebrow_solutions') }}</p>
                <h2 class="vp-section-title">{!! __('messages.vente.propositions_title') !!}</h2>
                <p class="vp-section-sub">{{ __('messages.vente.propositions_sub') }}</p>
            </div>
            <div class="vp-solutions-list">

                {{-- 1. Achat Direct (blue) --}}
                <div class="vp-solution-item">
                    <div class="vp-sol-media">
                        <img src="https://img.freepik.com/photos-gratuite/gros-plan-client-homme-affaires-chez-concessionnaire-automobile_23-2149117137.jpg?t=st=1771691293~exp=1771694893~hmac=0a859f7b4f543627103588a1670b426206883df89b96be592b1316a616d672ec&w=1480"
                             alt="Achat direct NADO" loading="lazy">
                        <span class="vp-sol-float-badge" style="background:#1459DD;">{{ __('messages.vente.sol1_title') }}</span>
                    </div>
                    <div class="vp-sol-body">
                        <p class="vp-sol-eyebrow">{{ __('messages.vente.solution') }} 01</p>
                        <h3 class="vp-sol-title">{{ __('messages.vente.sol1_title') }}</h3>
                        <p class="vp-sol-desc">{{ __('messages.vente.sol1_desc') }}</p>
                        <ul class="vp-sol-feats">
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#1459DD;flex-shrink:0;"></i> {{ __('messages.vente.sol1_f1') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#1459DD;flex-shrink:0;"></i> {{ __('messages.vente.sol1_f2') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#1459DD;flex-shrink:0;"></i> {{ __('messages.vente.sol1_f3') }}</li>
                        </ul>
                        <button class="vp-sol-cta-btn" onclick="openVenteModal()">
                            {{ __('messages.vente.sol1_cta') }} <i data-lucide="arrow-right" class="vp-sol-cta-arrow" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                </div>

                {{-- 2. Reprise (yellow) — image right --}}
                <div class="vp-solution-item">
                    <div class="vp-sol-body" style="order:1;">
                        <p class="vp-sol-eyebrow">{{ __('messages.vente.solution') }} 02</p>
                        <h3 class="vp-sol-title">{{ __('messages.vente.sol2_title') }}</h3>
                        <p class="vp-sol-desc">{{ __('messages.vente.sol2_desc') }}</p>
                        <ul class="vp-sol-feats">
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#d97706;flex-shrink:0;"></i> {{ __('messages.vente.sol2_f1') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#d97706;flex-shrink:0;"></i> {{ __('messages.vente.sol2_f2') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#d97706;flex-shrink:0;"></i> {{ __('messages.vente.sol2_f3') }}</li>
                        </ul>
                        <button class="vp-sol-cta-btn" onclick="openVenteModal()">
                            {{ __('messages.vente.sol2_cta') }} <i data-lucide="arrow-right" class="vp-sol-cta-arrow" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                    <div class="vp-sol-media" style="order:2;">
                        <img src="https://img.freepik.com/photos-gratuite/jeune-couple-choisissant-voiture-dans-exposition-voitures_1303-22835.jpg?t=st=1771692792~exp=1771696392~hmac=68ad7274e0d7d0d7ab054019b6ab1bb64b4fcb543ca0a99200c4ea86f9da1750&w=1060"
                             alt="Reprise NADO" loading="lazy">
                        <span class="vp-sol-float-badge badge-yellow" style="background:#FDD817;">{{ __('messages.vente.sol2_badge') }}</span>
                    </div>
                </div>

                {{-- 3. Dépôt-vente (green) --}}
                <div class="vp-solution-item">
                    <div class="vp-sol-media">
                        <img src="https://img.freepik.com/photos-gratuite/homme-femme-chez-concessionnaire-automobile_23-2148333017.jpg?t=st=1771691462~exp=1771695062~hmac=805c99a2054d96155f61b9bbc07fadd8f76de7108b81afac3337021021d084a7&w=1480"
                             alt="Dépôt-vente NADO" loading="lazy">
                        <span class="vp-sol-float-badge" style="background:#16a34a;">{{ __('messages.vente.sol3_title') }}</span>
                    </div>
                    <div class="vp-sol-body">
                        <p class="vp-sol-eyebrow">{{ __('messages.vente.solution') }} 03</p>
                        <h3 class="vp-sol-title">{{ __('messages.vente.sol3_title') }}</h3>
                        <p class="vp-sol-desc">{{ __('messages.vente.sol3_desc') }}</p>
                        <ul class="vp-sol-feats">
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#16a34a;flex-shrink:0;"></i> {{ __('messages.vente.sol3_f1') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#16a34a;flex-shrink:0;"></i> {{ __('messages.vente.sol3_f2') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#16a34a;flex-shrink:0;"></i> {{ __('messages.vente.sol3_f3') }}</li>
                        </ul>
                        <button class="vp-sol-cta-btn" onclick="openVenteModal()">
                            {{ __('messages.vente.sol3_cta') }} <i data-lucide="arrow-right" class="vp-sol-cta-arrow" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                </div>

                {{-- 4. Pack Annonces (purple) — image right --}}
                <div class="vp-solution-item">
                    <div class="vp-sol-body" style="order:1;">
                        <p class="vp-sol-eyebrow">{{ __('messages.vente.solution') }} 04</p>
                        <h3 class="vp-sol-title">{{ __('messages.vente.sol4_title') }}</h3>
                        <p class="vp-sol-desc">{{ __('messages.vente.sol4_desc') }}</p>
                        <ul class="vp-sol-feats">
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#7c3aed;flex-shrink:0;"></i> {{ __('messages.vente.sol4_f1') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#7c3aed;flex-shrink:0;"></i> {{ __('messages.vente.sol4_f2') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#7c3aed;flex-shrink:0;"></i> {{ __('messages.vente.sol4_f3') }}</li>
                        </ul>
                        <button class="vp-sol-cta-btn" onclick="openVenteModal()">
                            {{ __('messages.vente.sol4_cta') }} <i data-lucide="arrow-right" class="vp-sol-cta-arrow" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                    <div class="vp-sol-media" style="order:2;">
                        <img src="https://img.freepik.com/photos-gratuite/portrait-homme-gai-dans-vetements-base-souriant-montrant-pouces-vers-haut-camera-isole-fond-blanc_176420-45185.jpg?t=st=1771692121~exp=1771695721~hmac=ce97cd426b2a8c1de8802d203788c9db487f8bb33bee097d9870d3d36b8f53f2&w=1480"
                             alt="Pack annonces NADO" loading="lazy">
                        <span class="vp-sol-float-badge" style="background:#7c3aed;">{{ __('messages.vente.sol4_title') }}</span>
                    </div>
                </div>

                {{-- 5. Enchère Auto (orange) --}}
                <div class="vp-solution-item">
                    <div class="vp-sol-media">
                        <img src="https://img.freepik.com/photos-gratuite/vue-aerienne-clientele-feminine-homme-affaires-barbu-elegant-moderne-dans-salon-automobile_146671-16021.jpg?t=st=1771691824~exp=1771695424~hmac=a90a4858804a5138b7905f11697e2bea18a966fbc564872b76f90d625522ceb2&w=1480"
                             alt="Enchère auto NADO" loading="lazy">
                        <span class="vp-sol-float-badge" style="background:#ea580c;">{{ __('messages.vente.sol5_title') }}</span>
                    </div>
                    <div class="vp-sol-body">
                        <p class="vp-sol-eyebrow">{{ __('messages.vente.solution') }} 05</p>
                        <h3 class="vp-sol-title">{{ __('messages.vente.sol5_title') }}</h3>
                        <p class="vp-sol-desc">{{ __('messages.vente.sol5_desc') }}</p>
                        <ul class="vp-sol-feats">
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#ea580c;flex-shrink:0;"></i> {{ __('messages.vente.sol5_f1') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#ea580c;flex-shrink:0;"></i> {{ __('messages.vente.sol5_f2') }}</li>
                            <li class="vp-sol-feat"><i data-lucide="check-circle-2" style="width:18px;height:18px;color:#ea580c;flex-shrink:0;"></i> {{ __('messages.vente.sol5_f3') }}</li>
                        </ul>
                        <button class="vp-sol-cta-btn" onclick="openVenteModal()">
                            {{ __('messages.vente.sol5_cta') }} <i data-lucide="arrow-right" class="vp-sol-cta-arrow" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Banner --}}
            @if (!request()->is('vente/formulaire*'))
                <div class="vp-banner" style="margin-top:80px;">
                    <div class="vp-banner-inner"><p>📣</p>{{ __('messages.ventemodal.banner') }}</div>
                </div>
            @endif
        </div>
    </section>

    {{-- ════════ FINAL CTA ════════ --}}
    <section class="vp-final-cta">
        <div class="vp-final-dots"></div>
        <div class="vmkt-wrap">
            <div class="vp-final-content" style="max-width:700px;margin:0 auto;">
                <p class="vp-final-eyebrow">{{ __('messages.vente.final_eyebrow') }}</p>
                <h2 class="vp-final-h2">{!! __('messages.vente.final_title') !!}</h2>
                <p class="vp-final-sub">{{ __('messages.vente.final_sub') }}</p>
                <div class="vp-final-btns">
                    <button class="vp-btn-white" onclick="openVenteModal()">
                        <i data-lucide="plus-circle" style="width:18px;height:18px;"></i>
                        {{ __('messages.vente.publish') }}
                    </button>
                    <a href="{{ route('reprise.form') }}" class="vp-btn-outline">
                        <i data-lucide="refresh-cw" style="width:18px;height:18px;"></i>
                        {{ __('messages.vente.card3_title') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>{{-- .vente-mkt --}}
@endunless

{{-- Extra CSS for drop zones & photo previews --}}
<style>
.vm-section-label {
    font-size: 11px; font-weight: 900; text-transform: uppercase;
    letter-spacing: .15em; color: var(--vp-muted);
    margin: 28px 0 12px; display: flex; align-items: center; gap: 8px;
}
.vm-section-label::after {
    content: ''; flex: 1; height: 1px; background: var(--vp-border);
}
/* Drop zone */
.vm-dropzone {
    border: 2px dashed var(--vp-border);
    border-radius: 20px;
    padding: 36px 24px;
    text-align: center;
    cursor: pointer;
    background: var(--vp-slate50);
    transition: border-color .2s, background .2s, color .2s;
    color: var(--vp-muted);
    position: relative;
}
.vm-dropzone:hover,
.vm-dropzone.drag-over {
    border-color: var(--vp-blue);
    background: #eff6ff;
    color: var(--vp-blue);
}
.vm-dropzone-icon  { font-size: 2rem; margin-bottom: 10px; line-height: 1; }
.vm-dropzone-title { font-size: 14px; font-weight: 800; margin-bottom: 4px; }
.vm-dropzone-sub   { font-size: 11px; font-weight: 600; opacity: .7; }
.vm-dropzone-count {
    display: inline-block; margin-top: 12px;
    padding: 4px 12px; border-radius: 9999px;
    background: rgba(20,89,221,.08); color: var(--vp-blue);
    font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em;
}
/* Photo preview grid */
.vm-photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 8px;
    margin-top: 14px;
}
.vm-photo-thumb {
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--vp-border);
    position: relative;
    background: var(--vp-slate50);
}
.vm-photo-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.vm-photo-remove {
    position: absolute; top: 3px; right: 3px;
    width: 20px; height: 20px; border-radius: 50%;
    background: rgba(15,23,42,.75); color: white;
    border: none; cursor: pointer;
    font-size: 13px; font-weight: 900; line-height: 1;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.vm-photo-remove:hover { background: #dc2626; }
/* Video drop zone variant */
.vm-dropzone-video { border-radius: 20px; }
.vm-video-preview {
    display: flex; align-items: center; gap: 14px;
    margin-top: 12px; padding: 12px 16px;
    background: white; border: 1px solid var(--vp-border);
    border-radius: 14px;
}
.vm-video-preview-icon { font-size: 1.5rem; flex-shrink: 0; }
.vm-video-preview-name { font-size: 13px; font-weight: 700; color: var(--vp-slate); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.vm-video-remove { background: none; border: none; cursor: pointer; color: var(--vp-muted); font-size: 16px; padding: 0 4px; }
.vm-video-remove:hover { color: #dc2626; }
</style>

{{-- ════════════════════════════════════════════════
     VENTE FORM MODAL  (3 steps)
═══════════════════════════════════════════════════ --}}
<div id="venteModal" class="vp-modal-overlay" onclick="handleModalBackdrop(event)">
    <div class="vp-modal-box">

        <div class="vp-modal-header">
            <div>
                <div class="vp-badge" style="margin-bottom:8px;box-shadow:none;">
                    <i data-lucide="zap" style="width:12px;height:12px;fill:currentColor;"></i>
                    {{ __('messages.ventemodal.badge_price') }}
                </div>
                <div class="vp-modal-title">{{ __('messages.ventemodal.title') }}</div>
            </div>
            <button class="vp-modal-close" onclick="closeVenteModal()" aria-label="{{ __('messages.ventemodal.close') }}">
                <i data-lucide="x" style="width:18px;height:18px;"></i>
            </button>
        </div>

        <div class="vp-modal-body" id="vm-main-body">

            {{-- ── 3-step stepper ── --}}
            <div class="vm-stepper">
                <div class="vm-step-line"></div>
                <div id="vm-progress" class="vm-step-prog"></div>

                <div class="vm-step-node active" data-step="1">
                    <div class="vm-step-circle"><i data-lucide="car-front" style="width:16px;height:16px;"></i></div>
                    <span class="vm-step-label">{{ __('messages.ventemodal.step_vehicle') }}</span>
                </div>
                <div class="vm-step-node" data-step="2">
                    <div class="vm-step-circle"><i data-lucide="user" style="width:16px;height:16px;"></i></div>
                    <span class="vm-step-label">{{ __('messages.ventemodal.step_contact') }}</span>
                </div>
                <div class="vm-step-node" data-step="3">
                    <div class="vm-step-circle"><i data-lucide="package" style="width:16px;height:16px;"></i></div>
                    <span class="vm-step-label">{{ __('messages.ventemodal.step_offer') }}</span>
                </div>
            </div>

            <form action="{{ route('vente.store') }}" method="POST" enctype="multipart/form-data" id="vm-form">
                @csrf
                <input type="hidden" name="jeveux" value="vendre ma voiture">
                <input type="hidden" name="mode" id="vm-mode" value="">
                {{-- Carte grise (pré-remplissage IA) — champs véhicule uniquement, jamais publiés tels quels --}}
                <input type="hidden" name="matricule" id="vm-matricule" value="{{ $prefill['matricule'] ?? '' }}">
                <input type="hidden" name="vin" id="vm-vin" value="">
                @isset($prefill)
                    <input type="hidden" name="annonce_id" value="{{ $prefill['annonce_id'] ?? '' }}">
                @endisset
                {{-- keep_photos[]: current photos the client keeps when editing (filled by JS) --}}
                <div id="vm-keep-photos"></div>

                {{-- ══════════════════════════════════════
                     STEP 1 — Véhicule & Détails
                ═════════════════════════════════════════ --}}
                <div id="vm-step-1" class="vm-step-content active">

                    @auth('client')
                    {{-- ══════════ Remplir avec la carte grise (OCR) — recto + verso ══════════ --}}
                    <div class="cg-box" id="cg-box">
                        <div class="cg-head">
                            <span class="cg-badge"><i data-lucide="sparkles" style="width:14px;height:14px;"></i> {{ __('messages.ai.title') }}</span>
                        </div>
                        <p class="cg-sub">{{ __('messages.ai.subtitle') }}</p>

                        <div class="cg-slots">
                            {{-- label + for = ouverture native du sélecteur (Prendre une photo / Photothèque),
                                 plus fiable que input caché + click() JS sur mobile. --}}
                            <label class="cg-slot" id="cg-slot-recto" for="cg-recto">
                                <i data-lucide="credit-card" class="cg-slot-ic"></i>
                                <div class="cg-slot-title">{{ __('messages.ai.recto') }}</div>
                                <div class="cg-slot-sub" id="cg-recto-name">{{ __('messages.ai.recto_hint') }}</div>
                            </label>
                            <input type="file" id="cg-recto" accept="image/*,application/pdf" class="cg-file-input">

                            <label class="cg-slot" id="cg-slot-verso" for="cg-verso">
                                <i data-lucide="credit-card" class="cg-slot-ic" style="transform:scaleX(-1);"></i>
                                <div class="cg-slot-title">{{ __('messages.ai.verso') }}</div>
                                <div class="cg-slot-sub" id="cg-verso-name">{{ __('messages.ai.verso_hint') }}</div>
                            </label>
                            <input type="file" id="cg-verso" accept="image/*,application/pdf" class="cg-file-input">
                        </div>

                        <button type="button" class="cg-analyze" id="cg-analyze" disabled>
                            <i data-lucide="scan-line" style="width:16px;height:16px;"></i> {{ __('messages.ai.analyze') }}
                        </button>

                        <div class="cg-status" id="cg-status" hidden></div>
                        <p class="cg-consent"><i data-lucide="shield-check" style="width:13px;height:13px;"></i> {{ __('messages.ai.consent') }}</p>
                    </div>
                    @endauth

                    <h2 class="vm-step-h2">{{ __('messages.ventemodal.s1_title') }}</h2>

                    {{-- Row 1: identité --}}
                    <div class="vm-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.marque') }}</label>
                            <select name="mark" id="vm-mark" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->name }}" data-id="{{ $brand->id }}"
                                        @isset($prefill) {{ ($prefill['mark'] ?? '') === $brand->name ? 'selected' : '' }} @endisset>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.modele') }}</label>
                            <select name="modele" id="vm-modele" class="vm-input" required>
                                @isset($prefill)
                                    <option value="{{ $prefill['modele'] ?? '' }}" selected>{{ $prefill['modele'] ?? '' }}</option>
                                @else
                                    <option value="">{{ __('messages.ventemodal.select_brand_first') }}</option>
                                @endisset
                            </select>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.year') }}</label>
                            <select name="year" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.year') }}</option>
                                @for ($i = date('Y'); $i >= 1992; $i--)
                                    <option value="{{ $i }}" @isset($prefill) {{ (int)($prefill['year'] ?? 0) === (int)$i ? 'selected' : '' }} @endisset>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.fuel') }}</label>
                            <select name="carburant" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                <option value="Essence" @isset($prefill) {{ ($prefill['carburant'] ?? '') === 'Essence' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.fuel_essence') }}</option>
                                <option value="Diesel" @isset($prefill) {{ ($prefill['carburant'] ?? '') === 'Diesel' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.fuel_diesel') }}</option>
                                <option value="Electrique" @isset($prefill) {{ ($prefill['carburant'] ?? '') === 'Electrique' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.fuel_electric') }}</option>
                                <option value="Hybride" @isset($prefill) {{ ($prefill['carburant'] ?? '') === 'Hybride' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.fuel_hybrid') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: km + boîte + CV --}}
                    <div class="vm-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.km') }}</label>
                            <input type="number" name="km" placeholder="{{ __('messages.ventemodal.km_ph') }}" class="vm-input" required min="0"
                                @isset($prefill) value="{{ $prefill['km'] ?? '' }}" @endisset>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.box') }}</label>
                            <select name="box" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                <option value="Manuelle" @isset($prefill) {{ ($prefill['box'] ?? '') === 'Manuelle' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.box_manual') }}</option>
                                <option value="Automatique" @isset($prefill) {{ ($prefill['box'] ?? '') === 'Automatique' ? 'selected' : '' }} @endisset>{{ __('messages.ventemodal.box_auto') }}</option>
                            </select>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.cv') }}</label>
                            <input type="number" name="cv" placeholder="{{ __('messages.ventemodal.cv_ph') }}" class="vm-input" min="0"
                                @isset($prefill) value="{{ $prefill['cv'] ?? '' }}" @endisset>
                        </div>
                    </div>

                    {{-- Row 3: état / origine --}}
                    <div class="vm-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.state') }}</label>
                            @php $etatLabels = [
                                'Excellent' => __('messages.ventemodal.state_excellent'),
                                'Très bon'  => __('messages.ventemodal.state_very_good'),
                                'Bon'       => __('messages.ventemodal.state_good'),
                                'Correct'   => __('messages.ventemodal.state_correct'),
                                'Endommagé' => __('messages.ventemodal.state_damaged'),
                                'Neuf'      => __('messages.ventemodal.state_new'),
                            ]; @endphp
                            <select name="etat" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                @foreach($etatLabels as $val => $label)
                                    <option value="{{ $val }}" @isset($prefill) {{ ($prefill['etat'] ?? '') === $val ? 'selected' : '' }} @endisset>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.origin') }}</label>
                            @php $origineLabels = [
                                'Dédouané'             => __('messages.ventemodal.origin_cleared'),
                                'WW au Maroc'          => __('messages.ventemodal.origin_ww'),
                                'Importé neuf'         => __('messages.ventemodal.origin_imported'),
                                'Pas encore dédouané'  => __('messages.ventemodal.origin_not_cleared'),
                            ]; @endphp
                            <select name="origine" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                @foreach($origineLabels as $val => $label)
                                    <option value="{{ $val }}" @isset($prefill) {{ ($prefill['origine'] ?? '') === $val ? 'selected' : '' }} @endisset>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 4: Prix (seul, centré) --}}
                    <div class="vm-form-grid" style="grid-template-columns: 1fr; margin-top: 8px;">
                        <div class="vm-form-group" style="max-width: 320px; margin: 0 auto; width: 100%; text-align: center;">
                            <label style="display:block; text-align:center;">{{ __('messages.ventemodal.price') }}</label>
                            <input type="number" name="prix" placeholder="{{ __('messages.ventemodal.price_ph') }}"
                                class="vm-input" min="0" style="text-align: center;"
                                @isset($prefill) value="{{ $prefill['prix'] ?? '' }}" @endisset>
                        </div>
                    </div>

                    {{-- Row 5: Description --}}
                    <div class="vm-form-grid" style="grid-template-columns: 1fr; margin-top: 16px;">
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.about_vehicle') }}</label>
                            <textarea name="description" rows="5"
                                class="vm-input"
                                placeholder="{{ __('messages.ventemodal.desc_ph') }}"
                                maxlength="5000"
                                style="resize: vertical; min-height: 140px; font-family: inherit; line-height: 1.5;">@isset($prefill){{ $prefill['description'] ?? '' }}@endisset</textarea>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                     STEP 2 — Coordonnées & Photos
                ═════════════════════════════════════════ --}}
                <div id="vm-step-2" class="vm-step-content">
                    <h2 class="vm-step-h2">{{ __('messages.ventemodal.s2_title') }}</h2>

                    {{-- Contact fields --}}
                    <div class="vm-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.fullname') }}</label>
                            <input type="text" name="name" placeholder="{{ __('messages.ventemodal.fullname_ph') }}" class="vm-input" required
                                value="{{ $prefill['name'] ?? (Auth::guard('client')->check() ? (Auth::guard('client')->user()->name ?? '') : '') }}">
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.phone') }}</label>
                            <input type="tel" name="phone" placeholder="{{ __('messages.ventemodal.phone_ph') }}" class="vm-input" required
                                value="{{ $prefill['phone'] ?? (Auth::guard('client')->check() ? (Auth::guard('client')->user()->phone ?? '') : '') }}">
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.email') }}</label>
                            <input type="email" name="email" placeholder="{{ __('messages.ventemodal.email_ph') }}" class="vm-input"
                                value="{{ $prefill['email'] ?? (Auth::guard('client')->check() ? (Auth::guard('client')->user()->email ?? '') : '') }}">
                        </div>
                        <div class="vm-form-group">
                            <label>{{ __('messages.ventemodal.city') }}</label>
                            <select name="city" class="vm-input" required>
                                <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                @foreach ($city as $c)
                                    <option value="{{ $c->name }}" @isset($prefill) {{ ($prefill['city'] ?? '') === $c->name ? 'selected' : '' }} @endisset>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Photos drop zone --}}
                    <p class="vm-section-label">
                        <i data-lucide="image" style="width:14px;height:14px;"></i>
                        {{ __('messages.ventemodal.photos_label') }}
                    </p>
                    <p style="font-size:12px;color:var(--vp-muted);font-weight:600;margin-bottom:12px;">
                        {{ __('messages.ventemodal.photos_hint') }}
                    </p>
                    <div class="vm-dropzone" id="vm-photo-zone" onclick="document.getElementById('vm-photos-input').click()">
                        <div class="vm-dropzone-icon">🖼️</div>
                        <div class="vm-dropzone-title">{{ __('messages.ventemodal.drop_click') }}</div>
                        <div class="vm-dropzone-sub">{{ __('messages.ventemodal.photo_formats') }}</div>
                        <span class="vm-dropzone-count" id="vm-photo-count">0 {{ __('messages.ventemodal.photos_suffix') }}</span>
                        <input type="file" id="vm-photos-input" name="photos[]"
                               multiple accept="image/*" style="display:none">
                    </div>
                    <div class="vm-photo-grid" id="vm-photo-preview"></div>

                    {{-- Video drop zone --}}
                    <p class="vm-section-label" style="margin-top:32px;">
                        <i data-lucide="video" style="width:14px;height:14px;"></i>
                        {{ __('messages.ventemodal.video_label') }} <span style="font-weight:500;text-transform:none;letter-spacing:0;opacity:.6;">{{ __('messages.ventemodal.optional') }}</span>
                    </p>
                    <p style="font-size:12px;color:var(--vp-muted);font-weight:600;margin-bottom:12px;">
                        {{ __('messages.ventemodal.video_hint') }}
                    </p>
                    <div class="vm-dropzone vm-dropzone-video" id="vm-video-zone" onclick="document.getElementById('vm-video-input').click()">
                        <div class="vm-dropzone-icon">🎥</div>
                        <div class="vm-dropzone-title">{{ __('messages.ventemodal.drop_video') }}</div>
                        <div class="vm-dropzone-sub">{{ __('messages.ventemodal.video_formats') }}</div>
                        <input type="file" id="vm-video-input" name="video"
                               accept="video/*" style="display:none">
                    </div>
                    <div id="vm-video-preview" style="display:none;" class="vm-video-preview">
                        <div class="vm-video-preview-icon">🎬</div>
                        <div class="vm-video-preview-name" id="vm-video-name"></div>
                        <button type="button" class="vm-video-remove" onclick="vmRemoveVideo()" title="{{ __('messages.ventemodal.remove') }}">✕</button>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                     STEP 3 — Choisissez votre pack
                ═════════════════════════════════════════ --}}
                <div id="vm-step-3" class="vm-step-content">
                    <h2 class="vm-step-h2" style="text-align:center;">{{ __('messages.ventemodal.s3_title') }}</h2>
                    <p style="text-align:center;color:var(--vp-muted);font-size:14px;font-weight:500;margin:-8px 0 32px;">
                        {{ __('messages.ventemodal.s3_sub') }}
                    </p>

                    {{-- Pack cards --}}
                    <div class="vm-packs-grid" id="vm-packs-list"></div>

                    {{-- Free publish CTA --}}
                    <div style="text-align:center;margin:28px 0 8px;">
                        <button type="button" id="vm-free-btn" class="vm-free-btn">
                            <i data-lucide="sparkles" style="width:15px;height:15px;"></i>
                            {{ __('messages.ventemodal.publish_free') }}
                        </button>
                    </div>
                </div>

                {{-- Nav buttons --}}
                <div class="vm-nav">
                    <button type="button" id="vm-prev" class="vm-btn-prev" onclick="vmMoveStep(-1)">{{ __('messages.ventemodal.prev') }}</button>
                    <div class="vm-spacer"></div>
                    <button type="button" id="vm-next" class="vm-btn-next" onclick="vmMoveStep(1)">
                        {{ __('messages.ventemodal.next') }} <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </form>

        </div>{{-- .vp-modal-body --}}

        {{-- ═══════════════════════════════════
             CONFIRMATION PAGE (new page inside modal)
        ════════════════════════════════════════ --}}
        <div id="vm-confirm-page" class="vm-confirm-page">
            {{-- Animated check --}}
            <div class="vm-cp-icon-wrap">
                <svg class="vm-cp-ring" viewBox="0 0 120 120">
                    <circle class="vm-cp-ring-bg"  cx="60" cy="60" r="54" fill="none" stroke="#e2e8f0" stroke-width="6"/>
                    <circle class="vm-cp-ring-fill" cx="60" cy="60" r="54" fill="none" stroke="#22c55e" stroke-width="6"
                            stroke-linecap="round" stroke-dasharray="339.3" stroke-dashoffset="339.3"/>
                </svg>
                <div class="vm-cp-check">
                    <i data-lucide="check" style="width:36px;height:36px;stroke-width:3;color:white;"></i>
                </div>
            </div>

            <h2 class="vm-cp-title">{{ __('messages.ventemodal.confirmed') }}</h2>

            <div class="vm-cp-pack-badge" id="vm-cp-pack-badge">
                <i data-lucide="package" style="width:14px;height:14px;"></i>
                <span id="vm-cp-pack-label">{{ __('messages.ventemodal.free_publish') }}</span>
            </div>

            <p class="vm-cp-sub">
                {{ __('messages.ventemodal.submitted_1') }}<br>
                {{ __('messages.ventemodal.submitted_2') }}
            </p>

            <div class="vm-cp-actions">
                @if(request()->boolean('embed'))
                    <button type="button" class="vp-cta-btn vm-cp-submit" onclick="closeVenteModal()">
                        {{ __('messages.ventemodal.back_dashboard') }}
                        <i data-lucide="arrow-right" class="vp-cta-arrow" style="width:18px;height:18px;"></i>
                    </button>
                @else
                    <button type="button" class="vp-cta-btn vm-cp-submit"
                            onclick="window.location.href = '{{ route('index') }}'">
                        {{ __('messages.ventemodal.back_site') }}
                        <i data-lucide="arrow-right" class="vp-cta-arrow" style="width:18px;height:18px;"></i>
                    </button>
                    <button type="button" class="vm-cp-cancel" onclick="vmResetVente(); closeVenteModal()">
                        {{ __('messages.ventemodal.stay') }}
                    </button>
                @endif
            </div>
        </div>{{-- #vm-confirm-page --}}

    </div>{{-- .vp-modal-box --}}
</div>{{-- #venteModal --}}

@endsection

@section('script')
<script>
// ── i18n (injected from messages.php so JS strings follow the locale) ──
const VM_I18N = {
    loading:         @json(__('messages.ventemodal.loading')),
    selectBrand:     @json(__('messages.ventemodal.select_brand')),
    selectModel:     @json(__('messages.ventemodal.select_model')),
    loadError:       @json(__('messages.ventemodal.load_error')),
    fillRequired:    @json(__('messages.ventemodal.fill_required')),
    bestChoice:      @json(__('messages.ventemodal.best_choice')),
    selected:        @json(__('messages.ventemodal.selected')),
    choose:          @json(__('messages.ventemodal.choose')),
    videoTooBig:     @json(__('messages.ventemodal.video_too_big')),
    filesTooBig:     @json(__('messages.ventemodal.files_too_big')),
    sessionExpired:  @json(__('messages.ventemodal.session_expired')),
    genericError:    @json(__('messages.ventemodal.generic_error')),
    connectionError: @json(__('messages.ventemodal.connection_error')),
    checkInfo:       @json(__('messages.ventemodal.check_info')),
    freePublish:     @json(__('messages.ventemodal.free_publish')),
    photosSuffix:    @json(__('messages.ventemodal.photos_suffix')),
};
// ── MODAL ─────────────────────────────────────────────────────────
function openVenteModal() {
    @auth('client')
        document.getElementById('venteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    @else
        window.location.href = "{{ route('client.login') }}?redirect=" + encodeURIComponent(window.location.href + (window.location.href.includes('?') ? '&' : '?') + 'open_modal=1');
    @endauth
}
function closeVenteModal() {
    // Embedded in the client dashboard: let the parent page close the popup
    // (and refresh its list if an annonce was published)
    if (VM_EMBED && window.parent !== window) {
        window.parent.postMessage({ type: vmSubmitted ? 'vente:done' : 'vente:close' }, window.location.origin);
        return;
    }
    document.getElementById('venteModal').classList.remove('open');
    document.body.style.overflow = '';
}
function handleModalBackdrop(e) {
    if (e.target === document.getElementById('venteModal')) closeVenteModal();
}

// ── STATE ─────────────────────────────────────────────────────────
var VM_EMBED = {{ request()->boolean('embed') ? 'true' : 'false' }};
var vmSubmitted = false;
var vmStep = 1;
var VM_TOTAL = 3;
var vmSelectedPack = null;
var vmPhotoFiles = [];   // accumulated File objects
// Modifier: the annonce's current photos [{name, url}] — removable, sent back as keep_photos[]
var vmExistingPhotos = @json($prefill['photos'] ?? []);
// id stays canonical (sent to the server as `mode`); title/features are localized.
var vmPacks = [
    { id: 'Pack Distance', title: @json(__('messages.ventemodal.pack_distance')), price: '199',
      features: [@json(__('messages.ventemodal.feat_remote')), @json(__('messages.ventemodal.feat_renew_1d')), @json(__('messages.ventemodal.feat_diffusion')), @json(__('messages.ventemodal.feat_direct_call')), @json(__('messages.ventemodal.feat_basic_support'))] },
    { id: 'Pack Showroom', title: @json(__('messages.ventemodal.pack_showroom')), price: '499', recommended: true,
      features: [@json(__('messages.ventemodal.feat_local')), @json(__('messages.ventemodal.feat_renew_3d')), @json(__('messages.ventemodal.feat_reels')), @json(__('messages.ventemodal.feat_expertise')), @json(__('messages.ventemodal.feat_expo')), @json(__('messages.ventemodal.feat_warranty')), @json(__('messages.ventemodal.feat_appointments'))] },
    { id: 'Pack Express',  title: @json(__('messages.ventemodal.pack_express')),  price: '299',
      features: [@json(__('messages.ventemodal.feat_mobility')), @json(__('messages.ventemodal.feat_renew_2d')), @json(__('messages.ventemodal.feat_youtube')), @json(__('messages.ventemodal.feat_control')), @json(__('messages.ventemodal.feat_rdv_support'))] }
];

// ── INIT ──────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // The layout can output this script twice — bind listeners only once,
    // otherwise every selected photo is added (and previewed) twice.
    if (window.vmInitDone) return;
    window.vmInitDone = true;

    if (typeof lucide !== 'undefined') lucide.createIcons();
    vmRenderPacks();

    // AJAX: brand → models
    document.getElementById('vm-mark').addEventListener('change', function () {
        var brandId = this.options[this.selectedIndex].dataset.id;
        var sel = document.getElementById('vm-modele');
        sel.innerHTML = '<option value="">' + VM_I18N.loading + '</option>';
        if (!brandId) { sel.innerHTML = '<option value="">' + VM_I18N.selectBrand + '</option>'; return; }
        fetch("{{ route('get.models') }}?brand_id=" + brandId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                sel.innerHTML = '<option value="">' + VM_I18N.selectModel + '</option>';
                data.forEach(function (m) {
                    var o = document.createElement('option');
                    o.value = m.name; o.textContent = m.name;
                    sel.appendChild(o);
                });
            })
            .catch(function () { sel.innerHTML = '<option value="">' + VM_I18N.loadError + '</option>'; });
    });

    // Photo drop zone
    vmInitPhotoZone();
    if (vmExistingPhotos.length) vmRenderPhotos(); // Modifier: show current photos

    // Video drop zone
    vmInitVideoZone();

    // Free publish button
    var freeBtn = document.getElementById('vm-free-btn');
    if (freeBtn) {
        freeBtn.addEventListener('click', function () {
            document.getElementById('vm-mode').value = 'gratuit';
            vmSubmitVente(null);
        });
    }

    // Auto-open if ?open_modal=1
    if (VM_EMBED) {
        openVenteModal(); // the page is only the modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeVenteModal();
        });
    } else if (new URLSearchParams(window.location.search).get('open_modal') === '1') {
        setTimeout(openVenteModal, 300);
    }

    // Prefill from existing annonce (client dashboard -> vente/formulaire?annonce_id=..)
    @isset($prefill)
    (function(){
        var pre = @json($prefill);
        if (!pre) return;

        // Fill simple inputs
        var prix = document.querySelector('input[name=\"prix\"]');
        if (prix && pre.prix != null) prix.value = pre.prix;

        // Select brand (triggers model fetch)
        var markSel = document.getElementById('vm-mark');
        var modeleSel = document.getElementById('vm-modele');
        if (markSel && pre.mark) {
            markSel.value = pre.mark;
            // Manually trigger change to fetch models, then set model
            var event = new Event('change');
            markSel.dispatchEvent(event);
        }

        // Try to set model after ajax finishes (simple polling)
        if (modeleSel && pre.modele) {
            var tries = 0;
            var timer = setInterval(function(){
                tries++;
                var has = Array.from(modeleSel.options).some(function(o){ return o.value === pre.modele; });
                if (has) {
                    modeleSel.value = pre.modele;
                    clearInterval(timer);
                }
                if (tries > 20) clearInterval(timer);
            }, 150);
        }
    })();
    @endisset
});

// ── PHOTO DROP ZONE ───────────────────────────────────────────────
function vmInitPhotoZone() {
    var zone  = document.getElementById('vm-photo-zone');
    var input = document.getElementById('vm-photos-input');

    zone.addEventListener('dragover', function (e) {
        e.preventDefault(); zone.classList.add('drag-over');
    });
    zone.addEventListener('dragleave', function () {
        zone.classList.remove('drag-over');
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault(); zone.classList.remove('drag-over');
        vmAddPhotos(e.dataTransfer.files);
    });
    input.addEventListener('change', function () {
        // vmRenderPhotos() writes the accumulated list back into this input
        vmAddPhotos(this.files);
    });
}

function vmAddPhotos(fileList) {
    var imgs = Array.from(fileList).filter(function (f) {
        if (!f.type.startsWith('image/')) return false;
        // skip files already in the list (e.g. re-selecting the same photo)
        return !vmPhotoFiles.some(function (p) {
            return p.name === f.name && p.size === f.size && p.lastModified === f.lastModified;
        });
    });
    // 20 photos max, current ones included
    vmPhotoFiles = vmPhotoFiles.concat(imgs).slice(0, Math.max(0, 20 - vmExistingPhotos.length));
    vmRenderPhotos();
}

function vmRemovePhoto(idx) {
    vmPhotoFiles.splice(idx, 1);
    vmRenderPhotos();
}

function vmRemoveExistingPhoto(idx) {
    vmExistingPhotos.splice(idx, 1);
    vmRenderPhotos();
}

function vmRenderPhotos() {
    var grid  = document.getElementById('vm-photo-preview');
    var count = document.getElementById('vm-photo-count');
    count.textContent = (vmExistingPhotos.length + vmPhotoFiles.length) + ' ' + VM_I18N.photosSuffix;

    // Render synchronously with object URLs: async FileReader callbacks from a
    // previous render could append after the grid was cleared (duplicates / wrong order).
    grid.querySelectorAll('img[data-blob]').forEach(function (img) { URL.revokeObjectURL(img.src); });
    grid.innerHTML = '';

    // Current photos of the annonce being edited
    var keep = document.getElementById('vm-keep-photos');
    keep.innerHTML = '';
    vmExistingPhotos.forEach(function (p, i) {
        var div = document.createElement('div');
        div.className = 'vm-photo-thumb';
        var img = document.createElement('img');
        img.src = p.url; img.alt = '';
        var btn = document.createElement('button');
        btn.type = 'button'; btn.className = 'vm-photo-remove'; btn.textContent = '✕';
        btn.onclick = function () { vmRemoveExistingPhoto(i); };
        div.appendChild(img); div.appendChild(btn);
        grid.appendChild(div);

        var input = document.createElement('input');
        input.type = 'hidden'; input.name = 'keep_photos[]'; input.value = p.name;
        keep.appendChild(input);
    });

    // New photos
    vmPhotoFiles.forEach(function (file, i) {
        var div = document.createElement('div');
        div.className = 'vm-photo-thumb';
        div.innerHTML =
            '<img data-blob="1" src="' + URL.createObjectURL(file) + '" alt="">' +
            '<button type="button" class="vm-photo-remove" onclick="vmRemovePhoto(' + i + ')">✕</button>';
        grid.appendChild(div);
    });

    // Keep the real file input in sync so the photos are actually submitted
    var dt = new DataTransfer();
    vmPhotoFiles.forEach(function (file) { dt.items.add(file); });
    document.getElementById('vm-photos-input').files = dt.files;
}

// ── VIDEO DROP ZONE ───────────────────────────────────────────────
function vmInitVideoZone() {
    var zone  = document.getElementById('vm-video-zone');
    var input = document.getElementById('vm-video-input');

    zone.addEventListener('dragover', function (e) {
        e.preventDefault(); zone.classList.add('drag-over');
    });
    zone.addEventListener('dragleave', function () {
        zone.classList.remove('drag-over');
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault(); zone.classList.remove('drag-over');
        if (e.dataTransfer.files[0]) vmSetVideo(e.dataTransfer.files[0]);
    });
    input.addEventListener('change', function () {
        if (this.files[0]) vmSetVideo(this.files[0]);
    });
}

function vmSetVideo(file) {
    var maxBytes = 100 * 1024 * 1024;
    if (file.size > maxBytes) {
        alert(VM_I18N.videoTooBig);
        document.getElementById('vm-video-input').value = '';
        return;
    }
    // Put the file in the real input (needed when it was dropped on the zone)
    var dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('vm-video-input').files = dt.files;
    document.getElementById('vm-video-name').textContent = file.name;
    document.getElementById('vm-video-preview').style.display = 'flex';
    document.getElementById('vm-video-zone').style.display = 'none';
}

function vmRemoveVideo() {
    document.getElementById('vm-video-input').value = '';
    document.getElementById('vm-video-preview').style.display = 'none';
    document.getElementById('vm-video-zone').style.display = 'block';
}

// ── STEP NAVIGATION ───────────────────────────────────────────────
function vmMoveStep(delta) {
    if (delta > 0 && !vmValidate(vmStep)) return;
    var next = vmStep + delta;
    if (next < 1 || next > VM_TOTAL) return;
    document.getElementById('vm-step-' + vmStep).classList.remove('active');
    vmStep = next;
    document.getElementById('vm-step-' + vmStep).classList.add('active');
    vmUpdateUI();
}

function vmValidate(step) {
    var ok = true;
    document.getElementById('vm-step-' + step)
        .querySelectorAll('input[required], select[required]')
        .forEach(function (el) {
            if (!el.value.trim()) { el.classList.add('error'); ok = false; }
            else                    el.classList.remove('error');
        });
    if (!ok) alert(VM_I18N.fillRequired);
    return ok;
}

function vmUpdateUI() {
    document.querySelectorAll('.vm-step-node').forEach(function (n) {
        n.classList.toggle('active', parseInt(n.dataset.step) <= vmStep);
    });
    // progress: step 1→0%, step 2→50%, step 3→100%
    document.getElementById('vm-progress').style.width = (((vmStep - 1) / (VM_TOTAL - 1)) * 80) + '%';

    document.getElementById('vm-prev').classList.toggle('show',   vmStep > 1);
    document.getElementById('vm-next').classList.toggle('hidden', vmStep === VM_TOTAL);

    if (typeof lucide !== 'undefined') lucide.createIcons();
    var box = document.querySelector('.vp-modal-box');
    if (box) box.scrollTop = 0;
}

// ── PACKS ─────────────────────────────────────────────────────────
function vmRenderPacks() {
    var c = document.getElementById('vm-packs-list');
    if (!c) return;
    c.innerHTML = vmPacks.map(function (p) {
        var sel = vmSelectedPack === p.id;
        return '<div class="vm-pack-card' + (sel ? ' selected' : '') + '" onclick="vmSelectPack(\'' + p.id + '\')">'
            + (p.recommended ? '<div class="vm-pack-recommended">' + VM_I18N.bestChoice + '</div>' : '')
            + '<h3 class="vm-pack-title">' + p.title + '</h3>'
            + '<div class="vm-pack-price">' + p.price + ' <span>DH</span></div>'
            + '<ul class="vm-pack-feats">'
            + p.features.map(function (f) {
                return '<li class="vm-pack-feat">'
                    + '<i data-lucide="check-circle-2" class="vm-pack-feat-icon" style="width:14px;height:14px;"></i>'
                    + f + '</li>';
              }).join('')
            + '</ul>'
            + '<button type="button" class="vm-select-btn">'
            + (sel ? (VM_I18N.selected + ' ✓') : VM_I18N.choose) + '</button>'
            + '</div>';
    }).join('');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function vmSelectPack(id) {
    if (vmSending) return;
    vmSelectedPack = id;
    document.getElementById('vm-mode').value = id;
    vmRenderPacks();
    vmSubmitVente(id);
}

// ── SUBMIT ────────────────────────────────────────────────────────
// The annonce is sent first; the confirmation page is only shown once the
// server has actually saved it.
var vmSending = false;

// Fresh CSRF token: the one rendered in the page is stale if the session was
// regenerated meanwhile (login/logout in another tab, expiry) → "419 Page Expired".
function vmRefreshToken(form) {
    return fetch("{{ route('csrf.token') }}", { credentials: 'same-origin', cache: 'no-store' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data && data.token) {
                form.querySelector('input[name="_token"]').value = data.token;
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.token);
            }
        })
        .catch(function () { /* fall back to the token already in the form */ });
}

function vmSetSending(on) {
    vmSending = on;
    var body = document.getElementById('vm-main-body');
    body.style.pointerEvents = on ? 'none' : '';
    body.style.opacity = on ? '.6' : '';
    var freeBtn = document.getElementById('vm-free-btn');
    if (freeBtn) freeBtn.disabled = on;
}

function vmSubmitVente(packId) {
    if (vmSending) return;
    var form = document.getElementById('vm-form');
    vmSetSending(true);

    vmRefreshToken(form)
        .then(function () {
            return fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
        })
        .then(function (r) {
            return r.json().catch(function () { return {}; })
                .then(function (data) { return { status: r.status, data: data }; });
        })
        .then(function (res) {
            vmSetSending(false);
            if (res.status >= 200 && res.status < 300) {
                vmSubmitted = true;
                vmShowConfirmPage(packId);
            } else if (res.status === 422) {
                vmShowErrors(res.data.errors || {});
            } else if (res.status === 413) {
                alert(VM_I18N.filesTooBig);
            } else if (res.status === 419) {
                alert(VM_I18N.sessionExpired);
            } else {
                // Log the server's detail (message / exception) for debugging
                console.error('Vente submit failed', res.status, res.data);
                alert((res.data && res.data.message) || VM_I18N.genericError);
            }
        })
        .catch(function () {
            vmSetSending(false);
            alert(VM_I18N.connectionError);
        });
}

// Validation errors (422): highlight fields, go back to the first step with an error
function vmShowErrors(errors) {
    var form = document.getElementById('vm-form');
    var messages = [];
    var firstStep = null;
    Object.keys(errors).forEach(function (key) {
        messages.push(errors[key][0]);
        var name = key.split('.')[0];
        var el = form.querySelector('[name="' + name + '"], [name="' + name + '[]"]');
        if (!el) return;
        el.classList.add('error');
        var stepEl = el.closest('.vm-step-content');
        var step = stepEl ? parseInt(stepEl.id.replace('vm-step-', '')) : null;
        if (step && (firstStep === null || step < firstStep)) firstStep = step;
    });
    if (firstStep && firstStep !== vmStep) vmMoveStep(firstStep - vmStep);
    alert(messages.join('\n') || VM_I18N.checkInfo);
}

// Reset the modal to a blank form (after a successful submit)
function vmResetVente() {
    var form = document.getElementById('vm-form');
    form.reset();
    document.getElementById('vm-mode').value = '';
    vmSelectedPack = null;
    vmPhotoFiles = [];
    vmExistingPhotos = [];
    // after an edit, a new submit must create a new annonce, not update that one again
    var annonceIdInput = form.querySelector('input[name="annonce_id"]');
    if (annonceIdInput) annonceIdInput.remove();
    vmRenderPhotos();
    vmRemoveVideo();
    vmRenderPacks();
    document.getElementById('vm-step-' + vmStep).classList.remove('active');
    vmStep = 1;
    document.getElementById('vm-step-1').classList.add('active');
    vmUpdateUI();
    document.getElementById('vm-confirm-page').classList.remove('show');
    document.getElementById('vm-main-body').style.display = '';
}

function vmShowConfirmPage(packId) {
    var pack  = packId ? vmPacks.find(function (p) { return p.id === packId; }) : null;
    var label = pack
        ? pack.title + ' — ' + pack.price + ' DH'
        : VM_I18N.freePublish;

    document.getElementById('vm-cp-pack-label').textContent = label;

    // Swap: hide steps body, show confirmation page
    document.getElementById('vm-main-body').style.display = 'none';
    document.getElementById('vm-confirm-page').classList.add('show');

    var box = document.querySelector('.vp-modal-box');
    if (box) box.scrollTop = 0;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
</script>

@auth('client')
<style>
    .cg-box { border: 1px dashed #c7d6f5; background: linear-gradient(180deg,#f5f9ff,#ffffff); border-radius: 14px; padding: 16px; margin-bottom: 20px; }
    .cg-head { display: flex; align-items: center; justify-content: space-between; }
    .cg-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: #1459DD; }
    .cg-sub { font-size: 12.5px; color: #5b6b86; margin: 6px 0 12px; }
    /* Input réellement présent (pas display:none) mais invisible → ouverture caméra fiable. */
    .cg-file-input { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; opacity: 0; }
    .cg-slots { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media (max-width: 480px) { .cg-slots { grid-template-columns: 1fr; } }
    .cg-slot { display: flex; flex-direction: column; align-items: center; gap: 3px; text-align: center; padding: 16px 10px; border: 2px dashed #bcd0f5; border-radius: 12px; background: #fff; cursor: pointer; transition: .15s; }
    .cg-slot:hover, .cg-slot.drag { background: #eef4ff; border-color: #1459DD; }
    .cg-slot.filled { border-style: solid; border-color: #34d399; background: #f0fdf9; }
    .cg-slot-ic { width: 24px; height: 24px; color: #1459DD; }
    .cg-slot.filled .cg-slot-ic { color: #059669; }
    .cg-slot-title { font-weight: 800; font-size: 13px; color: #0f172a; }
    .cg-slot-sub { font-size: 11px; color: #8595ad; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cg-analyze { margin-top: 12px; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 16px; border-radius: 12px; background: #1459DD; color: #fff; font-weight: 800; font-size: 13.5px; cursor: pointer; transition: .15s; }
    .cg-analyze:hover:not(:disabled) { background: #0b3fa0; }
    .cg-analyze:disabled { opacity: .5; cursor: not-allowed; }
    .cg-status { margin-top: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 12px; }
    .cg-status.loading { background: #eff6ff; color: #1d4ed8; }
    .cg-status.ok { background: #ecfdf5; color: #047857; }
    .cg-status.err { background: #fef2f2; color: #b91c1c; }
    .cg-spin { width: 16px; height: 16px; border: 2px solid #bfdbfe; border-top-color: #1d4ed8; border-radius: 50%; animation: cgspin .7s linear infinite; }
    @keyframes cgspin { to { transform: rotate(360deg); } }
    .cg-consent { font-size: 11px; color: #7a889e; margin-top: 10px; display: flex; align-items: center; gap: 6px; line-height: 1.4; }
    /* Champs pré-remplis par l'IA (toujours éditables) */
    .vm-input.cg-filled { border-color: #34d399 !important; background: #f0fdf9 !important; }
    .vm-input.cg-low { border-color: #f59e0b !important; background: #fffbeb !important; }
    html[dir=rtl] .cg-zone, html[dir=rtl] .cg-status, html[dir=rtl] .cg-consent { text-align: right; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var recto   = document.getElementById('cg-recto');
    var verso   = document.getElementById('cg-verso');
    var analyze = document.getElementById('cg-analyze');
    var status  = document.getElementById('cg-status');
    if (!recto || !verso || !analyze) return;

    function showStatus(kind, html) {
        status.hidden = false;
        status.className = 'cg-status ' + kind;
        status.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function markField(el, low) {
        if (!el) return;
        el.classList.remove('cg-filled', 'cg-low');
        el.classList.add(low ? 'cg-low' : 'cg-filled');
    }

    // Sélectionne une valeur dans un <select> (insensible à la casse) → true si trouvé
    function setSelect(el, value) {
        if (!el || value == null) return false;
        var target = String(value).toLowerCase();
        for (var i = 0; i < el.options.length; i++) {
            if (el.options[i].value.toLowerCase() === target || el.options[i].text.trim().toLowerCase() === target) {
                el.selectedIndex = i; return true;
            }
        }
        return false;
    }

    function applyFields(data) {
        var f = data.fields || {};
        var low = data.low_confidence || [];
        var isLow = function (k) { return low.indexOf(k) !== -1; };

        // Champs simples
        if (f.cv != null) { var cv = document.querySelector('input[name="cv"]'); if (cv) { cv.value = f.cv; markField(cv, isLow('cv')); } }
        if (f.year != null) { var y = document.querySelector('select[name="year"]'); if (setSelect(y, f.year)) markField(y, isLow('year')); }
        if (f.carburant != null) { var c = document.querySelector('select[name="carburant"]'); if (setSelect(c, f.carburant)) markField(c, isLow('carburant')); }

        // Champs cachés (non publiés tels quels)
        if (f.matricule != null) { var mt = document.getElementById('vm-matricule'); if (mt) mt.value = f.matricule; }
        if (f.vin != null) { var vn = document.getElementById('vm-vin'); if (vn) vn.value = f.vin; }

        // Marque → déclenche le chargement AJAX des modèles, puis sélectionne le modèle
        var markSel = document.getElementById('vm-mark');
        var modeleSel = document.getElementById('vm-modele');
        if (f.mark && markSel && setSelect(markSel, f.mark)) {
            markField(markSel, isLow('mark'));
            markSel.dispatchEvent(new Event('change')); // charge les modèles de la marque
            if (f.modele && modeleSel) {
                var tries = 0;
                var timer = setInterval(function () {
                    tries++;
                    if (setSelect(modeleSel, f.modele)) { markField(modeleSel, isLow('modele')); clearInterval(timer); }
                    if (tries > 25) clearInterval(timer);
                }, 150);
            }
        }

        var n = Object.keys(f).length;
        if (n === 0) {
            showStatus('err', '<i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> {{ __('messages.ai.nofields') }}');
        } else {
            showStatus('ok', '<i data-lucide="check-circle" style="width:16px;height:16px;"></i> {{ __('messages.ai.done') }}');
        }
    }

    // Affiche le nom du fichier choisi et marque le slot comme rempli.
    function bindSlot(input, slotId, nameId, hint) {
        var slot = document.getElementById(slotId);
        var name = document.getElementById(nameId);
        input.addEventListener('change', function () {
            if (input.files && input.files.length) {
                slot.classList.add('filled');
                name.textContent = input.files[0].name;
                refreshBtn();
                scheduleAuto(); // lancement automatique après sélection
            } else {
                slot.classList.remove('filled');
                name.textContent = hint;
                refreshBtn();
            }
        });
    }
    function refreshBtn() {
        var n = (recto.files && recto.files.length ? 1 : 0) + (verso.files && verso.files.length ? 1 : 0);
        analyze.disabled = (n === 0);
    }

    var autoTimer = null;
    // Débounce : laisse le temps d'ajouter le 2e côté, puis analyse une seule fois.
    function scheduleAuto() {
        if (autoTimer) clearTimeout(autoTimer);
        autoTimer = setTimeout(runExtract, 1000);
    }

    function runExtract() {
        if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
        var hasRecto = recto.files && recto.files.length;
        var hasVerso = verso.files && verso.files.length;
        if (!hasRecto && !hasVerso) return;

        var fd = new FormData();
        if (hasRecto) fd.append('images[]', recto.files[0]);
        if (hasVerso) fd.append('images[]', verso.files[0]);
        fd.append('_token', (document.querySelector('input[name=_token]') || {}).value || '{{ csrf_token() }}');

        analyze.disabled = true;
        showStatus('loading', '<span class="cg-spin"></span> {{ __('messages.ai.loading') }}');

        fetch("{{ route('cartegrise.extract') }}", {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: fd
        })
        .then(function (r) { return r.json().catch(function () { return { success: false }; }); })
        .then(function (data) {
            if (data && data.success) { applyFields(data); }
            else { showStatus('err', '<i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> ' + (data && data.message ? data.message : '{{ __('messages.ai.failed') }}')); }
            refreshBtn();
        })
        .catch(function () {
            showStatus('err', '<i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> {{ __('messages.ai.failed') }}');
            refreshBtn();
        });
    }

    bindSlot(recto, 'cg-slot-recto', 'cg-recto-name', @json(__('messages.ai.recto_hint')));
    bindSlot(verso, 'cg-slot-verso', 'cg-verso-name', @json(__('messages.ai.verso_hint')));

    analyze.addEventListener('click', runExtract); // bouton = re-analyser (optionnel)
});
</script>
@endauth
@endsection
