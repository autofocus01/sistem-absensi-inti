<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sistem Absensi PT. INTI')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: {
    colors: {"primary":"#001428","primary-container":"#0f2942","secondary":"#006a61",
      "background":"#f8f9ff","surface-container-lowest":"#ffffff","surface-container-low":"#eff4ff",
      "on-surface":"#0d1c2e","on-surface-variant":"#43474d","error":"#ba1a1a"},
    fontFamily: { sans: ["Plus Jakarta Sans"] }
  }}}
</script>
</head>
<body class="bg-background font-sans text-on-surface antialiased">
<div class="flex min-h-screen">
  <aside class="w-64 bg-primary text-white p-6 flex flex-col gap-2">
    <div class="mb-6 bg-white rounded-lg p-2 w-fit">
      <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-6 w-auto object-contain">
    </div>
    @if (auth()->user()->isHrAdmin())
      <a href="{{ route('employees.index') }}" class="px-3 py-2 rounded-lg hover:bg-primary-container">Data Karyawan</a>
      <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-lg hover:bg-primary-container">Rekap Absensi</a>
    @endif
    @if (auth()->user()->isDirekturUtama())
      <a href="{{ route('direktur.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-primary-container">Dashboard Direktur</a>
    @endif
  </aside>

  <main class="flex-1 p-8">
    @if (session('status'))
      <div class="mb-4 px-4 py-3 rounded-lg bg-secondary/10 text-secondary font-semibold">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 px-4 py-3 rounded-lg bg-error/10 text-error">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>
</div>
</body>
</html>