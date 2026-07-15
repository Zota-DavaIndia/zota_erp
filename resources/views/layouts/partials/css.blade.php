<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

@php
    $themeColor = session('business.theme_color', 'primary');
    $themeColorMap = [
        'primary' => ['700' => '#004EEB', '800' => '#0040C1', '900' => '#00359E'],
        'indigo'  => ['700' => '#4338CA', '800' => '#3730A3', '900' => '#312E81'],
        'violet'  => ['700' => '#6D28D9', '800' => '#5B21B6', '900' => '#4C1D95'],
        'purple'  => ['700' => '#5925DC', '800' => '#4A1FB8', '900' => '#3E1C96'],
        'teal'    => ['700' => '#0F766E', '800' => '#115E59', '900' => '#134E4A'],
        'emerald' => ['700' => '#047857', '800' => '#065F46', '900' => '#064E3B'],
        'green'   => ['700' => '#067647', '800' => '#085D3A', '900' => '#074D31'],
        'sky'     => ['700' => '#026AA2', '800' => '#065986', '900' => '#0B4A6F'],
        'pink'    => ['700' => '#BE185D', '800' => '#9D174D', '900' => '#831843'],
        'rose'    => ['700' => '#BE123C', '800' => '#9F1239', '900' => '#881337'],
        'red'     => ['700' => '#B42318', '800' => '#912018', '900' => '#7A271A'],
        'orange'  => ['700' => '#B93815', '800' => '#932F19', '900' => '#772917'],
        'yellow'  => ['700' => '#B54708', '800' => '#93370D', '900' => '#7A2E0E'],
        'slate'   => ['700' => '#334155', '800' => '#1E293B', '900' => '#0F172A'],
    ];
    $tc = $themeColorMap[$themeColor] ?? $themeColorMap['primary'];
@endphp
<style>
    :root {
        --theme-700: {{ $tc['700'] }};
        --theme-800: {{ $tc['800'] }};
        --theme-900: {{ $tc['900'] }};
        /* Dava India brand colors */
        --dava-green: #1F7A4D;
        --dava-green-dark: #0F4D2E;
        --dava-green-light: #2D9D6A;
        --dava-green-soft: #E8F5EE;
        --dava-orange: #F26A21;
        --dava-orange-dark: #D85A14;
        --dava-orange-light: #FFE9DA;
    }
    .theme-header-bg {
        background-image: linear-gradient(to right, var(--theme-800), var(--theme-900));
    }
    .theme-btn-bg {
        background-color: var(--theme-800);
    }
    .theme-btn-bg:hover {
        background-color: var(--theme-700);
    }
    .theme-btn-bg:active,
    .theme-btn-bg:focus,
    .theme-btn-bg:focus-visible {
        background-color: var(--theme-900);
        color: #fff;
        outline: 2px solid color-mix(in srgb, var(--theme-700) 40%, transparent);
        outline-offset: 0px;
    }
    .theme-logo-bg {
        background-color: var(--theme-800);
    }
    #side-bar a svg, #side-bar a i {
        color: #6B7280;
    }
    #side-bar a:hover svg, #side-bar a:hover i,
    #side-bar a.theme-sidebar-active svg, #side-bar a.theme-sidebar-active i {
        color: var(--dava-green);
    }
    #side-bar .theme-sidebar-hover:hover,
    #side-bar .theme-sidebar-hover:active,
    #side-bar .theme-sidebar-hover:focus {
        background-color: var(--dava-green-soft);
        color: var(--dava-green-dark);
        outline: none;
    }
    #side-bar .theme-sidebar-active {
        background-color: var(--dava-green-soft);
        color: var(--dava-green-dark);
        font-weight: 600;
        box-shadow: inset 3px 0 0 0 var(--dava-orange);
    }
    #side-bar .theme-sidebar-child-hover:hover,
    #side-bar .theme-sidebar-child-hover:active,
    #side-bar .theme-sidebar-child-hover:focus {
        color: var(--dava-green);
        outline: none;
    }
    #side-bar .theme-sidebar-child-active {
        color: var(--dava-green);
        font-weight: 600;
    }

    /* ========== DAVA INDIA SIDEBAR THEME ========== */
    .dava-side-bar {
        background: #FFFFFF;
        border-right: 1px solid #E5E7EB;
        box-shadow: 4px 0 24px rgba(15, 77, 46, 0.05);
    }
    .dava-side-bar-header {
        background: linear-gradient(135deg, var(--dava-green) 0%, var(--dava-green-dark) 100%);
        position: relative;
        overflow: hidden;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dava-side-bar-header::before {
        content: "";
        position: absolute;
        top: -30px; right: -20px;
        width: 100px; height: 100px;
        background: rgba(242, 106, 33, 0.18);
        border-radius: 50%;
        filter: blur(4px);
        pointer-events: none;
    }
    .dava-side-bar-header::after {
        content: "";
        position: absolute;
        bottom: -30px; left: -20px;
        width: 90px; height: 90px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        filter: blur(6px);
        pointer-events: none;
    }
    .dava-side-bar-logo {
        width: 40px; height: 40px;
        background: #FFFFFF;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }
    .dava-side-bar-text { line-height: 1.1; position: relative; z-index: 2; min-width: 0; }
    .dava-side-bar-name {
        font-size: 16px; font-weight: 800; color: #FFFFFF;
        letter-spacing: -0.3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .dava-side-bar-tag {
        font-size: 9.5px; font-weight: 700; color: var(--dava-orange);
        letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px;
    }
    .dava-side-bar-status {
        position: relative; z-index: 2;
        width: 8px; height: 8px; background: #22C55E; border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
        flex-shrink: 0; margin-left: auto;
    }

    .dava-side-bar-search {
        padding: 12px 14px 8px 14px;
        border-bottom: 1px solid #F0F2F5;
    }
    .dava-side-bar-search-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #F5FAF7;
        border: 1.5px solid #E5E7EB;
        border-radius: 10px;
        transition: all .2s ease;
    }
    .dava-side-bar-search-inner:focus-within {
        background: #FFFFFF;
        border-color: var(--dava-green);
        box-shadow: 0 0 0 3px rgba(31, 122, 77, 0.12);
    }
    .dava-side-bar-search-inner svg { color: #9CA3AF; flex-shrink: 0; }
    .dava-side-bar-search-inner input {
        flex: 1; min-width: 0;
        background: transparent;
        border: none; outline: none;
        font-size: 13px; color: #0F2A1C;
    }
    .dava-side-bar-search-inner input::placeholder { color: #9CA3AF; }

    /* Sidebar menu container */
    #side-bar {
        padding: 12px 12px 16px 12px;
        background: #FFFFFF;
    }
    .dava-menu-section-title {
        padding: 14px 12px 6px 12px;
        font-size: 10.5px;
        font-weight: 700;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }
    .dava-menu-section-title:not(:first-child) {
        margin-top: 6px;
    }

    /* Menu links - top level */
    #side-bar a.theme-sidebar-hover,
    #side-bar a.drop_down {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        font-size: 13.5px;
        font-weight: 500;
        color: #4B5563;
        border-radius: 10px;
        transition: all .2s ease;
        white-space: nowrap;
        margin: 1px 0;
    }
    #side-bar a.theme-sidebar-hover .menu-icon-wrap,
    #side-bar a.drop_down .menu-icon-wrap {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: #F3F4F6;
        color: #6B7280;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all .25s ease;
    }
    #side-bar a.theme-sidebar-hover svg,
    #side-bar a.theme-sidebar-hover i,
    #side-bar a.drop_down svg,
    #side-bar a.drop_down i {
        color: #6B7280;
        transition: color .25s ease;
    }
    #side-bar a.theme-sidebar-hover:hover,
    #side-bar a.drop_down:hover {
        background: var(--dava-green-soft);
        color: var(--dava-green-dark);
        transform: translateX(2px);
    }
    #side-bar a.theme-sidebar-hover:hover .menu-icon-wrap,
    #side-bar a.drop_down:hover .menu-icon-wrap {
        background: #FFFFFF;
        color: var(--dava-green);
        box-shadow: 0 2px 8px rgba(31, 122, 77, 0.12);
    }
    #side-bar a.theme-sidebar-hover:hover svg,
    #side-bar a.theme-sidebar-hover:hover i,
    #side-bar a.drop_down:hover svg,
    #side-bar a.drop_down:hover i {
        color: var(--dava-green);
    }
    /* Active state */
    #side-bar a.theme-sidebar-active,
    #side-bar a.drop_down.theme-sidebar-active {
        background: linear-gradient(95deg, var(--dava-green-soft) 0%, #F5FAF7 100%);
        color: var(--dava-green-dark);
        font-weight: 600;
    }
    #side-bar a.theme-sidebar-active::before,
    #side-bar a.drop_down.theme-sidebar-active::before {
        content: "";
        position: absolute;
        left: 0; top: 8px; bottom: 8px;
        width: 3px;
        background: linear-gradient(180deg, var(--dava-green) 0%, var(--dava-orange) 100%);
        border-radius: 0 4px 4px 0;
    }
    #side-bar a.theme-sidebar-active .menu-icon-wrap,
    #side-bar a.drop_down.theme-sidebar-active .menu-icon-wrap {
        background: linear-gradient(135deg, var(--dava-green) 0%, var(--dava-green-dark) 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(31, 122, 77, 0.3);
    }
    #side-bar a.theme-sidebar-active svg,
    #side-bar a.theme-sidebar-active i,
    #side-bar a.drop_down.theme-sidebar-active svg,
    #side-bar a.drop_down.theme-sidebar-active i {
        color: #FFFFFF;
    }
    /* Caret */
    #side-bar a.drop_down .svg {
        margin-left: auto;
        color: #9CA3AF;
        transition: transform .25s ease, color .25s ease;
    }
    #side-bar a.drop_down.theme-sidebar-active .svg {
        color: var(--dava-green);
        transform: rotate(90deg);
    }

    /* Child menu */
    #side-bar .chiled {
        position: relative;
        margin: 4px 0 6px 0;
        padding-left: 44px;
    }
    #side-bar .chiled::before {
        content: "";
        position: absolute;
        left: 19px; top: 0; bottom: 6px;
        width: 2px;
        background: linear-gradient(180deg, var(--dava-green) 0%, var(--dava-green-soft) 100%);
        opacity: 0.4;
        border-radius: 2px;
    }
    #side-bar .chiled .tw-space-y-1 > a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        font-size: 12.5px;
        font-weight: 500;
        color: #6B7280;
        border-radius: 8px;
        transition: all .2s ease;
        position: relative;
    }
    #side-bar .chiled .tw-space-y-1 > a::before {
        content: "";
        width: 6px; height: 6px;
        background: #D1D5DB;
        border-radius: 50%;
        flex-shrink: 0;
        transition: all .2s ease;
    }
    #side-bar .chiled .tw-space-y-1 > a:hover {
        background: var(--dava-green-soft);
        color: var(--dava-green-dark);
    }
    #side-bar .chiled .tw-space-y-1 > a:hover::before {
        background: var(--dava-green);
        box-shadow: 0 0 0 3px rgba(31, 122, 77, 0.15);
    }
    #side-bar .chiled .tw-space-y-1 > a.theme-sidebar-child-active {
        background: var(--dava-green-soft);
        color: var(--dava-green-dark);
        font-weight: 600;
    }
    #side-bar .chiled .tw-space-y-1 > a.theme-sidebar-child-active::before {
        background: var(--dava-orange);
        box-shadow: 0 0 0 3px rgba(242, 106, 33, 0.18);
    }

    /* Scrollbar */
    #side-bar::-webkit-scrollbar { width: 6px; }
    #side-bar::-webkit-scrollbar-track { background: transparent; }
    #side-bar::-webkit-scrollbar-thumb { background: #D1FAE5; border-radius: 3px; }
    #side-bar::-webkit-scrollbar-thumb:hover { background: var(--dava-green); }

    /* Footer area */
    .dava-side-bar-footer {
        padding: 12px 16px;
        border-top: 1px solid #F0F2F5;
        background: linear-gradient(180deg, #FFFFFF 0%, #F5FAF7 100%);
    }
    .dava-side-bar-footer-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: var(--dava-green-soft);
        border: 1px solid #D1FAE5;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--dava-green-dark);
    }
    .dava-side-bar-footer-badge::before {
        content: "";
        width: 6px; height: 6px;
        background: var(--dava-green);
        border-radius: 50%;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .dava-side-bar-header { padding: 14px 14px; }
    }
    @media (max-width: 768px) {
        .dava-side-bar-header { padding: 12px 12px; }
        .dava-side-bar-name { font-size: 14px; }
        .dava-side-bar-search { padding: 10px 12px 6px 12px; }
    }
</style>

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif

