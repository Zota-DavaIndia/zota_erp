@extends('layouts.app')
@section('title', __('home.home'))

@section('content')

<style>
    /* ================================
       DAVA INDIA DASHBOARD THEME
       ================================ */
    :root {
        --dava-green: #1F7A4D;
        --dava-green-dark: #0F4D2E;
        --dava-green-light: #2D9D6A;
        --dava-green-soft: #E8F5EE;
        --dava-orange: #F26A21;
        --dava-orange-dark: #D85A14;
        --dava-orange-light: #FFE9DA;
        --dava-bg: #F5FAF7;
    }

    .dava-dash-bg {
        background: var(--dava-bg);
        min-height: 100vh;
    }

    /* Welcome banner */
    .dava-welcome {
        background: linear-gradient(135deg, #1F7A4D 0%, #0F4D2E 100%);
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 28px 28px;
        color: #fff;
        box-shadow: 0 14px 40px rgba(31, 122, 77, 0.18);
    }
    .dava-welcome::before {
        content: "";
        position: absolute;
        top: -80px; right: -80px;
        width: 240px; height: 240px;
        background: rgba(242, 106, 33, 0.2);
        border-radius: 50%;
        filter: blur(6px);
    }
    .dava-welcome::after {
        content: "";
        position: absolute;
        bottom: -100px; left: -60px;
        width: 280px; height: 280px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 50%;
        filter: blur(8px);
    }
    .dava-welcome .dava-pill {
        position: absolute;
        border-radius: 999px;
        background: #fff;
        opacity: 0.12;
    }
    .dava-welcome .dava-pill-1 { top: 22%; right: 18%; width: 70px; height: 18px; transform: rotate(20deg); }
    .dava-welcome .dava-pill-2 { bottom: 18%; right: 8%; width: 50px; height: 14px; transform: rotate(-25deg); background: var(--dava-orange); opacity: 0.5; }
    .dava-welcome .dava-pill-3 { top: 60%; left: 6%; width: 40px; height: 12px; transform: rotate(45deg); }
    .dava-welcome h1 { color: #fff !important; font-size: 22px; font-weight: 700; margin: 0; position: relative; z-index: 2; line-height: 1.3; }
    .dava-welcome .dava-welcome-sub { color: rgba(255,255,255,0.85); font-size: 14px; margin-top: 6px; position: relative; z-index: 2; }
    .dava-welcome .dava-welcome-meta { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 999px; font-size: 12.5px; font-weight: 600; position: relative; z-index: 2; }

    /* Filter controls inside welcome banner */
    .dava-welcome .dava-filter { position: relative; z-index: 2; }
    .dava-welcome .dava-select,
    .dava-welcome #dashboard_date_filter {
        height: 42px;
        background: rgba(255,255,255,0.95) !important;
        border: 1.5px solid transparent !important;
        color: #0F2A1C !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        padding: 0 14px !important;
        min-width: 180px;
        transition: all .2s;
        box-shadow: none !important;
    }
    .dava-welcome .dava-select:focus,
    .dava-welcome #dashboard_date_filter:focus,
    .dava-welcome #dashboard_date_filter:hover {
        background: #fff !important;
        border-color: var(--dava-orange) !important;
        box-shadow: 0 0 0 4px rgba(242, 106, 33, 0.15) !important;
        outline: none;
    }
    .dava-welcome #dashboard_date_filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
    }

    /* Stat cards - solid colored variant */
    .dava-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #E5E7EB;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .dava-stat-card::after {
        content: "";
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        background: radial-gradient(circle, var(--accent-soft, var(--dava-green-soft)) 0%, transparent 70%);
        opacity: 0.6;
        pointer-events: none;
    }
    .dava-stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(15, 77, 46, 0.10); border-color: var(--accent, var(--dava-green)); }
    .dava-stat-card .dava-stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--accent-soft, var(--dava-green-soft));
        color: var(--accent, var(--dava-green));
        flex-shrink: 0;
    }
    .dava-stat-card .dava-stat-label { color: #6B7280; font-size: 13px; font-weight: 500; margin: 0; }
    .dava-stat-card .dava-stat-value { color: #0F2A1C; font-size: 22px; font-weight: 800; margin: 4px 0 0 0; letter-spacing: -0.4px; }
    .dava-stat-card .dava-stat-trend { display: inline-flex; align-items: center; gap: 3px; font-size: 12px; font-weight: 600; margin-top: 6px; color: var(--accent, var(--dava-green)); }
    .dava-stat-card.green { --accent: var(--dava-green); --accent-soft: var(--dava-green-soft); }
    .dava-stat-card.orange { --accent: var(--dava-orange); --accent-soft: var(--dava-orange-light); }
    .dava-stat-card.sky { --accent: #0EA5E9; --accent-soft: #E0F2FE; }
    .dava-stat-card.amber { --accent: #F59E0B; --accent-soft: #FEF3C7; }
    .dava-stat-card.red { --accent: #DC2626; --accent-soft: #FEE2E2; }
    .dava-stat-card.violet { --accent: #7C3AED; --accent-soft: #EDE9FE; }

    /* Solid color variant — modern 3D medicine look */
    .dava-stat-card.solid {
        color: #fff;
        border: none;
        border-radius: 18px;
        padding: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 32px var(--accent-shadow, rgba(31, 122, 77, 0.25));
        background: linear-gradient(135deg, var(--accent-1, var(--dava-green)) 0%, var(--accent-2, var(--dava-green-dark)) 100%);
    }
    .dava-stat-card.solid::before {
        content: "";
        position: absolute;
        top: -60px; right: -60px;
        width: 180px; height: 180px;
        background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dava-stat-card.solid::after {
        content: "";
        position: absolute;
        bottom: -80px; left: -40px;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dava-stat-card.solid:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 44px var(--accent-shadow, rgba(31, 122, 77, 0.35));
    }
    .dava-stat-card.solid .dava-stat-icon {
        width: 64px; height: 64px;
        border-radius: 18px;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(8px);
        border: 1.5px solid rgba(255,255,255,0.28);
        box-shadow: 0 8px 20px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.3);
        color: #fff;
    }
    .dava-stat-card.solid .dava-stat-icon svg { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    .dava-stat-card.solid .dava-stat-label { color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600; letter-spacing: 0.2px; }
    .dava-stat-card.solid .dava-stat-value { color: #fff; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; text-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    .dava-stat-card.solid .dava-stat-trend {
        color: #fff;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11.5px;
    }
    .dava-stat-card.solid.green { --accent-1: #1F7A4D; --accent-2: #0F4D2E; --accent-shadow: rgba(31, 122, 77, 0.3); }
    .dava-stat-card.solid.orange { --accent-1: #F26A21; --accent-2: #C44A0A; --accent-shadow: rgba(242, 106, 33, 0.32); }
    .dava-stat-card.solid.sky { --accent-1: #0EA5E9; --accent-2: #0369A1; --accent-shadow: rgba(14, 165, 233, 0.3); }
    .dava-stat-card.solid.amber { --accent-1: #F59E0B; --accent-2: #B45309; --accent-shadow: rgba(245, 158, 11, 0.3); }
    .dava-stat-card.solid.red { --accent-1: #EF4444; --accent-2: #B91C1C; --accent-shadow: rgba(239, 68, 68, 0.3); }
    .dava-stat-card.solid.violet { --accent-1: #8B5CF6; --accent-2: #6D28D9; --accent-shadow: rgba(139, 92, 246, 0.3); }

    /* Panel / chart / table cards */
    .dava-panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        overflow: hidden;
        transition: all .25s ease;
        height: 100%;
    }
    .dava-panel:hover { box-shadow: 0 12px 30px rgba(15, 77, 46, 0.08); }
    .dava-panel-head {
        padding: 18px 22px;
        border-bottom: 1px solid #F0F2F5;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .dava-panel-head .dava-panel-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--dava-green-soft);
        color: var(--dava-green);
        flex-shrink: 0;
    }
    .dava-panel-head .dava-panel-icon.orange { background: var(--dava-orange-light); color: var(--dava-orange); }
    .dava-panel-head .dava-panel-icon.sky { background: #E0F2FE; color: #0EA5E9; }
    .dava-panel-head .dava-panel-icon.amber { background: #FEF3C7; color: #F59E0B; }
    .dava-panel-head .dava-panel-icon.red { background: #FEE2E2; color: #DC2626; }
    .dava-panel-head h3 { margin: 0; font-size: 16px; font-weight: 700; color: #0F2A1C; }
    .dava-panel-head .dava-panel-sub { color: #6B7280; font-size: 12.5px; margin: 2px 0 0 0; }
    .dava-panel-head .dava-panel-right { margin-left: auto; min-width: 180px; }
    .dava-panel-head .dava-panel-right .select2-container { width: 100% !important; }
    .dava-panel-head .dava-panel-right .form-control {
        height: 38px;
        background: #F9FAFB !important;
        border: 1.5px solid #E5E7EB !important;
        color: #0F2A1C !important;
        border-radius: 9px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        box-shadow: none !important;
    }
    .dava-panel-head .dava-panel-right .form-control:focus { border-color: var(--dava-green) !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(31, 122, 77, 0.12) !important; }
    .dava-panel-body { padding: 18px 22px; }
    .dava-panel-body.tight { padding: 14px 0; }

    /* Chart area */
    .dava-chart-wrap {
        background: linear-gradient(180deg, #fff 0%, var(--dava-green-soft) 100%);
        border-radius: 12px;
        padding: 16px;
        border: 1px dashed #D1FAE5;
    }

    /* Tables inside panels */
    .dava-panel-body .table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
    }
    .dava-panel-body .table thead th {
        background: #F9FAFB;
        color: #4B5563;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border: none;
        border-bottom: 1px solid #E5E7EB;
    }
    .dava-panel-body .table tbody td {
        padding: 12px 14px;
        font-size: 13.5px;
        color: #0F2A1C;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    .dava-panel-body .table tbody tr:hover { background: #F9FAFB; }

    /* Status badges */
    .dava-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
    }
    .dava-badge.green { background: var(--dava-green-soft); color: var(--dava-green-dark); }
    .dava-badge.orange { background: var(--dava-orange-light); color: var(--dava-orange-dark); }
    .dava-badge.amber { background: #FEF3C7; color: #92400E; }
    .dava-badge.red { background: #FEE2E2; color: #991B1B; }
    .dava-badge.sky { background: #E0F2FE; color: #075985; }

    /* Section heading */
    .dava-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 24px 0 14px 0;
    }
    .dava-section-title .bar { width: 4px; height: 22px; background: linear-gradient(180deg, var(--dava-green), var(--dava-orange)); border-radius: 4px; }
    .dava-section-title h2 { font-size: 17px; font-weight: 700; color: #0F2A1C; margin: 0; }
    .dava-section-title .hint { color: #9CA3AF; font-size: 12.5px; }

    /* Layout wrapper */
    .dava-dash-wrap { padding: 22px 22px 32px 22px; }

    /* Responsive */
    @media (max-width: 768px) {
        .dava-welcome { padding: 22px 18px; }
        .dava-welcome h1 { font-size: 18px; }
        .dava-welcome .dava-pill { display: none; }
        .dava-stat-card { padding: 16px; }
        .dava-stat-card .dava-stat-value { font-size: 18px; }
        .dava-panel-head { padding: 14px 16px; }
        .dava-panel-body { padding: 14px 16px; }
        .dava-dash-wrap { padding: 14px 12px 24px 12px; }
        .dava-welcome .dava-select,
        .dava-welcome #dashboard_date_filter { min-width: 0; width: 100%; }
    }
</style>

<div class="dava-dash-bg">
<div class="dava-dash-wrap">

    {{-- Welcome banner with filters --}}
    @if (auth()->user()->can('dashboard.data'))
        @if ($is_admin)
            <div class="dava-welcome">
                <div class="dava-pill dava-pill-1"></div>
                <div class="dava-pill dava-pill-2"></div>
                <div class="dava-pill dava-pill-3"></div>

                <div class="tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between tw-gap-4">
                    <div style="position: relative; z-index: 2;">
                        <h1>
                            {{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
                        </h1>
                        <p class="dava-welcome-sub">Here's what's happening at your DavaIndia pharmacy chain today.</p>
                        <div class="dava-welcome-meta" style="margin-top:10px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            {{ \Carbon::now()->format('l, d M Y') }}
                        </div>
                    </div>

                    <div class="dava-filter tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full md:tw-w-auto">
                        @if (count($all_locations) > 1)
                            {!! Form::select('dashboard_location', $all_locations, null, [
                                'class' => 'dava-select select2',
                                'placeholder' => __('lang_v1.select_location'),
                                'id' => 'dashboard_location',
                            ]) !!}
                        @endif

                        <button type="button" id="dashboard_date_filter">
                            <svg width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>{{ __('messages.filter_by_date') }}</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- SALES STAT CARDS --}}
    @if (auth()->user()->can('dashboard.data'))
        @if ($is_admin)
            <div class="dava-section-title">
                <div class="bar"></div>
                <h2>Sales Overview</h2>
                <span class="hint">Track your pharmacy sales performance</span>
            </div>

            <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-gap-5 sm:tw-grid-cols-2 xl:tw-grid-cols-4">

                {{-- Total Sell --}}
                <div class="dava-stat-card solid green">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('home.total_sell') }}</p>
                            <p class="total_sell dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                                All outlets
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Pill/Capsule icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="pillLeft" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#D1FAE5"/>
                                    </linearGradient>
                                    <linearGradient id="pillRight" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0%" stop-color="#A7F3D0"/>
                                        <stop offset="100%" stop-color="#34D399"/>
                                    </linearGradient>
                                    <linearGradient id="pillShine" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.9"/>
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="34" rx="22" ry="10" fill="rgba(0,0,0,0.18)"/>
                                <rect x="10" y="18" width="44" height="22" rx="11" fill="url(#pillLeft)" stroke="rgba(0,0,0,0.2)" stroke-width="0.8"/>
                                <path d="M32 18 L32 40" stroke="rgba(0,0,0,0.15)" stroke-width="0.8"/>
                                <rect x="32" y="18" width="22" height="22" rx="11" fill="url(#pillRight)"/>
                                <rect x="14" y="20" width="36" height="6" rx="3" fill="url(#pillShine)"/>
                                <circle cx="20" cy="23" r="2" fill="#FFFFFF" opacity="0.8"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Net --}}
                <div class="dava-stat-card solid orange">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('lang_v1.net') }} @show_tooltip(__('lang_v1.net_home_tooltip'))</p>
                            <p class="net dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                Net revenue
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Medical Cross icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="crossBody" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#FED7AA"/>
                                    </linearGradient>
                                    <linearGradient id="crossShine" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.95"/>
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.1"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="48" rx="20" ry="4" fill="rgba(0,0,0,0.2)"/>
                                <path d="M24 12 H40 V24 H52 V40 H40 V52 H24 V40 H12 V24 H24 Z"
                                      fill="url(#crossBody)" stroke="rgba(0,0,0,0.25)" stroke-width="1" stroke-linejoin="round"/>
                                <path d="M24 12 H40 V18 H24 Z" fill="url(#crossShine)"/>
                                <path d="M12 24 H24 V28 H12 Z" fill="url(#crossShine)" opacity="0.6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Invoice Due --}}
                <div class="dava-stat-card solid amber">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('home.invoice_due') }}</p>
                            <p class="invoice_due dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Pending collection
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Prescription/Receipt icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="paper" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#FEF3C7"/>
                                    </linearGradient>
                                    <linearGradient id="rxBadge" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FCD34D"/>
                                        <stop offset="100%" stop-color="#D97706"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="54" rx="18" ry="3" fill="rgba(0,0,0,0.18)"/>
                                <path d="M14 8 L14 54 L20 50 L26 54 L32 50 L38 54 L44 50 L50 54 L50 8 Z"
                                      fill="url(#paper)" stroke="rgba(0,0,0,0.25)" stroke-width="1" stroke-linejoin="round"/>
                                <rect x="20" y="14" width="24" height="3" rx="1.5" fill="#F59E0B"/>
                                <rect x="20" y="22" width="20" height="2" rx="1" fill="#FBBF24" opacity="0.6"/>
                                <rect x="20" y="28" width="24" height="2" rx="1" fill="#FBBF24" opacity="0.6"/>
                                <rect x="20" y="34" width="18" height="2" rx="1" fill="#FBBF24" opacity="0.6"/>
                                <circle cx="40" cy="42" r="8" fill="url(#rxBadge)" stroke="rgba(0,0,0,0.25)" stroke-width="0.8"/>
                                <text x="40" y="46" text-anchor="middle" font-family="Arial, sans-serif" font-size="9" font-weight="900" fill="#FFFFFF">Rx</text>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Sell Return --}}
                <div class="dava-stat-card solid red">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">
                                {{ __('lang_v1.total_sell_return') }}
                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                    data-toggle="popover" data-placement="auto bottom" id="total_srp"
                                    data-value="{{ __('lang_v1.total_sell_return') }}-{{ __('lang_v1.total_sell_return_paid') }}"
                                    data-content="" data-html="true" data-trigger="hover"></i>
                            </p>
                            <p class="total_sell_return dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/></svg>
                                Returns processed
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Pill Bottle icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="bottleCap" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FECACA"/>
                                        <stop offset="100%" stop-color="#991B1B"/>
                                    </linearGradient>
                                    <linearGradient id="bottleBody" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="50%" stop-color="#FEE2E2"/>
                                        <stop offset="100%" stop-color="#FCA5A5"/>
                                    </linearGradient>
                                    <linearGradient id="bottleShine" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.85"/>
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="56" rx="16" ry="3" fill="rgba(0,0,0,0.2)"/>
                                <rect x="22" y="6" width="20" height="8" rx="2" fill="url(#bottleCap)" stroke="rgba(0,0,0,0.3)" stroke-width="0.8"/>
                                <rect x="20" y="14" width="24" height="3" fill="#7F1D1D"/>
                                <path d="M22 17 L22 52 Q22 54 24 54 L40 54 Q42 54 42 52 L42 17 Z"
                                      fill="url(#bottleBody)" stroke="rgba(0,0,0,0.25)" stroke-width="1"/>
                                <rect x="26" y="22" width="14" height="20" rx="2" fill="#FFFFFF" stroke="#DC2626" stroke-width="0.8"/>
                                <rect x="29" y="28" width="8" height="2" rx="1" fill="#DC2626"/>
                                <rect x="29" y="32" width="8" height="2" rx="1" fill="#DC2626" opacity="0.7"/>
                                <path d="M24 18 L24 50 Q24 52 26 52 L26 18 Z" fill="url(#bottleShine)"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PURCHASE STAT CARDS --}}
            <div class="dava-section-title">
                <div class="bar"></div>
                <h2>Purchase & Expenses</h2>
                <span class="hint">Inventory inflow and operational costs</span>
            </div>

            <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-gap-5 sm:tw-grid-cols-2 xl:tw-grid-cols-4">

                <div class="dava-stat-card solid sky">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('home.total_purchase') }}</p>
                            <p class="total_purchase dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Total inflow
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Medicine Box icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="boxTop" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#7DD3FC"/>
                                        <stop offset="100%" stop-color="#0EA5E9"/>
                                    </linearGradient>
                                    <linearGradient id="boxFront" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#E0F2FE"/>
                                    </linearGradient>
                                    <linearGradient id="boxShine" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.85"/>
                                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="54" rx="20" ry="3" fill="rgba(0,0,0,0.2)"/>
                                <path d="M10 22 L32 14 L54 22 L54 26 L32 18 L10 26 Z" fill="url(#boxTop)" stroke="rgba(0,0,0,0.25)" stroke-width="0.8"/>
                                <path d="M10 26 L32 18 L54 26 L54 50 L32 56 L10 50 Z" fill="url(#boxFront)" stroke="rgba(0,0,0,0.25)" stroke-width="0.8" stroke-linejoin="round"/>
                                <path d="M32 18 L32 56" stroke="rgba(0,0,0,0.1)" stroke-width="0.6"/>
                                <rect x="26" y="30" width="12" height="3" fill="#0EA5E9"/>
                                <rect x="30.5" y="25.5" width="3" height="12" fill="#0EA5E9"/>
                                <path d="M14 28 L14 48 Q14 50 16 50 L16 30 Z" fill="url(#boxShine)" opacity="0.6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="dava-stat-card solid amber">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('home.purchase_due') }}</p>
                            <p class="purchase_due dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Outstanding
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Stethoscope icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="stethTube" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#FCD34D"/>
                                    </linearGradient>
                                    <radialGradient id="stethBell" cx="0.35" cy="0.3" r="0.8">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="60%" stop-color="#F59E0B"/>
                                        <stop offset="100%" stop-color="#92400E"/>
                                    </radialGradient>
                                    <radialGradient id="stethEar" cx="0.35" cy="0.3" r="0.8">
                                        <stop offset="0%" stop-color="#FEF3C7"/>
                                        <stop offset="100%" stop-color="#B45309"/>
                                    </radialGradient>
                                </defs>
                                <ellipse cx="32" cy="58" rx="12" ry="2" fill="rgba(0,0,0,0.2)"/>
                                <path d="M14 12 L14 32 Q14 42 24 42 Q34 42 34 32 L34 18" fill="none" stroke="url(#stethTube)" stroke-width="5" stroke-linecap="round"/>
                                <path d="M34 18 L34 32 Q34 42 44 42" fill="none" stroke="url(#stethTube)" stroke-width="5" stroke-linecap="round"/>
                                <circle cx="14" cy="12" r="4" fill="url(#stethEar)" stroke="rgba(0,0,0,0.3)" stroke-width="0.6"/>
                                <circle cx="34" cy="12" r="4" fill="url(#stethEar)" stroke="rgba(0,0,0,0.3)" stroke-width="0.6"/>
                                <circle cx="44" cy="44" r="8" fill="url(#stethBell)" stroke="rgba(0,0,0,0.3)" stroke-width="0.8"/>
                                <circle cx="44" cy="44" r="3.5" fill="#7C2D12"/>
                                <circle cx="41" cy="41" r="1.5" fill="#FFFFFF" opacity="0.85"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="dava-stat-card solid red">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">
                                {{ __('lang_v1.total_purchase_return') }}
                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                data-toggle="popover" data-placement="auto bottom" id="total_prp"
                                data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                                data-content="" data-html="true" data-trigger="hover"></i>
                            </p>
                            <p class="total_purchase_return dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                                Returns to vendors
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Syringe icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="syringeBarrel" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="50%" stop-color="#FEE2E2"/>
                                        <stop offset="100%" stop-color="#FCA5A5"/>
                                    </linearGradient>
                                    <linearGradient id="syringePlunger" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FECACA"/>
                                        <stop offset="100%" stop-color="#7F1D1D"/>
                                    </linearGradient>
                                    <linearGradient id="syringeLiquid" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FCA5A5"/>
                                        <stop offset="100%" stop-color="#DC2626"/>
                                    </linearGradient>
                                </defs>
                                <ellipse cx="32" cy="56" rx="18" ry="2" fill="rgba(0,0,0,0.2)"/>
                                <g transform="rotate(-35 32 32)">
                                    <rect x="10" y="26" width="6" height="12" rx="1" fill="url(#syringePlunger)" stroke="rgba(0,0,0,0.3)" stroke-width="0.6"/>
                                    <rect x="16" y="22" width="3" height="20" fill="#7F1D1D"/>
                                    <rect x="19" y="24" width="22" height="16" rx="2" fill="url(#syringeBarrel)" stroke="rgba(0,0,0,0.3)" stroke-width="0.8"/>
                                    <rect x="22" y="26" width="17" height="12" rx="1" fill="url(#syringeLiquid)"/>
                                    <rect x="22" y="26" width="3" height="12" fill="#FFFFFF" opacity="0.5"/>
                                    <rect x="28" y="29" width="1" height="6" fill="#FFFFFF" opacity="0.6"/>
                                    <rect x="32" y="29" width="1" height="6" fill="#FFFFFF" opacity="0.6"/>
                                    <rect x="36" y="29" width="1" height="6" fill="#FFFFFF" opacity="0.6"/>
                                    <rect x="41" y="30" width="6" height="4" fill="#94A3B8" stroke="rgba(0,0,0,0.3)" stroke-width="0.6"/>
                                    <path d="M47 32 L52 30 L52 34 Z" fill="#94A3B8" stroke="rgba(0,0,0,0.3)" stroke-width="0.6" stroke-linejoin="round"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="dava-stat-card solid violet">
                    <div class="tw-flex tw-items-start tw-justify-between tw-gap-3">
                        <div style="flex:1; min-width:0;">
                            <p class="dava-stat-label">{{ __('lang_v1.expense') }}</p>
                            <p class="total_expense dava-stat-value" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;"></p>
                            <span class="dava-stat-trend">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                Operating cost
                            </span>
                        </div>
                        <div class="dava-stat-icon">
                            {{-- 3D Wallet/Cash icon --}}
                            <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="walletBack" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#C4B5FD"/>
                                        <stop offset="100%" stop-color="#6D28D9"/>
                                    </linearGradient>
                                    <linearGradient id="walletFront" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="100%" stop-color="#EDE9FE"/>
                                    </linearGradient>
                                    <radialGradient id="coin" cx="0.35" cy="0.3" r="0.8">
                                        <stop offset="0%" stop-color="#FEF3C7"/>
                                        <stop offset="100%" stop-color="#B45309"/>
                                    </radialGradient>
                                </defs>
                                <ellipse cx="32" cy="54" rx="20" ry="3" fill="rgba(0,0,0,0.2)"/>
                                <path d="M8 18 L8 50 Q8 52 10 52 L52 52 Q54 52 54 50 L54 24 L44 24 Q40 24 40 28 L40 36 Q40 40 44 40 L54 40 L54 50 Q54 52 52 52 L10 52 Q8 52 8 50 L8 18 Z"
                                      fill="url(#walletBack)" stroke="rgba(0,0,0,0.3)" stroke-width="0.8" stroke-linejoin="round"/>
                                <path d="M8 18 L8 50 Q8 52 10 52 L52 52 Q54 52 54 50 L54 24 L8 18 Z"
                                      fill="url(#walletFront)" stroke="rgba(0,0,0,0.25)" stroke-width="0.8" stroke-linejoin="round"/>
                                <path d="M8 18 L8 16 Q8 14 10 14 L48 14 Q50 14 50 16 L50 24 L8 18 Z"
                                      fill="#8B5CF6" stroke="rgba(0,0,0,0.3)" stroke-width="0.8" stroke-linejoin="round"/>
                                <path d="M14 16 L46 16 L46 22 L14 22 Z" fill="url(#walletBack)" opacity="0.5"/>
                                <circle cx="44" cy="32" r="6" fill="url(#coin)" stroke="rgba(0,0,0,0.3)" stroke-width="0.8"/>
                                <text x="44" y="35" text-anchor="middle" font-family="Arial, sans-serif" font-size="8" font-weight="900" fill="#7C2D12">$</text>
                                <path d="M12 18 L12 50 Q12 52 14 52 L14 20 Z" fill="#FFFFFF" opacity="0.4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- CHARTS & TABLES --}}
    @if (auth()->user()->can('dashboard.data'))
        <div class="tw-mt-2">
            <div class="tw-grid tw-grid-cols-1 tw-gap-4 sm:tw-gap-5 lg:tw-grid-cols-2">

                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                    @if (!empty($all_locations))
                        <div class="dava-panel lg:tw-col-span-2">
                            <div class="dava-panel-head">
                                <div class="dava-panel-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3>{{ __('home.sells_last_30_days') }}</h3>
                                    <p class="dava-panel-sub">Daily sales performance across all stores</p>
                                </div>
                            </div>
                            <div class="dava-panel-body">
                                <div class="dava-chart-wrap">
                                    {!! $sells_chart_1->container() !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!empty($all_locations))
                        <div class="dava-panel lg:tw-col-span-2">
                            <div class="dava-panel-head">
                                <div class="dava-panel-icon orange">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3v18h18"/><path d="M7 12l3-3 4 4 5-5"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3>{{ __('home.sells_current_fy') }}</h3>
                                    <p class="dava-panel-sub">Month-wise sales for the current financial year</p>
                                </div>
                            </div>
                            <div class="dava-panel-body">
                                <div class="dava-chart-wrap">
                                    {!! $sells_chart_2->container() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                    <div class="dava-panel lg:tw-col-span-1">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon amber">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>{{ __('lang_v1.sales_payment_dues') }} @show_tooltip(__('lang_v1.tooltip_sales_payment_dues'))</h3>
                            </div>
                            <div class="dava-panel-right">
                                {!! Form::select('sales_payment_dues_location', $all_locations, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('lang_v1.select_location'),
                                    'id' => 'sales_payment_dues_location',
                                ]) !!}
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped" id="sales_payment_dues_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang('contact.customer')</th>
                                            <th>@lang('sale.invoice_no')</th>
                                            <th>@lang('home.due_amount')</th>
                                            <th class="not-export">@lang('messages.action')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @can('purchase.view')
                    <div class="dava-panel lg:tw-col-span-1">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon amber">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>{{ __('lang_v1.purchase_payment_dues') }} @show_tooltip(__('tooltip.payment_dues'))</h3>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('purchase_payment_dues_location', $all_locations, null, [
                                        'class' => 'form-control select2 ',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'purchase_payment_dues_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped" id="purchase_payment_dues_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang('purchase.supplier')</th>
                                            <th>@lang('purchase.ref_no')</th>
                                            <th>@lang('home.due_amount')</th>
                                            <th class="not-export">@lang('messages.action')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('stock_report.view')
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon red">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>{{ __('home.product_stock_alert') }} @show_tooltip(__('tooltip.product_stock_alert'))</h3>
                                <p class="dava-panel-sub">Medicines running low across your stores</p>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('stock_alert_location', $all_locations, null, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'stock_alert_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped" id="stock_alert_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>@lang('sale.product')</th>
                                            <th>@lang('business.location')</th>
                                            <th>@lang('report.current_stock')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if (session('business.enable_product_expiry') == 1)
                        <div class="dava-panel lg:tw-col-span-1">
                            <div class="dava-panel-head">
                                <div class="dava-panel-icon amber">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <h3>{{ __('home.stock_expiry_alert') }} @show_tooltip(__('tooltip.stock_expiry_alert', ['days' => session('business.stock_expiry_alert_days', 30)]))</h3>
                                </div>
                            </div>
                            <div class="dava-panel-body tight">
                                <div class="tw-overflow-x-auto">
                                    <input type="hidden" id="stock_expiry_alert_days"
                                        value="{{ \Carbon::now()->addDays(session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                                    <table class="table table-bordered table-striped" id="stock_expiry_alert_table">
                                        <thead>
                                            <tr>
                                                <th>@lang('business.product')</th>
                                                <th>@lang('business.location')</th>
                                                <th>@lang('report.stock_left')</th>
                                                <th>@lang('product.expires_in')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endcan

                @if (auth()->user()->can('so.view_all') || auth()->user()->can('so.view_own'))
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>{{ __('lang_v1.sales_order') }}</h3>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('so_location', $all_locations, null, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'so_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped ajax_view" id="sales_order_table">
                                    <thead>
                                        <tr>
                                            <th class="not-export">@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('restaurant.order_no')</th>
                                            <th>@lang('sale.customer_name')</th>
                                            <th>@lang('lang_v1.contact_no')</th>
                                            <th>@lang('sale.location')</th>
                                            <th>@lang('sale.status')</th>
                                            <th>@lang('lang_v1.shipping_status')</th>
                                            <th>@lang('lang_v1.quantity_remaining')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if (
                    !empty($common_settings['enable_purchase_requisition']) &&
                        (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon sky">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="2" width="6" height="4" rx="1"/>
                                    <path d="M9 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/>
                                    <path d="M9 12h6M9 16h4"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>@lang('lang_v1.purchase_requisition')</h3>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('pr_location', $all_locations, null, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'pr_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped ajax_view" id="purchase_requisition_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="not-export">@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('purchase.ref_no')</th>
                                            <th>@lang('purchase.location')</th>
                                            <th>@lang('sale.status')</th>
                                            <th>@lang('lang_v1.required_by_date')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if (
                    !empty($common_settings['enable_purchase_order']) &&
                        (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon sky">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>@lang('lang_v1.purchase_order')</h3>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('po_location', $all_locations, null, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'po_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped ajax_view" id="purchase_order_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="not-export">@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('purchase.ref_no')</th>
                                            <th>@lang('purchase.location')</th>
                                            <th>@lang('purchase.supplier')</th>
                                            <th>@lang('sale.status')</th>
                                            <th>@lang('lang_v1.quantity_remaining')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->can('access_pending_shipments_only') ||
                        auth()->user()->can('access_shipping') ||
                        auth()->user()->can('access_own_shipping'))
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon orange">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>@lang('lang_v1.pending_shipments')</h3>
                            </div>
                            <div class="dava-panel-right">
                                @if (count($all_locations) > 1)
                                    {!! Form::select('pending_shipments_location', $all_locations, null, [
                                        'class' => 'form-control select2 ',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'pending_shipments_location',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped ajax_view" id="shipments_table">
                                    <thead>
                                        <tr>
                                            <th class="not-export">@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('sale.invoice_no')</th>
                                            <th>@lang('sale.customer_name')</th>
                                            <th>@lang('lang_v1.contact_no')</th>
                                            <th>@lang('sale.location')</th>
                                            <th>@lang('lang_v1.shipping_status')</th>
                                            @if (!empty($custom_labels['shipping']['custom_field_1']))
                                                <th>{{ $custom_labels['shipping']['custom_field_1'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_2']))
                                                <th>{{ $custom_labels['shipping']['custom_field_2'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_3']))
                                                <th>{{ $custom_labels['shipping']['custom_field_3'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_4']))
                                                <th>{{ $custom_labels['shipping']['custom_field_4'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_5']))
                                                <th>{{ $custom_labels['shipping']['custom_field_5'] }}</th>
                                            @endif
                                            <th>@lang('sale.payment_status')</th>
                                            <th>@lang('restaurant.service_staff')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
                    <div class="dava-panel lg:tw-col-span-2">
                        <div class="dava-panel-head">
                            <div class="dava-panel-icon green">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <h3>@lang('lang_v1.payment_recovered_today')</h3>
                            </div>
                        </div>
                        <div class="dava-panel-body tight">
                            <div class="tw-overflow-x-auto">
                                <table class="table table-bordered table-striped" id="cash_flow_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('account.account')</th>
                                            <th>@lang('lang_v1.description')</th>
                                            <th>@lang('lang_v1.payment_method')</th>
                                            <th>@lang('lang_v1.payment_details')</th>
                                            <th>@lang('account.credit')</th>
                                            <th>@lang('lang_v1.account_balance') @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                                            <th>@lang('lang_v1.total_balance') @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr class="bg-gray font-17 footer-total text-center">
                                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                            <td class="footer_total_credit"></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

</div>
</div>

@endsection


<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@section('css')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')
    @if (!empty($all_locations))
        {!! $sells_chart_1->script() !!}
        {!! $sells_chart_2->script() !!}
    @endif
    <script type="text/javascript">
        $(document).ready(function() {
            sales_order_table = $('#sales_order_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}?sale_type=sales_order',
                    "data": function(d) {
                        d.for_dashboard_sales_order = true;

                        if ($('#so_location').length > 0) {
                            d.location_id = $('#so_location').val();
                        }
                    }
                },
                columnDefs: [{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    {
                        data: 'so_qty_remaining',
                        name: 'so_qty_remaining',
                        "searchable": false
                    },
                    {
                        data: 'added_by',
                        name: 'u.first_name'
                    },
                ]
            });

            @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)

                // Cash Flow Table
                cash_flow_table = $('#cash_flow_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    "ajax": {
                        "url": "{{ action([\App\Http\Controllers\AccountController::class, 'cashFlow']) }}",
                        "data": function(d) {
                            d.type = 'credit';
                            d.only_payment_recovered = true;
                        }
                    },
                    "ordering": false,
                    "searching": false,
                    columns: [{
                            data: 'operation_date',
                            name: 'operation_date'
                        },
                        {
                            data: 'account_name',
                            name: 'account_name'
                        },
                        {
                            data: 'sub_type',
                            name: 'sub_type'
                        },
                        {
                            data: 'method',
                            name: 'TP.method'
                        },
                        {
                            data: 'payment_details',
                            name: 'payment_details',
                            searchable: false
                        },
                        {
                            data: 'credit',
                            name: 'amount'
                        },
                        {
                            data: 'balance',
                            name: 'balance'
                        },
                        {
                            data: 'total_balance',
                            name: 'total_balance'
                        },
                    ],
                    "fnDrawCallback": function(oSettings) {
                        __currency_convert_recursively($('#cash_flow_table'));
                    },
                    "footerCallback": function(row, data, start, end, display) {
                        var footer_total_credit = 0;

                        for (var r in data) {
                            footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(
                                data[r].credit).data('orig-value')) : 0;
                        }
                        $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                    }
                });
            @endif

            $('#so_location').change(function() {
                sales_order_table.ajax.reload();
            });
            @if (!empty($common_settings['enable_purchase_order']))
                //Purchase table
                purchase_order_table = $('#purchase_order_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseOrderController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#po_location').length > 0) {
                                d.location_id = $('#po_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'name',
                            name: 'contacts.name'
                        },
                        {
                            data: 'status',
                            name: 'transactions.status'
                        },
                        {
                            data: 'po_qty_remaining',
                            name: 'po_qty_remaining',
                            "searchable": false
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        }
                    ]
                })

                $('#po_location').change(function() {
                    purchase_order_table.ajax.reload();
                });
            @endif

            @if (!empty($common_settings['enable_purchase_requisition']))
                //Purchase table
                purchase_requisition_table = $('#purchase_requisition_table').DataTable({
                    processing: true,
                    serverSide: true,
                    fixedHeader:false,
                    aaSorting: [
                        [1, 'desc']
                    ],
                    scrollY: "75vh",
                    scrollX: true,
                    scrollCollapse: true,
                    ajax: {
                        url: '{{ action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']) }}',
                        data: function(d) {
                            d.from_dashboard = true;

                            if ($('#pr_location').length > 0) {
                                d.location_id = $('#pr_location').val();
                            }
                        },
                    },
                    columns: [{
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no'
                        },
                        {
                            data: 'location_name',
                            name: 'BS.name'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'delivery_date',
                            name: 'delivery_date'
                        },
                        {
                            data: 'added_by',
                            name: 'u.first_name'
                        },
                    ]
                })

                $('#pr_location').change(function() {
                    purchase_requisition_table.ajax.reload();
                });

                $(document).on('click', 'a.delete-purchase-requisition', function(e) {
                    e.preventDefault();
                    swal({
                        title: LANG.sure,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).attr('href');
                            $.ajax({
                                method: 'DELETE',
                                url: href,
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        purchase_requisition_table.ajax.reload();
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });
            @endif

            sell_table = $('#shipments_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader:false,
                aaSorting: [
                    [1, 'desc']
                ],
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                "ajax": {
                    "url": '{{ action([\App\Http\Controllers\SellController::class, 'index']) }}',
                    "data": function(d) {
                        d.only_pending_shipments = true;
                        if ($('#pending_shipments_location').length > 0) {
                            d.location_id = $('#pending_shipments_location').val();
                        }
                    }
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'conatct_name',
                        name: 'conatct_name'
                    },
                    {
                        data: 'mobile',
                        name: 'contacts.mobile'
                    },
                    {
                        data: 'business_location',
                        name: 'bl.name'
                    },
                    {
                        data: 'shipping_status',
                        name: 'shipping_status'
                    },
                    @if (!empty($custom_labels['shipping']['custom_field_1']))
                        {
                            data: 'shipping_custom_field_1',
                            name: 'shipping_custom_field_1'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_2']))
                        {
                            data: 'shipping_custom_field_2',
                            name: 'shipping_custom_field_2'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_3']))
                        {
                            data: 'shipping_custom_field_3',
                            name: 'shipping_custom_field_3'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_4']))
                        {
                            data: 'shipping_custom_field_4',
                            name: 'shipping_custom_field_4'
                        },
                    @endif
                    @if (!empty($custom_labels['shipping']['custom_field_5']))
                        {
                            data: 'shipping_custom_field_5',
                            name: 'shipping_custom_field_5'
                        },
                    @endif {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'waiter',
                        name: 'ss.first_name',
                        @if (empty($is_service_staff_enabled))
                            visible: false
                        @endif
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(4)').attr('class', 'clickable_td');
                }
            });

            $('#pending_shipments_location').change(function() {
                sell_table.ajax.reload();
            });
        });
    </script>

@endsection
