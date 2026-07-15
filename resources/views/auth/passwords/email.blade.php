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

    <h1 class="dava-form-title">Forgot your password?</h1>
    <p class="dava-form-sub">No worries &mdash; enter the email address associated with your DavaIndia pharmacy account and we'll send you a reset link.</p>

    @if (session('status') && is_string(session('status')))
        <div class="dava-alert success">
            <svg style="vertical-align:middle;margin-right:6px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="dava-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <div class="dava-field">
            <label class="dava-label" for="email">@lang('Email')</label>
            <div class="dava-input-wrap">
                <span class="dava-input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input id="email" type="email" class="dava-input" name="email" value="{{ old('email') }}" required autofocus placeholder="@lang('lang_v1.email_address')" />
            </div>
            @if ($errors->has('email'))
                <span class="dava-help-block"><strong>{{ $errors->first('email') }}</strong></span>
            @endif
        </div>

        <button type="submit" class="dava-btn orange" style="margin-top:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
            <span>@lang('lang_v1.send_password_reset_link')</span>
        </button>

        <div class="dava-divider">or</div>

        <div class="dava-footer" style="margin-top:0;">
            Remember your password?
            <a href="{{ route('login') }}">Sign in instead</a>
        </div>
    </form>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_lang').click(function() {
                window.location = "{{ route('password.request') }}?lang=" + $(this).attr('value');
            });
        })
    </script>
@endsection
