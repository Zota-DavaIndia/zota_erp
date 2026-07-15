   <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /* ================================
           DAVA INDIA PHARMACY THEME
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

        html {
            height: 100%;
            background: linear-gradient(135deg, #f5faf7 0%, #e8f5ee 100%);
        }

        body {
            min-height: 100vh;
            background: transparent;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        h1 { color: #fff; }

        /* Split layout */
        .dava-auth-wrap {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .dava-auth-left {
            flex: 1.1;
            background: linear-gradient(135deg, #1F7A4D 0%, #0F4D2E 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
            color: #fff;
        }

        .dava-auth-left::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 360px;
            height: 360px;
            background: rgba(242, 106, 33, 0.18);
            border-radius: 50%;
            filter: blur(10px);
        }

        .dava-auth-left::after {
            content: "";
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 420px;
            height: 420px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            filter: blur(10px);
        }

        .dava-pill {
            position: absolute;
            border-radius: 999px;
            opacity: 0.18;
            background: #fff;
        }

        .dava-pill-1 { top: 12%; left: 8%; width: 140px; height: 38px; transform: rotate(-25deg); }
        .dava-pill-2 { top: 28%; right: 6%; width: 90px; height: 28px; transform: rotate(35deg); background: var(--dava-orange); opacity: 0.35; }
        .dava-pill-3 { bottom: 18%; left: 12%; width: 110px; height: 32px; transform: rotate(15deg); }
        .dava-pill-4 { bottom: 8%; right: 14%; width: 70px; height: 22px; transform: rotate(-40deg); background: var(--dava-orange); opacity: 0.4; }

        .dava-cross {
            position: absolute;
            opacity: 0.10;
            color: #fff;
        }

        .dava-cross-1 { top: 60%; right: 22%; font-size: 80px; transform: rotate(15deg); }
        .dava-cross-2 { top: 20%; left: 25%; font-size: 50px; transform: rotate(-10deg); }

        .dava-auth-left-content {
            position: relative;
            z-index: 2;
            max-width: 480px;
        }

        .dava-logo-block {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }

        .dava-logo-mark {
            width: 64px;
            height: 64px;
            background: #fff;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }

        .dava-logo-text { line-height: 1.1; }
        .dava-logo-text .dava-name { font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .dava-logo-text .dava-tag { font-size: 11px; font-weight: 600; color: var(--dava-orange); letter-spacing: 2.5px; text-transform: uppercase; }

        .dava-headline {
            font-size: 44px;
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 16px 0;
            color: #fff;
            letter-spacing: -1px;
        }

        .dava-headline .accent { color: var(--dava-orange); }

        .dava-sub {
            font-size: 17px;
            line-height: 1.6;
            color: rgba(255,255,255,0.85);
            margin-bottom: 36px;
        }

        .dava-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .dava-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 14px 18px;
            border-radius: 14px;
            backdrop-filter: blur(8px);
            transition: all .25s ease;
        }

        .dava-feature:hover { background: rgba(255,255,255,0.14); transform: translateX(4px); }

        .dava-feature-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--dava-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .dava-feature-text { line-height: 1.3; }
        .dava-feature-text .title { font-size: 15px; font-weight: 700; color: #fff; }
        .dava-feature-text .desc { font-size: 12.5px; color: rgba(255,255,255,0.75); }

        .dava-auth-right {
            flex: 1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            position: relative;
        }

        .dava-auth-card {
            width: 100%;
            max-width: 440px;
        }

        .dava-form-logo {
            display: none;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .dava-form-title {
            font-size: 30px;
            font-weight: 800;
            color: #0F2A1C;
            margin: 0 0 6px 0;
            letter-spacing: -0.6px;
        }

        .dava-form-sub {
            font-size: 14.5px;
            color: #6B7280;
            margin-bottom: 32px;
        }

        .dava-field { margin-bottom: 18px; }

        .dava-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0F2A1C;
            margin-bottom: 8px;
        }

        .dava-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .dava-input-icon {
            position: absolute;
            left: 16px;
            color: #9CA3AF;
            pointer-events: none;
        }

        .dava-input {
            width: 100%;
            height: 52px;
            border: 1.5px solid #E5E7EB;
            border-radius: 12px;
            padding: 0 16px 0 46px;
            font-size: 15px;
            color: #0F2A1C;
            background: #F9FAFB;
            transition: all .2s ease;
            outline: none;
            font-weight: 500;
        }

        .dava-input::placeholder { color: #B0B5BD; font-weight: 400; }

        .dava-input:focus {
            border-color: var(--dava-green);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(31, 122, 77, 0.12);
        }

        .dava-toggle-pass {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
        }

        .dava-toggle-pass:hover { color: var(--dava-green); }

        .dava-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 8px 0 22px 0;
        }

        .dava-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .dava-check input { display: none; }

        .dava-check-mark {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #D1D5DA;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
            background: #fff;
        }

        .dava-check input:checked + .dava-check-mark {
            background: var(--dava-green);
            border-color: var(--dava-green);
        }

        .dava-check input:checked + .dava-check-mark svg { display: block; }

        .dava-check-mark svg { display: none; color: #fff; }

        .dava-check-label { font-size: 13.5px; color: #4B5563; font-weight: 500; }

        .dava-link {
            color: var(--dava-green);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }

        .dava-link:hover { color: var(--dava-orange); }

        .dava-btn {
            width: 100%;
            height: 52px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--dava-green) 0%, var(--dava-green-dark) 100%);
            color: #fff;
            font-size: 15.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all .25s ease;
            box-shadow: 0 8px 20px rgba(31, 122, 77, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .dava-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(31, 122, 77, 0.35);
        }

        .dava-btn:active { transform: translateY(0); }

        .dava-btn.orange {
            background: linear-gradient(135deg, var(--dava-orange) 0%, var(--dava-orange-dark) 100%);
            box-shadow: 0 8px 20px rgba(242, 106, 33, 0.28);
        }

        .dava-btn.orange:hover { box-shadow: 0 12px 28px rgba(242, 106, 33, 0.4); }

        .dava-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #9CA3AF;
            font-size: 12px;
            font-weight: 500;
            margin: 24px 0;
        }

        .dava-divider::before, .dava-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #E5E7EB;
        }

        .dava-footer {
            text-align: center;
            font-size: 13.5px;
            color: #6B7280;
            margin-top: 24px;
        }

        .dava-footer a { color: var(--dava-orange); font-weight: 700; text-decoration: none; }
        .dava-footer a:hover { text-decoration: underline; }

        .dava-alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 18px;
        }

        .dava-alert.success { background: var(--dava-green-soft); color: var(--dava-green-dark); border: 1px solid rgba(31, 122, 77, 0.18); }
        .dava-alert.error { background: #FEE2E2; color: #991B1B; border: 1px solid rgba(220, 38, 38, 0.18); }

        .dava-help-block {
            color: #DC2626;
            font-size: 12.5px;
            font-weight: 500;
            margin-top: 6px;
            display: block;
        }

        .dava-top-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 30px;
            z-index: 10;
        }

        .dava-lang-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 999px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .dava-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #6B7280;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 24px;
            transition: color .2s;
        }

        .dava-back-link:hover { color: var(--dava-green); }

        /* Responsive */
        @media (max-width: 900px) {
            .dava-auth-wrap { flex-direction: column; }
            .dava-auth-left { padding: 40px 24px; min-height: 280px; }
            .dava-headline { font-size: 28px; }
            .dava-sub { font-size: 14px; }
            .dava-features { display: none; }
            .dava-form-logo { display: flex; }
            .dava-auth-right { padding: 30px 24px; }
            .dava-form-title { font-size: 24px; }
        }
    </style>
