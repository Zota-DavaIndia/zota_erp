@extends('layouts.auth2')

@section('title', __('lang_v1.reset_password'))

@section('content')

    <a href="{{ route('login') }}" class="dava-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Back to sign in
    </a>

    <h1 class="dava-form-title">@lang('lang_v1.reset_password')</h1>
    <p class="dava-form-sub">Choose a strong new password to keep your DavaIndia pharmacy account secure.</p>

    @if ($errors->any())
        <div class="dava-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="dava-field">
            <label class="dava-label" for="email">@lang('Email')</label>
            <div class="dava-input-wrap">
                <span class="dava-input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input id="email" type="email" class="dava-input" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="@lang('lang_v1.email_address')" />
            </div>
            @if ($errors->has('email'))
                <span class="dava-help-block"><strong>{{ $errors->first('email') }}</strong></span>
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
                <input id="password" type="password" class="dava-input" name="password" required placeholder="@lang('lang_v1.password')" style="padding-right:48px;" />
                <button type="button" class="dava-toggle-pass" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.8" stroke="#9CA3AF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password'))
                <span class="dava-help-block"><strong>{{ $errors->first('password') }}</strong></span>
            @endif
        </div>

        <div class="dava-field">
            <label class="dava-label" for="password_confirmation">@lang('business.confirm_password')</label>
            <div class="dava-input-wrap">
                <span class="dava-input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </span>
                <input id="password_confirmation" type="password" class="dava-input" name="password_confirmation" required placeholder="@lang('business.confirm_password')" style="padding-right:48px;" />
                <button type="button" class="dava-toggle-pass" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle password visibility">
                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.8" stroke="#9CA3AF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password_confirmation'))
                <span class="dava-help-block"><strong>{{ $errors->first('password_confirmation') }}</strong></span>
            @endif
        </div>

        <button type="submit" class="dava-btn" style="margin-top:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M9 12l2 2 4-4"/>
            </svg>
            <span>@lang('lang_v1.reset_password')</span>
        </button>
    </form>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('.eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>';
            }
        }
    </script>
@endsection
