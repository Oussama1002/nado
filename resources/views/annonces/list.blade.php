@extends('layouts.app')
@section('title', 'Catalogue voitures — NADO')

@section('content')
<style>
    /* ========== BASE ========== */
    .purchase-page {
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        padding-bottom: 80px;
    }

    /* ========== BREADCRUMB ========== */
    .pp-breadcrumb {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 12px 0;
    }
    .pp-breadcrumb .inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .pp-breadcrumb nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
    }
    .pp-breadcrumb a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }
    .pp-breadcrumb a:hover { color: #1459DD; }
    .pp-breadcrumb .current { color: #0f172a; }

    /* ========== HERO HEADER ========== */
    .pp-header {
        max-width: 1280px;
        margin: 0 auto;
        padding: 32px 16px;
    }
    .pp-header-row {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    @media (min-width: 768px) {
        .pp-header-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
    }
    .pp-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1459DD;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 8px;
    }
    .pp-h1 {
        font-size: 3.25rem;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.05em;
        line-height: 1;
        margin: 0;
    }
    @media (min-width: 640px) {
        .pp-h1 { font-size: 4.5rem; }
    }
    @media (min-width: 768px) {
        .pp-h1 { font-size: 5.5rem; }
    }
    @media (min-width: 1024px) {
        .pp-h1 { font-size: 6rem; }
    }
    .pp-h1 .pp-blue { color: #1459DD; }
    .pp-h1 .pp-yellow { color: #FDD817; }

    /* ========== CATEGORIES ========== */
    .pp-cats-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .pp-cats-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 8px 4px 32px;
        scrollbar-width: none;
        -ms-overflow-style: none;
        margin-bottom: 10px;
    }
    .pp-cats-scroll::-webkit-scrollbar { display: none; }

    .pp-cat-tile {
        flex-shrink: 0;
        width: 112px;
        border-radius: 1rem;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: all 0.25s;
        text-align: left;
    }
    .pp-cat-tile:hover {
        border-color: #1459DD;
        box-shadow: 0 8px 20px -4px rgba(20,89,221,0.12);
    }
    .pp-cat-tile:hover .pp-cat-top img { opacity: 1 !important; }
    .pp-cat-tile.active .pp-cat-top img { opacity: 1 !important; }
    .pp-cat-tile.active {
        border-color: #1459DD;
        box-shadow: 0 12px 28px -6px rgba(20,89,221,0.18);
        transform: scale(1.03);
    }
    .pp-cat-top {
        height: 64px;
        background: #f8fafc;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .pp-cat-top svg { transition: color 0.2s; }
    .pp-cat-tile.active .pp-cat-top svg { color: #1459DD; }
    .pp-cat-tile:not(.active) .pp-cat-top svg { color: #94a3b8; }
    .pp-cat-tile:not(.active):hover .pp-cat-top svg { color: #1459DD; }
    .pp-cat-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 16px;
        height: 16px;
        background: #FDD817;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .pp-cat-tile.active .pp-cat-check { display: flex; }
    .pp-cat-label {
        padding: 8px 8px;
        background: #fff;
        font-size: 8px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1.3;
    }
    .pp-cat-tile.active .pp-cat-label { color: #1459DD; }
    .pp-cat-tile:not(.active) .pp-cat-label { color: #475569; }

    /* ========== MAIN LAYOUT ========== */
    .pp-main-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 16px;
    }
    .pp-layout {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }
    @media (min-width: 1024px) {
        .pp-layout { flex-direction: row; align-items: flex-start; }
    }

    /* ========== SIDEBAR ========== */
    .pp-sidebar {
        display: none;
    }
    @media (min-width: 1024px) {
        .pp-sidebar {
            display: block;
            width: 360px;
            flex-shrink: 0;
            position: sticky;
            top: 112px;
            max-height: calc(100vh - 128px);
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .pp-sidebar::-webkit-scrollbar { width: 8px; }
        .pp-sidebar::-webkit-scrollbar-track { background: transparent; }
        .pp-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .pp-sidebar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    }
    .pp-sidebar-inner {
        background: #f8fafc;
        border-radius: 1.5rem;
        padding: 24px;
        border: 1px solid #f1f5f9;
    }
    .pp-sidebar-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    .pp-sidebar-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pp-icon-box {
        width: 40px;
        height: 40px;
        background: #1459DD;
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pp-icon-box.blue-light {
        background: #eff6ff;
        color: #1459DD;
    }
    .pp-sidebar-title h2 {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.025em;
        margin: 0;
    }
    .pp-reset-btn {
        font-size: 10px;
        font-weight: 700;
        color: #1459DD;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: none;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }
    .pp-reset-btn:hover { text-decoration: underline; }

    /* ========== FILTER BLOCKS ========== */
    .pp-filter-blocks {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .pp-filter-block {
        background: #fff;
        border-radius: 1rem;
        padding: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .pp-filter-block-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .pp-filter-block-head span {
        font-size: 13px;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }

    .pp-select {
        width: 100%;
        background: #f8fafc;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        cursor: pointer;
        appearance: none;
        margin-bottom: 10px;
        outline: none;
        transition: box-shadow 0.2s;
    }
    .pp-select:last-child { margin-bottom: 0; }
    .pp-select:focus {
        box-shadow: 0 0 0 3px rgba(20,89,221,0.15);
    }

    .pp-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .pp-chip-label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 8px;
    }

    .pp-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }
    .pp-chips:last-child { margin-bottom: 0; }

    .pp-chip {
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        background: #f8fafc;
        color: #475569;
    }
    .pp-chip:hover { background: #f1f5f9; }
    .pp-chip.active {
        background: #1459DD;
        color: #fff;
        box-shadow: 0 4px 12px -2px rgba(20,89,221,0.35);
    }
    .pp-chip.grid { flex: 1; text-align: center; }

    .pp-slider-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .pp-slider-head label {
        font-size: 10px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .pp-slider-val {
        font-size: 14px;
        font-weight: 900;
        color: #1459DD;
    }

    input[type="range"].pp-range {
        width: 100%;
        height: 8px;
        border-radius: 10px;
        appearance: none;
        background: #e2e8f0;
        cursor: pointer;
        accent-color: #1459DD;
        margin-bottom: 6px;
    }
    input[type="range"].pp-range::-webkit-slider-thumb {
        appearance: none;
        width: 24px;
        height: 24px;
        background: #1459DD;
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(20,89,221,0.3);
        cursor: pointer;
    }
    .pp-slider-ticks {
        display: flex;
        justify-content: space-between;
        font-size: 9px;
        color: #94a3b8;
        font-weight: 700;
        margin-top: 2px;
    }

    .pp-search-btn {
        width: 100%;
        padding: 16px;
        background: #1459DD;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 8px 20px -4px rgba(20,89,221,0.3);
    }
    .pp-search-btn:hover { background: #1d4ed8; transform: translateY(-1px); }
    .pp-search-btn:active { transform: scale(0.98); }

    /* ========== LISTING AREA ========== */
    .pp-listing { flex: 1; }

    .pp-active-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 32px;
    }
    .pp-af-label {
        font-size: 10px;
        font-weight: 900;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .pp-af-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #1459DD;
        color: #fff;
        border-radius: 9999px;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        text-decoration: none;
    }
    .pp-af-chip svg { opacity: 0.7; }
    .pp-af-clear {
        font-size: 9px;
        font-weight: 900;
        color: #1459DD;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-left: auto;
    }
    .pp-results-count {
        font-size: 10px;
        font-weight: 900;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    /* ========== VEHICLE GRID ========== */
    .pp-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
    }
    @media (min-width: 768px) {
        .pp-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ========== VEHICLE CARD ========== */
    .pp-card {
        background: #fff;
        border-radius: 2.5rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s, transform 0.3s;
        text-decoration: none;
    }
    .pp-card:hover {
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    }

    .pp-card-media {
        position: relative;
        aspect-ratio: 16 / 11;
        overflow: hidden;
    }
    .pp-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s;
    }
    .pp-card:hover .pp-card-media img { transform: scale(1.1); }

    .pp-status {
        position: absolute;
        top: 24px;
        left: 24px;
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #fff;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .pp-status.disponible { background: rgba(34,197,94,0.9); }
    .pp-status.reserve    { background: rgba(249,115,22,0.9); }
    .pp-status.vendu      { background: rgba(239,68,68,0.9); }

    .pp-type {
        position: absolute;
        top: 24px;
        right: 24px;
        padding: 8px 14px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #fff;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 2;
    }
    .pp-type.certifie    { background: rgba(16,185,129,0.9); }
    .pp-type.particulier { background: rgba(59,130,246,0.95); }
    .pp-type.pro         { background: rgba(245,158,11,0.95); }

    .pp-price-overlay {
        position: absolute;
        bottom: 24px;
        left: 24px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        padding: 14px 18px;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .pp-price-overlay span {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1459DD;
        letter-spacing: -0.05em;
        line-height: 1;
    }

    .pp-card-body {
        padding: 32px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .pp-card-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 12px;
    }
    .pp-card-title-row h3 {
        font-size: 1.875rem;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.04em;
        line-height: 1.1;
        margin: 0;
        transition: color 0.2s;
    }
    .pp-card:hover .pp-card-title-row h3 { color: #1459DD; }

    .pp-specs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px 24px;
        margin-bottom: 24px;
    }
    .pp-spec {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pp-spec svg { color: #1459DD; flex-shrink: 0; }
    .pp-spec span {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: -0.01em;
        color: #475569;
    }

    /* Footer band */
    .pp-monthly {
        margin-top: auto;
        padding: 20px 24px;
        background: #1459DD;
        border-radius: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 8px 20px -4px rgba(20,89,221,0.3);
        transition: transform 0.25s;
    }
    .pp-card:hover .pp-monthly { transform: scale(1.02); }
    .pp-monthly-eye {
        font-size: 9px;
        font-weight: 900;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 4px;
    }
    .pp-monthly-val {
        font-size: 1.375rem;
        font-weight: 900;
        color: #fff;
    }
    .pp-monthly-val small {
        font-size: 11px;
        font-weight: 900;
        color: #FDD817;
    }
    .pp-monthly-arrow {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }

    /* ========== PROMO INSERT ========== */
    .pp-promo {
        grid-column: 1 / -1;
        position: relative;
        overflow: hidden;
        background: #0A1120;
        border-radius: 3rem;
        padding: 32px;
        display: flex;
        flex-direction: column;
        gap: 32px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    @media (min-width: 768px) {
        .pp-promo {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 48px;
        }
    }
    .pp-promo-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }
    .pp-promo-eyebrow span {
        font-size: 9px;
        font-weight: 900;
        color: #FDD817;
        text-transform: uppercase;
        letter-spacing: 0.3em;
    }
    .pp-promo h2 {
        font-size: 1.5rem;
        font-weight: 900;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: -0.04em;
        line-height: 1.1;
        margin: 0 0 12px;
    }
    @media (min-width: 768px) {
        .pp-promo h2 { font-size: 2.25rem; }
    }
    .pp-promo h2 .pp-promo-accent { color: #1459DD; }
    .pp-promo-body {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
        max-width: 28rem;
        margin: 0 0 20px;
        line-height: 1.6;
    }
    .pp-promo-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: #1459DD;
        color: #fff;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .pp-promo-cta:hover { background: #fff; color: #0f172a; }
    .pp-promo-img-wrap {
        width: 100%;
        flex-shrink: 0;
        aspect-ratio: 16 / 10;
        border-radius: 2.5rem;
        overflow: hidden;
        border: 4px solid rgba(30,41,59,0.5);
    }
    @media (min-width: 768px) {
        .pp-promo-img-wrap { width: 35%; }
    }
    .pp-promo-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 3s;
    }
    .pp-promo:hover .pp-promo-img-wrap img { transform: scale(1.1); }

    /* ========== BANNER ========== */
    .pp-banner {
        grid-column: 1 / -1;
        border-radius: 2rem;
        overflow: hidden;
    }
    .pp-banner img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    /* ========== PAGINATION ========== */
    .pp-pagination {
        margin-top: 80px;
        display: flex;
        justify-content: center;
    }
    .pp-pagination ul {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pp-pagination li a,
    .pp-pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #f1f5f9;
        color: #0f172a !important;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pp-pagination li.active span {
        background: #1459DD !important;
        color: #fff !important;
        border-color: #1459DD !important;
        box-shadow: 0 6px 16px -3px rgba(20,89,221,0.4);
    }
    .pp-pagination li a:hover {
        background: #1459DD !important;
        color: #fff !important;
        border-color: #1459DD !important;
    }
    .pp-pagination li.disabled span {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ========== EMPTY STATE ========== */
    .pp-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 0;
        color: #64748b;
        font-weight: 700;
    }

    /* ========== MOBILE FAB ========== */
    .pp-fab {
        display: block;
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 60;
        width: 90%;
        max-width: 400px;
    }
    @media (min-width: 1024px) { .pp-fab { display: none; } }
    .pp-fab-btn {
        width: 100%;
        height: 56px;
        background: #1459DD;
        color: #fff;
        border: none;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        cursor: pointer;
        box-shadow: 0 16px 32px -8px rgba(20,89,221,0.5);
        transition: transform 0.15s;
    }
    .pp-fab-btn:active { transform: scale(0.95); }

    /* ========== MOBILE DRAWER ========== */
    .pp-drawer {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: #f8fafc;
        transform: translateY(100%);
        transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pp-drawer.open { transform: translateY(0); }
    @media (min-width: 1024px) { .pp-drawer { display: none; } }
    .pp-drawer-inner {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .pp-drawer-head {
        background: #fff;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .pp-drawer-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pp-drawer-title h2 {
        font-size: 1.25rem;
        font-weight: 900;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.025em;
        margin: 0;
    }
    .pp-drawer-close {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        border: none;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        cursor: pointer;
        transition: background 0.15s;
    }
    .pp-drawer-close:hover { background: #e2e8f0; }
    .pp-drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
</style>

<div class="purchase-page">

    {{-- ====== BREADCRUMBS ====== --}}
    <div class="pp-breadcrumb">
        <div class="inner">
            <nav>
                <a href="{{ route('index') }}">Accueil</a>
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span class="current">Catalogue</span>
            </nav>
        </div>
    </div>

    {{-- ====== HERO HEADER ====== --}}
    <div class="pp-header">
        <div class="pp-header-row">
            <div>
                <div class="pp-header-meta">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    {{ __('messages.catalog.updated') }}
                </div>
                <h1 class="pp-h1">
                    {{ __('messages.catalog.hero_1') }}<br>
                    <span class="pp-blue">{{ __('messages.catalog.hero_blue') }}</span> <span class="pp-yellow">{{ __('messages.catalog.hero_yellow') }}</span>
                </h1>
            </div>
        </div>
    </div>

    {{-- ====== CATEGORY TILES ====== --}}
    <div class="pp-cats-wrap">
        <div class="pp-cats-scroll" id="catScroll">
            @php
                $bodyworkFilter = request('bodywork', '');
                // img = storage path relative to storage/logos/; icon = lucide name fallback
                $categories = [
                    ['label' => 'Toutes',         'value' => '',          'img' => null,                                  'icon' => 'list'],
                    ['label' => 'Micro Citadine',  'value' => 'Micro Citadine', 'img' => 'neuf-picto-segment-micro-citadine.png', 'icon' => 'car'],
                    ['label' => 'Citadine',        'value' => 'Citadine',  'img' => 'neuf-picto-segment-citadine.png',     'icon' => 'car'],
                    ['label' => 'Compacte',        'value' => 'Compacte',  'img' => 'neuf-picto-segment-compacte.png',     'icon' => 'car'],
                    ['label' => 'Berline',         'value' => 'Berline',   'img' => 'neuf-picto-segment-routiere.png',     'icon' => 'car'],
                    ['label' => 'Familiale',       'value' => 'Familiale', 'img' => 'neuf-picto-segment-familiale.png',    'icon' => 'users'],
                    ['label' => 'SUV / 4×4',      'value' => 'SUV',       'img' => 'neuf-picto-segment-4x4.png',          'icon' => 'truck'],
                    ['label' => 'Monospace',       'value' => 'Monospace', 'img' => 'neuf-picto-segment-monospace.png',    'icon' => 'layout'],
                    ['label' => 'Cabriolet',       'value' => 'Cabriolet', 'img' => 'neuf-picto-segment-cabriolet.png',   'icon' => 'wind'],
                    ['label' => 'Luxe',            'value' => 'Luxe',      'img' => 'neuf-picto-segment-luxe.png',         'icon' => 'star'],
                    ['label' => 'Utilitaire',      'value' => 'Utilitaire','img' => null,                                  'icon' => 'box'],
                    ['label' => 'Électrique',      'value' => 'Electrique','img' => null,                                  'icon' => 'battery-charging'],
                ];
            @endphp
            @foreach ($categories as $cat)
                @php $isActive = ($bodyworkFilter === $cat['value']); @endphp
                <button
                    type="button"
                    class="pp-cat-tile {{ $isActive ? 'active' : '' }}"
                    data-bodywork="{{ $cat['value'] }}"
                    onclick="setCatFilter('{{ $cat['value'] }}')"
                    title="{{ $cat['label'] }}"
                >
                    <div class="pp-cat-top">
                        @if($cat['img'])
                            <img
                                src="{{ asset('storage/logos/' . $cat['img']) }}"
                                alt="{{ $cat['label'] }}"
                                style="width:56px;height:40px;object-fit:contain;object-position:center;opacity:{{ $isActive ? '1' : '0.6' }};transition:opacity 0.2s;"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                            >
                            <span style="display:none;align-items:center;justify-content:center;color:{{ $isActive ? '#1459DD' : '#94a3b8' }};">
                                <i data-lucide="{{ $cat['icon'] }}" width="24" height="24"></i>
                            </span>
                        @else
                            <i data-lucide="{{ $cat['icon'] }}" width="24" height="24"></i>
                        @endif
                        <div class="pp-cat-check">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="pp-cat-label">{{ __('messages.catalog.cat')[$cat['value']] ?? $cat['label'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ====== MAIN LAYOUT ====== --}}
    <div class="pp-main-wrap">
        <div class="pp-layout">

            {{-- ====== SIDEBAR ====== --}}
            <aside class="pp-sidebar">
                <div class="pp-sidebar-inner">
                    <div class="pp-sidebar-head">
                        <div class="pp-sidebar-title">
                            <div class="pp-icon-box">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                                    <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                                    <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                                </svg>
                            </div>
                            <h2>{{ __('messages.catalog.filters') }}</h2>
                        </div>
                        <a href="{{ route('achat.index') }}" class="pp-reset-btn">{{ __('messages.catalog.reset') }}</a>
                    </div>

                    @include('annonces._filter_form', ['formId' => 'desktopFilterForm'])
                </div>
            </aside>

            {{-- ====== LISTING ====== --}}
            <div class="pp-listing">

                {{-- Active filters row --}}
                <div class="pp-active-filters">
                    <span class="pp-af-label">{{ __('messages.catalog.active_filters') }}</span>
                    @if(request('marque'))
                        <a href="{{ route('achat.index', array_merge(request()->except('marque'), ['page'=>1])) }}" class="pp-af-chip">
                            {{ request('marque') }}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </a>
                    @endif
                    @if(request('carburant'))
                        <a href="{{ route('achat.index', array_merge(request()->except('carburant'), ['page'=>1])) }}" class="pp-af-chip">
                            {{ request('carburant') }}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </a>
                    @endif
                    @if(request('bodywork'))
                        <a href="{{ route('achat.index', array_merge(request()->except('bodywork'), ['page'=>1])) }}" class="pp-af-chip">
                            {{ request('bodywork') }}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </a>
                    @endif
                    <span class="pp-results-count">{{ $count }} {{ $count > 1 ? __('messages.catalog.count_many') : __('messages.catalog.count_one') }}</span>
                    @if(request()->except('page'))
                        <a href="{{ route('achat.index') }}" class="pp-af-clear">{{ __('messages.catalog.clear_all') }}</a>
                    @endif
                </div>

                {{-- All results are sold / non-disponible → show the demande card above the grid --}}
                @if(!empty($noAvailable))
                    <x-demande-empty-state
                        :filters="array_merge($searchKeys ?? [], ['source' => 'list'])"
                        :title="__('messages.catalog.empty_sold_title')"
                        :message="__('messages.catalog.empty_sold_message')" />
                @endif

                {{-- Vehicle grid --}}
                <div class="pp-grid">
                    @forelse ($ads as $index => $ad)

                        <a href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad->id]) }}"
                           class="pp-card">
                            <div class="pp-card-media">
                                @php
                                    // Inline placeholder: a data URI can never 404, unlike a file in public/.
                                    $noPhoto = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NDAiIGhlaWdodD0iNDQwIiB2aWV3Qm94PSIwIDAgNjQwIDQ0MCI+PHJlY3Qgd2lkdGg9IjY0MCIgaGVpZ2h0PSI0NDAiIGZpbGw9IiNmMWY1ZjkiLz48ZyBmaWxsPSJub25lIiBzdHJva2U9IiNjYmQ1ZTEiIHN0cm9rZS13aWR0aD0iMTIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTE4OCAyNjJ2LTM2YTE2IDE2IDAgMCAxIDE2LTE2aDEybDI0LTQwaDE2MGwyNCA0MGgxMmExNiAxNiAwIDAgMSAxNiAxNnYzNnoiLz48Y2lyY2xlIGN4PSIyMzIiIGN5PSIyNjIiIHI9IjIyIi8+PGNpcmNsZSBjeD0iNDA4IiBjeT0iMjYyIiByPSIyMiIvPjwvZz48dGV4dCB4PSIzMjAiIHk9IjMzMCIgZm9udC1mYW1pbHk9IkhlbHZldGljYSxBcmlhbCxzYW5zLXNlcmlmIiBmb250LXNpemU9IjIyIiBmb250LXdlaWdodD0iYm9sZCIgbGV0dGVyLXNwYWNpbmc9IjIiIGZpbGw9IiM5NGEzYjgiIHRleHQtYW5jaG9yPSJtaWRkbGUiPlBIT1RPIElORElTUE9OSUJMRTwvdGV4dD48L3N2Zz4=';

                                    // Prefer the model's own resolver — this is what the homepage uses.
                                    $first = null;
                                    if (method_exists($ad, 'pathImageAds')) {
                                        $p = $ad->pathImageAds();
                                        if (is_string($p) && trim($p) !== '') $first = trim($p);
                                    }

                                    // Fallback: images can arrive as a real array, a JSON string (no $casts
                                    // on the model), a comma-separated list, or a Collection of image models.
                                    $imgs = $ad->images ?? null;

                                    if (is_string($imgs)) {
                                        $decoded = json_decode($imgs, true);
                                        $imgs = is_array($decoded) ? $decoded : explode(',', $imgs);
                                    } elseif ($imgs instanceof \Illuminate\Support\Collection) {
                                        $imgs = $imgs->all();          // Collection -> array
                                    } elseif (is_object($imgs)) {
                                        $imgs = (array) $imgs;
                                    }
                                    $imgs = is_array($imgs) ? $imgs : [];

                                    // Each entry may itself be a model/array; pull the filename out of it.
                                    $imgs = array_values(array_filter(array_map(function ($i) {
                                        if (is_string($i)) return trim($i);
                                        $keys = ['path', 'url', 'image', 'filename', 'file', 'name', 'src'];
                                        foreach ($keys as $k) {
                                            $v = is_array($i) ? ($i[$k] ?? null)
                                               : (is_object($i) && isset($i->$k) ? $i->$k : null);
                                            if (is_string($v) && trim($v) !== '') return trim($v);
                                        }
                                        return null;
                                    }, $imgs)));

                                    $first = $first ?? ($imgs[0] ?? null);

                                    if (!$first) {
                                        $adImage = $noPhoto;
                                    } elseif (preg_match('#^(https?:)?//|^data:#', $first)) {
                                        $adImage = $first;                       // already an absolute URL
                                    } else {
                                        // strip any prefix already baked into the stored value
                                        $path = ltrim(str_replace('\\', '/', $first), '/');
                                        $path = preg_replace('#^(public/|storage/)#', '', $path);
                                        $path = preg_replace('#^ads/#', '', $path);
                                        $adImage = asset('storage/ads/' . $path);
                                    }
                                @endphp
                                <img
                                    src="{{ $adImage }}"
                                    alt="{{ $ad->mark }} {{ $ad->modele }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ $noPhoto }}';"
                                >
                                @if(request()->has('debugimg'))
                                    <div style="position:absolute;inset:0;background:rgba(255,255,255,.94);padding:10px;font:11px/1.45 monospace;color:#0f172a;overflow:auto;z-index:5;word-break:break-all;">
                                        <b>pathImageAds():</b> {{ method_exists($ad, 'pathImageAds') ? var_export($ad->pathImageAds(), true) : 'METHOD MISSING' }}<br>
                                        <b>raw type:</b> {{ get_debug_type($ad->images ?? null) }}<br>
                                        <b>raw:</b> {{ mb_substr(is_scalar($ad->images ?? null) ? (string)$ad->images : (string)json_encode($ad->images ?? null), 0, 300) }}<br>
                                        <b>normalised:</b> {{ json_encode($imgs) }}<br>
                                        <b>first:</b> {{ $first ?? 'NULL' }}<br>
                                        <b>src:</b> {{ str_starts_with($adImage, 'data:') ? '[placeholder]' : $adImage }}
                                    </div>
                                @endif
                                @php $st = strtolower($ad->status ?? 'disponible'); @endphp
                                <span class="pp-status {{ $st }}">
                                    @if($ad->status == 'réserve')
                                        {{ __('messages.catalog.reserved') }}
                                    @elseif(empty($ad->status) || $st === 'disponible')
                                        {{ __('messages.catalog.available') }}
                                    @else
                                        {{ strtoupper($ad->status) }}
                                    @endif
                                </span>
                                @if(!empty($ad->certifie))
                                    <span class="pp-type certifie">{{ __('messages.catalog.certified') }}</span>
                                @elseif(!empty($ad->particulie))
                                    <span class="pp-type particulier">{{ __('messages.catalog.particulier') }}</span>
                                @elseif(!empty($ad->pro))
                                    <span class="pp-type pro">{{ __('messages.catalog.pro') }}</span>
                                @endif
                                <div class="pp-price-overlay">
                                    <span>{{ number_format($ad->prix ?? 0, 0, ',', ' ') }} MAD</span>
                                </div>
                            </div>
                            <div class="pp-card-body">
                                <div class="pp-card-title-row">
                                    <h3>{{ $ad->brand ? $ad->brand->name : $ad->mark }} {{ $ad->modeleRelation ? $ad->modeleRelation->name : $ad->modele }}</h3>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1459DD" stroke-width="2" flex-shrink="0">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <div class="pp-specs">
                                    <div class="pp-spec">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <span>{{ $ad->year }}</span>
                                    </div>
                                    <div class="pp-spec">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/><path d="M12 6v6l4 2"/>
                                        </svg>
                                        <span>{{ number_format($ad->km ?? 0, 0, ',', ' ') }} km</span>
                                    </div>
                                    <div class="pp-spec">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M6.34 17.66l-1.41 1.41M2 12h2m16 0h2M6.34 6.34L4.93 4.93M17.66 17.66l1.41 1.41M12 22v-2M12 4V2"/>
                                        </svg>
                                        <span>{{ $ad->box ?? $ad->boite_vitesse ?? 'Auto' }}</span>
                                    </div>
                                    <div class="pp-spec">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 18V8a2 2 0 012-2h12a2 2 0 012 2v10M4 18h16M8 18v2m8-2v2M12 6V4m0 0a2 2 0 00-2 2m2-2a2 2 0 012 2"/>
                                        </svg>
                                        <span>{{ $ad->carburant ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="pp-monthly">
                                    <div>
                                        <div class="pp-monthly-eye">{{ __('messages.catalog.monthly') }}</div>
                                        <div class="pp-monthly-val">
                                            {{ number_format(($ad->prix ?? 0) / 84, 0, ',', ' ') }}
                                            <small> {{ __('messages.catalog.per_month') }}</small>
                                        </div>
                                    </div>
                                    <div class="pp-monthly-arrow">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>

                        {{-- Promo insert every 4th card --}}
                        @if (($index + 1) % 4 === 0)
                            <div class="pp-promo">
                                <div style="flex: 1;">
                                    <div class="pp-promo-eyebrow">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FDD817" stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        <span>{{ __('messages.catalog.trust_label') }}</span>
                                    </div>
                                    <h2>{!! __('messages.catalog.expertise_heading') !!}</h2>
                                    <p class="pp-promo-body">{{ __('messages.catalog.expertise_body') }}</p>
                                    <button class="pp-promo-cta" onclick="window.location.href='{{ route('about.index') }}'">
                                        {{ __('messages.catalog.expertise_cta') }}
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="pp-promo-img-wrap">
                                    <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800&q=80" alt="Expertise NADO">
                                </div>
                            </div>
                        @endif

                        {{-- Publication banners --}}
                        @if ($index + 1 === 6 && $firstBanner)
                            <div class="pp-banner">
                                <img src="{{ asset('storage/' . ltrim(preg_replace('#^(public/|storage/)#', '', $firstBanner->image), '/')) }}" alt="Publicité" onerror="this.closest('.pp-banner').style.display='none';">
                            </div>
                        @endif
                        @if ($index + 1 === 12 && $secondBanner)
                            <div class="pp-banner">
                                <img src="{{ asset('storage/' . ltrim(preg_replace('#^(public/|storage/)#', '', $secondBanner->image), '/')) }}" alt="Publicité" onerror="this.closest('.pp-banner').style.display='none';">
                            </div>
                        @endif
                        @if ($index + 1 === 18 && $thirdBanner)
                            <div class="pp-banner">
                                <img src="{{ asset('storage/' . ltrim(preg_replace('#^(public/|storage/)#', '', $thirdBanner->image), '/')) }}" alt="Publicité" onerror="this.closest('.pp-banner').style.display='none';">
                            </div>
                        @endif

                    @empty
                        <x-demande-empty-state :filters="array_merge($searchKeys ?? [], ['source' => 'list'])" />
                    @endforelse
                </div>

                {{-- "Pas trouvé votre voiture ?" CTA at the end of every page --}}
                @if ($ads->count())
                <div class="pp-no-match-cta">
                    <div class="pp-no-match-icon">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <div class="pp-no-match-text">
                        <h3>{{ __('messages.catalog.nomatch_title') }}</h3>
                        <p>{{ __('messages.catalog.nomatch_body') }}</p>
                    </div>
                    <a href="{{ route('demandes.create') }}" class="pp-no-match-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        {{ __('messages.catalog.nomatch_cta') }}
                    </a>
                </div>
                <style>
                    .pp-no-match-cta {
                        margin: 32px 0;
                        padding: 28px 24px;
                        background: linear-gradient(135deg, #1459DD 0%, #2563eb 100%);
                        color: #fff;
                        border-radius: 20px;
                        display: flex;
                        align-items: center;
                        gap: 20px;
                        box-shadow: 0 20px 40px -12px rgba(20, 89, 221, .35);
                    }
                    .pp-no-match-icon {
                        width: 64px; height: 64px;
                        background: rgba(255,255,255,.18);
                        border-radius: 50%;
                        display: flex; align-items: center; justify-content: center;
                        flex-shrink: 0;
                    }
                    .pp-no-match-text { flex: 1; min-width: 0; }
                    .pp-no-match-text h3 {
                        font-size: 20px; font-weight: 900; letter-spacing: -0.02em;
                        margin: 0 0 6px; line-height: 1.2;
                    }
                    .pp-no-match-text p {
                        font-size: 14px; font-weight: 500; opacity: .92; margin: 0; line-height: 1.5;
                    }
                    .pp-no-match-btn {
                        flex-shrink: 0;
                        display: inline-flex; align-items: center; gap: 8px;
                        padding: 14px 22px;
                        background: #FDD817;
                        color: #0f172a !important;
                        border-radius: 12px;
                        font-size: 13px; font-weight: 800;
                        text-transform: uppercase; letter-spacing: .06em;
                        text-decoration: none;
                        box-shadow: 0 8px 20px -4px rgba(0,0,0,.2);
                        transition: transform .15s, box-shadow .15s;
                    }
                    .pp-no-match-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 24px -4px rgba(0,0,0,.25);
                    }
                    @media (max-width: 768px) {
                        .pp-no-match-cta { flex-direction: column; text-align: center; padding: 24px 20px; gap: 16px; }
                        .pp-no-match-icon { width: 56px; height: 56px; }
                        .pp-no-match-text h3 { font-size: 18px; }
                        .pp-no-match-text p { font-size: 13px; }
                        .pp-no-match-btn { width: 100%; justify-content: center; padding: 14px 20px; }
                    }
                </style>
                @endif

                {{-- Pagination --}}
                @if ($ads->hasPages())
                    <div class="pp-pagination">
                        <ul>
                            @if ($ads->onFirstPage())
                                <li class="disabled">
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                    </span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $ads->previousPageUrl() }}" rel="prev">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                    </a>
                                </li>
                            @endif

                            @foreach ($ads->getUrlRange(max(1, $ads->currentPage() - 2), min($ads->lastPage(), $ads->currentPage() + 2)) as $page => $url)
                                @if ($page == $ads->currentPage())
                                    <li class="active"><span>{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if ($ads->hasMorePages())
                                <li>
                                    <a href="{{ $ads->nextPageUrl() }}" rel="next">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </li>
                            @else
                                <li class="disabled">
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

            </div>{{-- end .pp-listing --}}
        </div>{{-- end .pp-layout --}}
    </div>{{-- end .pp-main-wrap --}}

    {{-- ====== MOBILE FAB ====== --}}
    <div class="pp-fab">
        <button class="pp-fab-btn" id="openMobileFilter">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
            </svg>
            {{ __('messages.catalog.filters') }}
        </button>
    </div>

    {{-- ====== MOBILE DRAWER ====== --}}
    <div id="mobileDrawer" class="pp-drawer">
        <div class="pp-drawer-inner">
            <div class="pp-drawer-head">
                <div class="pp-drawer-title">
                    <div class="pp-icon-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                            <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                        </svg>
                    </div>
                    <h2>{{ __('messages.catalog.filters') }}</h2>
                </div>
                <button class="pp-drawer-close" id="closeMobileFilter">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="pp-drawer-body">
                @include('annonces._filter_form', ['formId' => 'mobileFilterForm'])
            </div>
        </div>
    </div>

</div>{{-- end .purchase-page --}}
@endsection

@section('script')
<script>
(function init() {
  // Run immediately if DOM is ready, otherwise wait
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }

function run() {

    // Init Lucide icons
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // ===== RANGE SLIDERS =====
    function setupSlider(rangeId, displayId, suffix, decimals) {
        var range = document.getElementById(rangeId);
        var disp  = document.querySelectorAll('[data-display="' + displayId + '"]');
        if (!range) return;
        function update() {
            var val = parseInt(range.value).toLocaleString('fr-FR') + ' ' + suffix;
            disp.forEach(function(el){ el.textContent = val; });
        }
        range.addEventListener('input', update);
        update();
    }
    setupSlider('priceRange', 'priceVal', 'MAD', 0);
    setupSlider('kmRange',    'kmVal',    'KM',  0);

    // ===== ENERGY CHIPS =====
    document.querySelectorAll('[data-fuel]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-fuel]').forEach(function(b){ b.classList.remove('active'); });
            this.classList.toggle('active');
            var inputs = document.querySelectorAll('input[name="carburant"]');
            inputs.forEach(function(inp){ inp.value = btn.classList.contains('active') ? btn.dataset.fuel : ''; });
        });
    });

    // ===== TRANS CHIPS =====
    document.querySelectorAll('[data-trans]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var grp = this.closest('.pp-chips');
            grp.querySelectorAll('[data-trans]').forEach(function(b){ b.classList.remove('active'); });
            this.classList.toggle('active');
            var inputs = document.querySelectorAll('input[name="box"]');
            inputs.forEach(function(inp){ inp.value = btn.classList.contains('active') ? btn.dataset.trans : ''; });
        });
    });

    // ===== CATEGORY FILTER =====
    window.setCatFilter = function(bodywork) {
        var url = new URL(window.location.href);
        if (bodywork === '') {
            url.searchParams.delete('bodywork');
        } else {
            url.searchParams.set('bodywork', bodywork);
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    };

    // ===== MOBILE DRAWER =====
    var drawer   = document.getElementById('mobileDrawer');
    var openBtn  = document.getElementById('openMobileFilter');
    var closeBtn = document.getElementById('closeMobileFilter');

    if (openBtn)  openBtn.addEventListener('click',  function(){ drawer.classList.add('open');    document.body.style.overflow = 'hidden'; });
    if (closeBtn) closeBtn.addEventListener('click', function(){ drawer.classList.remove('open'); document.body.style.overflow = ''; });

    // ===== BRAND → MODEL AJAX =====
    function loadModels(brandId, modelSelect) {
        if (!brandId) { modelSelect.innerHTML = '<option value="">' + @json(__('messages.catalog.all_models')) + '</option>'; return; }
        modelSelect.innerHTML = '<option value="">' + @json(__('messages.catalog.loading')) + '</option>';
        fetch('/achat/models-by-brand/' + brandId)
            .then(function(r){ return r.json(); })
            .then(function(data) {
                modelSelect.innerHTML = '<option value="">' + @json(__('messages.catalog.all_models')) + '</option>';
                var current = new URLSearchParams(window.location.search).get('modele');
                data.forEach(function(m) {
                    var o = document.createElement('option');
                    o.value = m.name; o.textContent = m.name;
                    if (current === m.name) o.selected = true;
                    modelSelect.appendChild(o);
                });
            })
            .catch(function(){ modelSelect.innerHTML = '<option value="">Erreur</option>'; });
    }

    document.querySelectorAll('select.nado-marks').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var id   = this.options[this.selectedIndex].getAttribute('data-id');
            var form = this.closest('form');
            var ms   = form ? form.querySelector('select.nado-models') : null;
            if (ms) loadModels(id, ms);
        });
        if (sel.value) {
            var id   = sel.options[sel.selectedIndex].getAttribute('data-id');
            var form = sel.closest('form');
            var ms   = form ? form.querySelector('select.nado-models') : null;
            if (ms) loadModels(id, ms);
        }
    });
} // end run()
})(); // end init()
</script>
@endsection
