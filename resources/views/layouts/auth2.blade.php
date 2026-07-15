<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    @include('layouts.partials.css')

    @include('layouts.partials.extracss_auth')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>

</head>

<body class="pace-done" data-new-gr-c-s-check-loaded="14.1172.0" data-gr-ext-installed="" cz-shortcut-listen="true">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif

    <div class="dava-auth-wrap">
        <!-- Left: Dava India Brand Panel -->
        <div class="dava-auth-left">
            <div class="dava-pill dava-pill-1"></div>
            <div class="dava-pill dava-pill-2"></div>
            <div class="dava-pill dava-pill-3"></div>
            <div class="dava-pill dava-pill-4"></div>
            <div class="dava-cross dava-cross-1">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v9h18v-9c0-1.1-.9-2-2-2zm-2 0H7V5h10v3zm-5 9h-2v-3h2v3z"/></svg>
            </div>
            <div class="dava-cross dava-cross-2">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v9h18v-9c0-1.1-.9-2-2-2zm-2 0H7V5h10v3zm-5 9h-2v-3h2v3z"/></svg>
            </div>

            <div class="dava-auth-left-content">
                <div class="dava-logo-block">
                    <div class="dava-logo-mark">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                            <path d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v9h18v-9c0-1.1-.9-2-2-2z" fill="#1F7A4D"/>
                            <path d="M11 11h2v2h-2zm0 3h2v2h-2z" fill="#fff"/>
                        </svg>
                    </div>
                    <div class="dava-logo-text">
                        <div class="dava-name">DavaIndia</div>
                        <div class="dava-tag">Generic Pharmacy</div>
                    </div>
                </div>

                <h1 class="dava-headline">
                    Healthcare made <span class="accent">smarter</span>,<br>
                    medicines made <span class="accent">affordable</span>.
                </h1>

                <p class="dava-sub">
                    Manage your pharmacy chain with India's most trusted POS &mdash; from inventory to billing, all in one place.
                </p>

                <div class="dava-features">
                    <div class="dava-feature">
                        <div class="dava-feature-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.5 20H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v3"/>
                                <path d="M14 4v4h4M16 17h.01M19 14h.01M19 20h.01M14 17h.01"/>
                            </svg>
                        </div>
                        <div class="dava-feature-text">
                            <div class="title">Genuine Medicines</div>
                            <div class="desc">100% quality-checked & approved products</div>
                        </div>
                    </div>

                    <div class="dava-feature">
                        <div class="dava-feature-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        </div>
                        <div class="dava-feature-text">
                            <div class="title">Same-Day Delivery</div>
                            <div class="desc">Right to your doorstep across 1000+ stores</div>
                        </div>
                    </div>

                    <div class="dava-feature">
                        <div class="dava-feature-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <div class="dava-feature-text">
                            <div class="title">Medicine Price Shield</div>
                            <div class="desc">Up to 60% savings vs market price</div>
                        </div>
                    </div>

                    <div class="dava-feature">
                        <div class="dava-feature-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                        </div>
                        <div class="dava-feature-text">
                            <div class="title">Priority Pharmacist Support</div>
                            <div class="desc">Trained experts, always ready to help</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div class="dava-auth-right">
            <div class="dava-auth-card">
                <!-- Mobile-only logo -->
                <div class="dava-form-logo">
                    <div class="dava-logo-mark" style="width:48px;height:48px;border-radius:14px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path d="M19 8h-2V3H7v5H5c-1.1 0-2 .9-2 2v9h18v-9c0-1.1-.9-2-2-2z" fill="#1F7A4D"/>
                            <path d="M11 11h2v2h-2zm0 3h2v2h-2z" fill="#fff"/>
                        </svg>
                    </div>
                    <div class="dava-logo-text">
                        <div class="dava-name" style="color:#0F2A1C;font-size:22px;">DavaIndia</div>
                        <div class="dava-tag" style="color:#F26A21;">Generic Pharmacy</div>
                    </div>
                </div>

                @yield('content')

                <div style="margin-top:32px;padding-top:20px;border-top:1px solid #F0F2F5;text-align:center;">
                    <p style="font-size:12px;color:#9CA3AF;margin:0;">
                        &copy; {{ date('Y') }} DavaIndia &middot; Generic Pharmacy Chain &middot; All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>

    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_register').select2();
        });
    </script>
    <style>
        .wizard>.content {
            background-color: white !important;
        }
    </style>
</body>

</html>
