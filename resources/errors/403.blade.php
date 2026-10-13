<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>⛔ Akses Ditolak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f2942; color: #edf3fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-page { text-align: center; padding: 2rem; }
        .error-code { font-size: 12rem; font-weight: 700; line-height: 1; color: #b7c3d2; margin-bottom: 1rem; }
        .error-title { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .error-desc { color: #b7c3d2; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; }
        .btn-back { display: inline-block; padding: 0.75rem 1.5rem; background: #355b7f; color: #edf3fa; text-decoration: none; border-radius: 4px; font-weight: 500; margin-top: 1rem; }
        .btn-back:hover { background: #274563; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <p class="error-desc">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <p class="error-desc">Peran Anda: <strong>{{ auth()->user() ? auth()->user()->role : 'tidak terautentikasi' }}</strong></p>
        <a href="{{ route('dashboard') }}" class="btn-back">Kembali ke Dashboard</a>
    </div>
</body>
</html>