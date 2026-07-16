@extends('layouts.auth2')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')
@section('content')
    @php
        $username = old('username');
        $password = null;
        if (config('app.env') == 'demo') {
            $username = 'admin';
            $password = '123456';

            $demo_types = [
                'all_in_one' => 'admin',
                'super_market' => 'admin',
                'pharmacy' => 'admin-pharmacy',
                'electronics' => 'admin-electronics',
                'services' => 'admin-services',
                'restaurant' => 'admin-restaurant',
                'superadmin' => 'superadmin',
                'woocommerce' => 'woocommerce_user',
                'essentials' => 'admin-essentials',
                'manufacturing' => 'manufacturer-demo',
            ];

            if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
                $username = $demo_types[$_GET['demo_type']];
            }
        }
    @endphp

    <h1 class="dava-form-title">@lang('lang_v1.welcome_back')</h1>
    <p class="dava-form-sub">@lang('lang_v1.login_to_your') {{ config('app.name', 'DavaIndia') }} pharmacy account</p>

    @if ($errors->any())
        <div class="dava-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    @if (session('status'))
        <div class="dava-alert {{ session('status.success') == 0 ? 'error' : 'success' }}">
            {{ session('status.msg') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form">
        {{ csrf_field() }}

        <div class="dava-field">
            <label class="dava-label" for="username">@lang('lang_v1.username')</label>
            <div class="dava-input-wrap">
                <span class="dava-input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <input id="username" type="text" name="username" required autofocus
                    placeholder="@lang('lang_v1.username')" value="{{ $username }}" class="dava-input" />
            </div>
            @if ($errors->has('username'))
                <span class="dava-help-block"><strong>{{ $errors->first('username') }}</strong></span>
            @endif
        </div>

        <div class="dava-field">
            <label class="dava-label" for="password">@lang('lang_v1.password')</label>
            <div class="dava-input-wrap">
                <span class="dava-input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input id="password" type="password" name="password" value="{{ $password }}" required
                    placeholder="@lang('lang_v1.password')" class="dava-input" style="padding-right:48px;" />
                <button type="button" id="show_hide_icon" class="dava-toggle-pass" aria-label="Toggle password visibility">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" id="eye-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.8" stroke="#9CA3AF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password'))
                <span class="dava-help-block"><strong>{{ $errors->first('password') }}</strong></span>
            @endif
        </div>

        <div class="dava-row">
            <label class="dava-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="dava-check-mark">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </span>
                <span class="dava-check-label">@lang('lang_v1.remember_me')</span>
            </label>

            @if (config('app.env') != 'demo')
                <a href="{{ route('password.request') }}" class="dava-link">@lang('lang_v1.forgot_your_password')</a>
            @endif
        </div>

        @if(config('constants.enable_recaptcha'))
            <div class="dava-field">
                <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                @if ($errors->has('g-recaptcha-response'))
                    <span class="dava-help-block">{{ $errors->first('g-recaptcha-response') }}</span>
                @endif
            </div>
        @endif

        <button type="submit" class="dava-btn">
            <span>@lang('lang_v1.login')</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>

        @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
            @if (config('constants.allow_registration'))
                <div class="dava-footer">
                    {{ __('business.not_yet_registered') }}
                    <a href="{{ route('business.getRegister') }}@if (!empty(request()->lang)) {{ '?lang=' . request()->lang }} @endif">{{ __('business.register_now') }}</a>
                </div>
            @endif
        @endif
    </form>

    @if (config('app.env') == 'demo')
        <div style="margin-top:24px;padding:16px;background:#FFF8F2;border:1px dashed #F26A21;border-radius:12px;">
            <p style="margin:0 0 10px 0;font-size:12.5px;font-weight:700;color:#D85A14;text-transform:uppercase;letter-spacing:.8px;">Demo Shops &mdash; one click login</p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                <a href="?demo_type=all_in_one" class="demo-login" data-admin="{{ $demo_types['all_in_one'] }}" style="font-size:12px;padding:6px 10px;background:#1F7A4D;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">All In One</a>
                <a href="?demo_type=pharmacy" class="demo-login" data-admin="{{ $demo_types['pharmacy'] }}" style="font-size:12px;padding:6px 10px;background:#0F4D2E;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Pharmacy</a>
                <a href="?demo_type=services" class="demo-login" data-admin="{{ $demo_types['services'] }}" style="font-size:12px;padding:6px 10px;background:#F26A21;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Services</a>
                <a href="?demo_type=super_market" class="demo-login" data-admin="{{ $demo_types['super_market'] }}" style="font-size:12px;padding:6px 10px;background:#1F7A4D;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Super Market</a>
                <a href="?demo_type=restaurant" class="demo-login" data-admin="{{ $demo_types['restaurant'] }}" style="font-size:12px;padding:6px 10px;background:#D85A14;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Restaurant</a>
                <a href="?demo_type=superadmin" class="demo-login" data-admin="{{ $demo_types['superadmin'] }}" style="font-size:12px;padding:6px 10px;background:#374151;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Superadmin</a>
            </div>
        </div>
    @endif
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_lang').click(function() {
                window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
            });
            $('a.demo-login').click(function(e) {
                e.preventDefault();
                $('#username').val($(this).data('admin'));
                $('#password').val("{{ $password }}");
                $('form#login-form').submit();
            });

            $('#show_hide_icon').on('click', function(e) {
                e.preventDefault();
                const passwordInput = $('#password');
                const icon = $('#eye-icon');
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.html('<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/>');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.html('<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>');
                }
            });
        })
    </script>
@endsection
