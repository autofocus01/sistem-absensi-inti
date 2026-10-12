<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi PT. INTI') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/p1-auth.css') }}">
</head>
<body class="inti-auth-body">
    <main class="inti-auth-shell">
        <div class="inti-auth-brand">
            <div class="inti-auth-logo-wrap">
                <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="inti-auth-logo">
            </div>
            <div class="inti-auth-brand-name">Sistem Absensi PT. INTI</div>
            <div class="inti-auth-brand-caption">Absensi, monitoring, dan lembur</div>
        </div>

        <section class="inti-auth-card" aria-label="Portal internal PT. INTI">
            {{ $slot }}
        </section>

        <p class="inti-auth-footer">Sistem Absensi &amp; Lembur PT. INTI</p>
    </main>
</body>
</html>
