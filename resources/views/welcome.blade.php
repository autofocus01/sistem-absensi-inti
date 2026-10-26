<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Absensi &amp; Lembur — PT. INTI</title>

    <script>
        (function () {
            const theme = localStorage.getItem('inti-theme') || 'system';
            const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50,-20,200&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/p1-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/p1-responsive-enterprise.css') }}">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    @include('partials.tailwind-config')

    <style>
        .welcome-hero {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 48px 24px 32px;
            background:
                radial-gradient(1200px 500px at 50% -10%, rgba(15, 41, 66, .06), transparent 60%),
                var(--inti-background);
        }
        .welcome-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            margin-bottom: 18px;
        }
        .welcome-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -.02em;
            color: var(--inti-primary);
        }
        .welcome-subtitle {
            margin: 14px auto 0;
            max-width: 560px;
            font-size: 15px;
            line-height: 24px;
            color: var(--inti-muted);
        }
        .welcome-cta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 28px;
        }
        .welcome-features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            max-width: 880px;
            margin: 40px auto 0;
        }
        .welcome-feature {
            text-align: left;
            padding: 18px;
        }
        .welcome-feature .material-symbols-outlined {
            font-size: 22px;
            color: var(--inti-secondary);
        }
        .welcome-feature h3 {
            margin: 10px 0 4px;
            font-size: 14px;
            line-height: 20px;
            font-weight: 700;
            color: var(--inti-primary);
        }
        .welcome-feature p {
            margin: 0;
            font-size: 12px;
            line-height: 18px;
            color: var(--inti-muted);
        }
        .welcome-footer {
            margin-top: 36px;
            font-size: 11px;
            line-height: 16px;
            color: var(--inti-muted);
        }
        @media (max-width: 767px) {
            .welcome-features { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<main class="welcome-hero">
    <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="welcome-logo">

    <span class="inti-eyebrow">Sistem Informasi Presensi &amp; Lembur</span>
    <h1 class="welcome-title">Selamat datang di Portal Internal</h1>
    <p class="welcome-subtitle">
        — semua dalam satu portal terpadu —
    </p>

    <div class="welcome-cta">
        @auth
            <a href="{{ url('/dashboard') }}" class="inti-btn inti-btn-primary">
                <span class="material-symbols-outlined" style="font-size:18px;">dashboard</span>
                Buka Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="inti-btn inti-btn-primary">
                <span class="material-symbols-outlined" style="font-size:18px;">login</span>
                <!-- Masuk ke Portal -->
            </a>
            <!-- @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inti-btn inti-btn-secondary">
                    Buat Akun
                </a>
            @endif -->
        @endauth
    </div>

    <div class="welcome-features">
        <div class="inti-card welcome-feature">
            <span class="material-symbols-outlined">SCHEDULE</span>
            <h3>Presensi &amp; Timesheet</h3>
            <p>Catat clock in/out, riwayat kehadiran, dan rekap otomatis.</p>
        </div>
        <div class="inti-card welcome-feature">
            <span class="material-symbols-outlined">TIMER</span>
            <h3>Pengajuan Lembur</h3>
            <p>Ajukan lembur, disetujui VP, diverifikasi HR dengan aturan yang jelas.</p>
        </div>
        <div class="inti-card welcome-feature">
            <span class="material-symbols-outlined">INSIGHTS</span>
            <h3>Dashboard &amp; Laporan</h3>
            <p>Pantau tren kehadiran, anomali, dan ekspor laporan untuk direksi.</p>
        </div>
    </div>

    <p class="welcome-footer">Sistem Absensi &amp; Lembur PT. INTI &middot; Portal Internal</p>
</main>

</body>
</html>
