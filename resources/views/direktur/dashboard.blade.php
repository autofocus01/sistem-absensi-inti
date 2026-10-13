<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
  (function () {
    const theme = localStorage.getItem('inti-theme') || 'system';
    const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.dataset.theme = theme;
  })();
</script>
<title>Dasbor Direktur Utama - PT. INTI</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/p1-design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/p1-responsive-enterprise.css') }}">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

@include('partials.tailwind-config')
<style>
  @layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}
  html { scrollbar-gutter: stable; }
  .material-symbols-outlined{
    font-family:'Material Symbols Outlined';
    font-weight:normal;
    font-style:normal;
    line-height:1;
    letter-spacing:normal;
    text-transform:none;
    display:inline-block;
    white-space:nowrap;
    word-wrap:normal;
    direction:ltr;
    -webkit-font-feature-settings:'liga';
    -webkit-font-smoothing:antialiased;
    font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
  }
</style>
<style>
  html.dark { color-scheme: dark; --dir-bg:#141c29; --dir-surface:#1b2636; --dir-low:#202d40; --dir-high:#2a3a50; --dir-text:#edf3fa; --dir-muted:#b7c3d2; --dir-primary:#dbe8f7; --dir-outline:#52657d; }
  html.dark body { background:var(--dir-bg) !important; color:var(--dir-text) !important; }
  html.dark .bg-background, html.dark .bg-surface, html.dark .bg-surface-bright { background-color:var(--dir-bg) !important; }
  html.dark .bg-surface-container-lowest, html.dark .bg-surface-container-lowest\/90 { background-color:var(--dir-surface) !important; }
  html.dark .bg-surface-container-low { background-color:var(--dir-low) !important; }
  html.dark .bg-surface-container { background-color:#26364a !important; }
  html.dark .bg-surface-container-high { background-color:var(--dir-high) !important; }
  html.dark .bg-surface-container-highest { background-color:#344961 !important; }
  html.dark .text-primary, html.dark .text-on-surface { color:var(--dir-text) !important; }
  html.dark .text-on-primary { color:#f8fbff !important; }
  html.dark .text-on-surface-variant { color:var(--dir-muted) !important; }
  html.dark .text-secondary { color:#68d8cb !important; }
  html.dark .text-error { color:#ff8b84 !important; }
  html.dark .border-surface-container-low, html.dark .border-outline, html.dark .border-outline-variant { border-color:var(--dir-outline) !important; }
  html.dark .divide-surface-container-low > :not([hidden]) ~ :not([hidden]) { border-color:#40516a !important; }
  html.dark .bg-primary-container { background-color:#355b7f !important; }
  html.dark .bg-primary { background-color:#355b7f !important; }
  html.dark .bg-secondary-container { background-color:#16463f !important; color:#d7fff9 !important; }
  html.dark .bg-error\/10 { background-color:rgba(255,110,110,.12) !important; }
  html.dark .bg-white, html.dark .bg-gray-50 { background-color:var(--dir-surface) !important; }
  html.dark input, html.dark select, html.dark textarea { background-color:#111b2b !important; color:var(--dir-text) !important; border-color:var(--dir-outline) !important; }
  html.dark input::placeholder { color:#8291a8 !important; }
  html.dark table thead tr { background-color:var(--dir-low) !important; }
  html.dark table tbody tr:hover { background-color:#24344a !important; }
  html.dark canvas { color-scheme:dark; }
  html.dark #sidebar { box-shadow:2px 0 18px rgba(0,0,0,.24) !important; }
</style>

<style>
  :root {
    --direktur-topbar-bg: rgba(255,255,255,.94);
    --direktur-topbar-border: rgba(116,119,126,.18);
    --direktur-topbar-text: #0d1c2e;
    --direktur-topbar-muted: #5e6875;
    --direktur-topbar-surface: #ffffff;
    --direktur-topbar-low: #eff4ff;
    --direktur-topbar-primary: #17466f;
  }
  html.dark {
    --direktur-topbar-bg: rgba(25,36,52,.96);
    --direktur-topbar-border: rgba(130,153,181,.24);
    --direktur-topbar-text: #edf3fa;
    --direktur-topbar-muted: #b7c3d2;
    --direktur-topbar-surface: #1b2636;
    --direktur-topbar-low: #202d40;
    --direktur-topbar-primary: #a9d7ff;
  }
  .direktur-topbar {
    background: var(--direktur-topbar-bg);
    border-bottom: 1px solid var(--direktur-topbar-border);
    box-shadow: 0 1px 10px rgba(0,0,0,.08);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
  }
  .direktur-topbar__title, .direktur-topbar__user-name { color: var(--direktur-topbar-text) !important; }
  .direktur-topbar__subtitle, .direktur-topbar__user-role { color: var(--direktur-topbar-muted) !important; }
  .direktur-topbar__shield { color: #0f766e; }
  html.dark .direktur-topbar__shield { color: #68d8cb; }
  .direktur-topbar__menu, .direktur-topbar__icon-btn { color: var(--direktur-topbar-muted); transition: background .15s, color .15s; }
  .direktur-topbar__menu:hover, .direktur-topbar__icon-btn:hover, .direktur-topbar__user:hover { background: var(--direktur-topbar-low); color: var(--direktur-topbar-text); }
  .direktur-topbar__clock { background: var(--direktur-topbar-low); color: var(--direktur-topbar-text); border: 1px solid var(--direktur-topbar-border); }
  .direktur-topbar__divider { background: var(--direktur-topbar-border); }
  .direktur-topbar__avatar { background: var(--direktur-topbar-primary); color: #fff; }
  html.dark .direktur-topbar__avatar { background: #355b7f; color: #f8fbff; }
  .direktur-topbar__settings { background: var(--direktur-topbar-low); color: var(--direktur-topbar-text); border: 1px solid var(--direktur-topbar-border); transition: background .15s, border-color .15s; }
  .direktur-topbar__settings:hover { background: var(--direktur-topbar-surface); border-color: var(--direktur-topbar-primary); }
  .direktur-user-menu, .direktur-theme-panel { background: var(--direktur-topbar-surface); color: var(--direktur-topbar-text); border: 1px solid var(--direktur-topbar-border); box-shadow: 0 14px 32px rgba(0,0,0,.16); }
  .direktur-user-menu > div { border-color: var(--direktur-topbar-border); }
  .direktur-user-menu__item { color: var(--direktur-topbar-text); }
  .direktur-user-menu__item:hover { background: var(--direktur-topbar-low); }
  .direktur-user-menu__logout { color: #d95c55; }
  html.dark .direktur-user-menu__logout { color: #ffb0a9; }
  .direktur-theme-select { background: var(--direktur-topbar-low); color: var(--direktur-topbar-text); border: 1px solid var(--direktur-topbar-border); outline: none; }
  .direktur-theme-select:focus { border-color: var(--direktur-topbar-primary); box-shadow: 0 0 0 3px rgba(104,216,203,.12); }
  .direktur-theme-select option { background: var(--direktur-topbar-surface); color: var(--direktur-topbar-text); }
  @media (max-width: 640px) {
    .direktur-topbar { padding-inline: 12px; }
    .direktur-topbar__actions { gap: 4px; }
    .direktur-topbar__settings { padding-inline: 9px; }
  }
</style>
<style>
  .direktur-responsive-table { scrollbar-width: thin; }
  .direktur-filter-form select, .direktur-filter-form input { min-height: 42px; }
  @media (max-width: 767px) {
    .direktur-topbar { height: 68px; }
    .direktur-topbar__clock { display:none !important; }
  }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { scroll-behavior:auto !important; transition-duration:0.01ms !important; animation-duration:0.01ms !important; }
  }
</style>
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between select-none
                            transform -translate-x-full transition-transform duration-200 ease-in-out md:translate-x-0">
  <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60 shrink-0">
      <a href="{{ route('dashboard') }}" class="flex items-center focus:outline-none transition-opacity hover:opacity-80" title="Kembali ke Dashboard">
        <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
      </a>
    </div>
    <div class="px-space-md py-space-xs">
      <div class="px-space-sm pb-space-xs flex items-center justify-between">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Portal Direktur Utama</span>
        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">DIREKTUR</span>
      </div>
      <nav class="flex flex-col gap-space-xs">
        <button type="button" onclick="showTab('ringkasan')" id="nav-ringkasan"
                class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm text-left bg-primary-container text-on-primary">
          <span class="material-symbols-outlined text-[18px] mr-2">dashboard</span> Dashboard
        </button>
        <button type="button" onclick="showTab('realtime')" id="nav-realtime"
                class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm text-left text-on-surface hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-[18px] mr-2">today</span> Presensi Hari Ini
        </button>
        <button type="button" onclick="showTab('history')" id="nav-history"
                class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm text-left text-on-surface hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-[18px] mr-2">history</span> History
        </button>
      </nav>
    </div>
  </div>

  <div class="border-t border-surface-container-low p-space-md bg-surface-container-lowest shrink-0">
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-1 py-2 text-on-surface hover:bg-surface-container-low transition-colors">
      <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-title-sm shrink-0">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
      <span class="min-w-0 flex-1">
        <span class="block font-title-sm text-title-sm truncate">{{ auth()->user()->name }}</span>
        <span class="block font-body-sm text-body-sm text-on-surface-variant truncate">Direktur Utama</span>
      </span>
    </a>
    <div class="flex flex-col gap-1 mt-1">
      <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]">account_circle</span>Profil Saya</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-2 py-2 text-sm text-error hover:bg-error/10 text-left"><span class="material-symbols-outlined text-[18px]">logout</span>Keluar</button>
      </form>
    </div>
    <!-- Notifikasi sesi aktif -->
<div class="font-xxs text-on-surface-variant mb-1">Sesi: Direktur</div>
<div class="mt-2 px-2 pt-2 border-t border-surface-container-low text-[11px] text-on-surface-variant">Rekap Absensi Terverifikasi · {{ $namaBulan }} {{ $tahun }}</div>
  </div>
</aside>

<div class="md:pl-72 flex flex-col min-h-screen">
  <header class="direktur-topbar fixed top-0 left-0 md:left-72 right-0 h-20 z-30 flex items-center justify-between px-4 md:px-space-xl">
    <div class="direktur-topbar__brand flex items-center gap-3 min-w-0">
      <button onclick="toggleSidebar()" class="direktur-topbar__menu md:hidden p-2 -ml-2 rounded-lg" aria-label="Buka menu">
        <span class="material-symbols-outlined text-[22px]">menu</span>
      </button>
      <span class="material-symbols-outlined direktur-topbar__shield hidden sm:inline text-[19px]">shield</span>
      <div class="min-w-0">
        <div class="direktur-topbar__title font-title-sm text-title-sm truncate">Portal Direktur Utama</div>
        <div class="direktur-topbar__subtitle hidden sm:block font-body-sm text-body-sm truncate">Portal Eksekutif &bull; PT. INTI</div>
      </div>
    </div>

    <div class="direktur-topbar__actions flex items-center gap-2 sm:gap-space-md">
      <div class="direktur-topbar__clock hidden lg:flex items-center gap-space-xs px-space-md py-1.5 rounded-full">
        <span class="material-symbols-outlined text-[16px]">schedule</span>
        <span id="jam-realtime" class="font-label-sm text-label-sm font-semibold tabular-nums">--:--:--</span>
        <span class="text-xs opacity-70">&bull;</span>
        <span class="font-body-sm text-body-sm">WIB</span>
      </div>

      @php($divisiPerluDitinjau = $matriksDivisi->where('perlu_ditinjau', true)->count())
      <button type="button" onclick="showTab('ringkasan'); document.getElementById('matriks-divisi').scrollIntoView({behavior:'smooth'})"
              class="direktur-topbar__icon-btn relative p-2 rounded-lg" title="Divisi yang perlu ditinjau" aria-label="Divisi yang perlu ditinjau">
        <span class="material-symbols-outlined text-[20px]">notifications</span>
        @if ($divisiPerluDitinjau > 0)
          <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-error ring-2 ring-[var(--direktur-topbar-bg)]"></span>
        @endif
      </button>

      <div class="direktur-topbar__divider h-8 w-px hidden sm:block"></div>

      <div class="relative">
        <button onclick="toggleUserMenu()" class="direktur-topbar__user flex items-center gap-space-sm rounded-lg px-1.5 py-1" aria-label="Menu pengguna">
          <div class="hidden md:flex flex-col text-right leading-tight">
            <span class="direktur-topbar__user-name font-title-sm text-title-sm truncate max-w-[180px]">{{ auth()->user()->name }}</span>
            <span class="direktur-topbar__user-role font-body-sm text-body-sm">Direktur Utama &bull; PT. INTI</span>
          </div>
          <div class="direktur-topbar__avatar w-10 h-10 rounded-full flex items-center justify-center font-title-sm shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
        </button>
        <div id="user-menu" class="direktur-user-menu hidden absolute right-0 top-14 w-56 rounded-xl py-1 z-50">
          <div class="px-4 py-3 border-b sm:hidden">
            <div class="font-title-sm text-title-sm truncate">{{ auth()->user()->name }}</div>
            <div class="font-body-sm text-body-sm truncate">{{ auth()->user()->email }}</div>
          </div>
          <a href="{{ route('profile.edit') }}" class="direktur-user-menu__item block px-4 py-2.5 text-sm">Profil Saya</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="direktur-user-menu__logout w-full text-left px-4 py-2.5 text-sm">Keluar</button>
          </form>
        </div>
      </div>

      <details class="relative">
        <summary class="direktur-topbar__settings list-none cursor-pointer select-none inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold" aria-label="Pengaturan tampilan">
          <span class="material-symbols-outlined text-[18px]">tune</span>
          <span class="hidden sm:inline">Pengaturan</span>
        </summary>
        <div class="direktur-theme-panel absolute right-0 top-12 z-50 w-56 rounded-xl p-4 space-y-3">
          <div>
            <label for="direktur-theme" class="block text-xs font-semibold mb-1">Tema</label>
            <select id="direktur-theme" class="direktur-theme-select w-full rounded-lg px-3 py-2 text-sm">
              <option value="light">Terang</option>
              <option value="dark">Gelap</option>
              <option value="system">Sesuai sistem</option>
            </select>
          </div>
          <p class="text-xs leading-4 opacity-75">Preferensi tema tersimpan di perangkat ini.</p>
        </div>
      </details>
    </div>
  </header>

  <main class="w-full pt-28 px-4 md:px-space-xl pb-space-2xl bg-background flex-1">

    <div class="flex flex-col gap-1 pb-space-base">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Monitoring &amp; Rekap Absensi &bull; Portal Direksi PT. INTI</span>
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Dashboard Eksekutif Direktur Utama</h1>
    </div>

    @if (session('status'))
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
        {{ session('status') }}
      </div>
    @endif

    {{-- Tab bar - satu halaman, ganti konten tanpa pindah URL --}}
    <div class="flex items-center gap-1 sm:gap-2 mb-space-lg border-b overflow-x-auto border-surface-container-low">
      <button type="button" onclick="showTab('ringkasan')" id="tabbtn-ringkasan"
              class="px-3 sm:px-4 py-2.5 font-title-sm whitespace-nowrap text-title-sm border-b-2 border-primary text-primary -mb-px">
        Ringkasan Eksekutif
      </button>
    </div>

    {{-- ============ TAB 1: RINGKASAN EKSEKUTIF ============ --}}
    <div id="tab-ringkasan">

      <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-base pb-space-lg">
        <!-- <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
          Rekap kehadiran berdasarkan 5 divisi resmi PT. INTI, dihitung langsung dari data absensi yang tersimpan di sistem.
        </p> -->

        <div class="flex flex-wrap items-center gap-2">
          <form method="GET" class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-surface-container-lowest shadow-sm">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">account_tree</span>
            <select name="division_id" onchange="this.form.submit()" class="border-0 bg-transparent font-title-sm text-title-sm text-primary focus:ring-0">
              <option value="">Semua Divisi</option>
              @foreach ($divisions as $division)
                <option value="{{ $division->id }}" @selected($divisionId == $division->id)>{{ $division->nama }}</option>
              @endforeach
            </select>
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant ml-2">calendar_today</span>
            <select name="bulan" onchange="this.form.submit()" class="border-0 bg-transparent font-title-sm text-title-sm text-primary focus:ring-0">
              @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" @selected($bulan == $m)>{{ $namaBulanMap[$m] }}</option>
              @endforeach
            </select>
            <select name="tahun" onchange="this.form.submit()" class="border-0 bg-transparent font-title-sm text-title-sm text-primary focus:ring-0">
              @foreach (range(now()->year - 1, now()->year) as $y)
                <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
              @endforeach
            </select>
          </form>

          <a href="{{ route('direktur.export-pdf', ['tahun' => $tahun, 'bulan' => $bulan, 'division_id' => $divisionId]) }}"
             class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-lowest shadow-sm hover:bg-surface-container-low transition-colors text-primary font-title-sm text-title-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">picture_as_pdf</span>
            <span class="hidden sm:inline">Unduh PDF</span>
          </a>
          <a href="{{ route('direktur.export-excel', ['tahun' => $tahun, 'bulan' => $bulan, 'division_id' => $divisionId]) }}"
             class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-lowest shadow-sm hover:bg-surface-container-low transition-colors text-primary font-title-sm text-title-sm">
            <span class="material-symbols-outlined text-[18px] text-primary">download</span>
            <span class="hidden sm:inline">Unduh Excel</span>
          </a>
        </div>
      </div>

      {{-- KPI Cards - warna diseragamkan, cuma dipakai buat status yang beneran perlu perhatian --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-xl">
        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tenaga Kerja Aktif</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">badge</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ number_format($totalKaryawan) }}</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Rata-rata Kehadiran</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">how_to_reg</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ $rataKehadiran }}%</div>
          <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Hadir {{ $totalHadir }} &bull; Dinas {{ $totalPerdin }}</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Alpha/Mangkir</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">person_off</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg {{ $totalAlpha > 0 ? 'text-error' : 'text-primary' }} tabular-nums">{{ $totalAlpha }}</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Menit Telat</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">timer</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ number_format($totalMenitTelat) }}</div>
          <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">&asymp; {{ round($totalMenitTelat / 60, 1) }} jam se-perusahaan</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Disiplin &amp; On-Time</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">verified</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ $tingkatOnTime }}%</div>
          <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Hari hadir tanpa catatan telat</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lembur Bulan Ini</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">more_time</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ $totalJamLembur }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></div>
          <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Dari log fingerprint/Face ID, se-perusahaan</div>
        </div>

        <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between pb-space-xs">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Kelengkapan Data Rekap</span>
            <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">fact_check</span></span>
          </div>
          <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ $kelengkapanRekap }}%</div>
          <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $karyawanDenganRekap }}/{{ $totalKaryawan }} karyawan sudah direkap HR</div>
        </div>
      </div>

      {{-- Tren Kehadiran & Alpha 6 Bulan Terakhir --}}
      <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-xl border border-surface-container-low">
        <div class="flex items-center justify-between pb-space-base border-b border-surface-container-low">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">trending_up</span>
            <h2 class="font-headline-md text-headline-md text-primary">Tren Kehadiran &amp; Alpha (6 Bulan Terakhir)</h2>
          </div>
          <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $divisionId ? $divisions->firstWhere('id', (int) $divisionId)?->nama : 'Semua Divisi' }}</span>
        </div>
        <div class="py-space-base h-[240px] sm:h-72 lg:h-80">
          <canvas id="chart-tren"></canvas>
        </div>
      </div>

      {{-- Grafik perbandingan antar divisi --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-md mb-space-xl">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low">
          <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
            <span class="material-symbols-outlined text-primary text-[22px]">bar_chart</span>
            <h2 class="font-headline-md text-headline-md text-primary">Rata-rata Kehadiran per Divisi</h2>
          </div>
          <div class="py-space-base h-[250px] sm:h-80">
            <canvas id="chart-divisi-kehadiran"></canvas>
          </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low">
          <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
            <span class="material-symbols-outlined text-primary text-[22px]">error</span>
            <h2 class="font-headline-md text-headline-md text-primary">Alpha &amp; Hari Telat per Divisi</h2>
          </div>
          <div class="py-space-base h-[250px] sm:h-80">
            <canvas id="chart-divisi-alpha"></canvas>
          </div>
        </div>
      </div>

      {{-- Bagan struktur --}}
      <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-xl border border-surface-container-low">
        <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
          <span class="material-symbols-outlined text-primary text-[24px]">account_tree</span>
          <h2 class="font-headline-md text-headline-md text-primary">Hierarki &amp; Kesehatan Presensi per Divisi</h2>
        </div>

        <div class="py-space-base overflow-x-auto">
          <div class="min-w-[900px] flex flex-col items-center">
            <div class="relative flex flex-col items-center">
              <div class="w-full max-w-72 bg-surface-container-high text-primary p-3 rounded-xl shadow-md border-2 border-outline-variant flex flex-col items-center text-center">
                <span class="font-label-sm text-[10px] tracking-wider uppercase font-bold text-on-surface-variant">Puncak Pimpinan Perseroan</span>
                <span class="font-title-md text-[17px] font-bold text-primary">Direktur Utama</span>
                <span class="font-semibold text-primary text-xs mt-1">Rata-rata Perusahaan: {{ $rataKehadiran }}%</span>
              </div>
              <div class="w-0.5 h-7 bg-outline-variant"></div>
            </div>

            <div class="w-full relative flex justify-between items-start gap-space-sm">
              <div class="absolute top-0 left-[6%] right-[6%] h-0.5 bg-outline-variant"></div>
              @foreach ($matriksDivisi as $row)
              <div class="flex flex-col items-center relative pt-4" style="width: {{ 100 / max(count($matriksDivisi),1) }}%">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0.5 h-4 bg-outline-variant"></div>
                <div class="w-full bg-primary-container text-white p-2.5 rounded-lg shadow-sm border border-primary-container flex flex-col gap-1 hover:opacity-90 transition-opacity">
                  <span class="font-title-sm text-xs font-semibold text-white leading-tight">{{ $row['divisi'] }}</span>
                  <div class="flex items-center justify-between">
                    <span class="text-[10px] text-white/70">{{ $row['total_personel'] }} Karyawan</span>
                    <span class="text-xs font-bold {{ $row['perlu_ditinjau'] ? 'text-error-container' : 'text-white' }}">{{ $row['rata_kehadiran'] }}%</span>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Sebaran Lokasi --}}
      <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-xl border border-surface-container-low">
        <div class="flex items-center justify-between pb-space-base border-b border-surface-container-low">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]">location_city</span>
            <h2 class="font-headline-md text-headline-md text-primary">Sebaran Lokasi Presensi</h2>
          </div>
          <span class="font-label-sm text-label-sm text-on-surface-variant">{{ number_format($totalHariBandung + $totalHariJakarta) }} hari presensi tercatat</span>
        </div>

        <div class="py-space-base flex flex-col lg:flex-row gap-space-lg items-stretch">
        <div class="w-full lg:w-48 shrink-0 h-40 lg:h-auto">
          <canvas id="chart-lokasi"></canvas>
        </div>
        <div class="flex-1 space-y-space-sm">
          <div class="p-space-base rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
            <div class="flex items-start gap-space-sm">
              <div class="p-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm mt-0.5">
                <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
              </div>
              <div class="flex flex-col">
                <span class="font-title-sm text-title-sm text-primary">Bandung</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Kantor pusat &amp; unit operasional utama</span>
              </div>
            </div>
            <div class="flex items-center gap-space-lg pl-10 sm:pl-0">
              <div class="w-32 h-2 rounded-full bg-surface-container-lowest overflow-hidden hidden sm:block">
                <div class="bg-primary h-full rounded-full" style="width: {{ $persenBandung }}%;"></div>
              </div>
              <div class="text-right">
                <div class="font-title-sm text-title-sm text-primary tabular-nums">{{ number_format($totalHariBandung) }} hari</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $persenBandung }}% dari total presensi</div>
              </div>
            </div>
          </div>

          <div class="p-space-base rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
            <div class="flex items-start gap-space-sm">
              <div class="p-2 rounded-lg bg-surface-container-lowest text-primary shadow-sm mt-0.5">
                <span class="material-symbols-outlined text-[20px]">apartment</span>
              </div>
              <div class="flex flex-col">
                <span class="font-title-sm text-title-sm text-primary">Jakarta</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Unit operasional &amp; perwakilan</span>
              </div>
            </div>
            <div class="flex items-center gap-space-lg pl-10 sm:pl-0">
              <div class="w-32 h-2 rounded-full bg-surface-container-lowest overflow-hidden hidden sm:block">
                <div class="bg-primary h-full rounded-full" style="width: {{ $persenJakarta }}%;"></div>
              </div>
              <div class="text-right">
                <div class="font-title-sm text-title-sm text-primary tabular-nums">{{ number_format($totalHariJakarta) }} hari</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $persenJakarta }}% dari total presensi</div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>

      {{-- Donut Chart Komposisi Kehadiran - CSS conic-gradient murni, data real dari rekap bulanan --}}
      <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-xl border border-surface-container-low">
        <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
          <span class="material-symbols-outlined text-primary text-[22px]">donut_large</span>
          <h2 class="font-headline-md text-headline-md text-primary">Komposisi Kehadiran</h2>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-space-xl py-space-base">
          <div class="w-40 h-40 sm:w-48 sm:h-48 shrink-0 relative">
            <canvas id="chart-komposisi"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="font-headline-md text-headline-md text-primary tabular-nums">{{ number_format($totalKomposisi) }}</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Total Hari</span>
            </div>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-space-sm w-full">
            @foreach ($komposisiDenganPersen as $label => $info)
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $info['warna'] }};"></span>
                <div class="flex flex-col">
                  <span class="font-title-sm text-title-sm text-on-surface">{{ $label }}</span>
                  <span class="font-body-sm text-body-sm text-on-surface-variant tabular-nums">{{ number_format($info['jumlah']) }} hari ({{ $info['persen'] }}%)</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Matriks tabel per divisi --}}
      <div id="matriks-divisi" class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg scroll-mt-24">
        <h2 class="font-headline-md text-headline-md text-primary mb-space-base">Matriks Kehadiran per Divisi</h2>
        <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[720px]">
          <thead>
            <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
              <th class="py-3 px-4 rounded-l-lg">Divisi</th>
              <th class="py-3 px-4">Total Personel</th>
              <th class="py-3 px-4">Rata-rata Kehadiran</th>
              <th class="py-3 px-4">Total Hari Telat</th>
              <th class="py-3 px-4">Total Alpha</th>
              <th class="py-3 px-4 text-right rounded-r-lg">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container-low">
            @foreach ($matriksDivisi as $row)
            <tr class="hover:bg-surface-container-low/40">
              <td class="py-3 px-4 font-semibold text-primary">{{ $row['divisi'] }}</td>
              <td class="py-3 px-4">{{ $row['total_personel'] }}</td>
              <td class="py-3 px-4 tabular-nums">{{ $row['rata_kehadiran'] }}%</td>
              <td class="py-3 px-4 tabular-nums">{{ $row['total_telat_hari'] }}</td>
              <td class="py-3 px-4 tabular-nums">{{ $row['total_alpha'] }}</td>
              <td class="py-3 px-4 text-right">
                @if ($row['perlu_ditinjau'])
                  <span class="px-2 py-1 rounded-full bg-error/10 text-error text-xs font-semibold">Perlu Ditinjau</span>
                @else
                  <span class="px-2 py-1 rounded-full bg-surface-container text-on-surface-variant text-xs font-semibold">Normal</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        </div>
      </div>
    </div>


      {{-- DATA KESELURUHAN KARYAWAN --}}
      <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mt-space-xl border border-surface-container-low">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-space-base border-b border-surface-container-low">
          <div>
            <h2 class="font-headline-md text-headline-md text-primary">Data Keseluruhan Karyawan</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Scope Direksi mencakup seluruh karyawan dalam periode yang dipilih; filter divisi tetap tersedia.</p>
          </div>
          <form method="GET" action="{{ route('direktur.dashboard') }}" class="flex flex-wrap gap-2">
            <input type="text" name="employee_q" value="{{ $employeeSearch ?? '' }}" placeholder="Nama atau NIPEG"
                   class="rounded-lg border border-outline bg-surface px-3 py-2 text-sm min-w-[220px]">
            <select name="division_id" class="rounded-lg border border-outline bg-surface px-3 py-2 text-sm">
              <option value="">Semua Divisi</option>
              @foreach($divisions as $division)
                <option value="{{ $division->id }}" @selected((int)($divisionId ?? 0) === (int)$division->id)>{{ $division->nama }}</option>
              @endforeach
            </select>
            <select name="bulan" class="rounded-lg border border-outline bg-surface px-3 py-2 text-sm">
              @for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected($bulan===$m)>{{ $namaBulanMap[$m] }}</option>@endfor
            </select>
            <select name="tahun" class="rounded-lg border border-outline bg-surface px-3 py-2 text-sm">
              @foreach(range(now()->year-2, now()->year+1) as $y)<option value="{{ $y }}" @selected($tahun===$y)>{{ $y }}</option>@endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-primary text-on-primary text-sm font-semibold" type="submit">Tampilkan</button>
          </form>
        </div>
        <div class="overflow-x-auto mt-4 direktur-responsive-table">
          <table class="w-full text-left min-w-[1050px]">
            <thead><tr class="bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">
              <th class="py-3 px-4">Karyawan</th><th class="py-3 px-4">NIPEG</th><th class="py-3 px-4">Band</th><th class="py-3 px-4">Divisi</th><th class="py-3 px-4">Hari Ini</th><th class="py-3 px-4">Kehadiran Bulanan</th><th class="py-3 px-4">Telat</th><th class="py-3 px-4">Anomali</th>
            </tr></thead>
            <tbody class="divide-y divide-surface-container-low">
              @forelse(($employeePaginator ?? $employeeOverview->take(25)) as $row)
                <tr class="hover:bg-surface-container-low/40">
                  <td class="py-3 px-4 font-semibold text-primary">{{ $row['nama'] }}</td><td class="py-3 px-4">{{ $row['nipeg'] }}</td><td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-full bg-primary-container/30 text-primary text-xs font-semibold">{{ $row['band'] !== null ? 'Band ' . $row['band'] : '-' }}</span></td><td class="py-3 px-4">{{ $row['divisi'] }}</td>
                  <td class="py-3 px-4"><div>{{ $row['status_hari_ini'] }}</div><div class="text-xs text-on-surface-variant">{{ $row['jam_masuk'] ? substr($row['jam_masuk'],0,5) : '--:--' }} → {{ $row['jam_pulang'] ? substr($row['jam_pulang'],0,5) : '--:--' }}</div></td>
                  <td class="py-3 px-4">{{ $row['persen_kehadiran'] }}%</td><td class="py-3 px-4">{{ $row['telat_hari'] }} hari</td><td class="py-3 px-4">{{ $row['anomaly_count'] }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="py-8 text-center text-on-surface-variant">Belum ada data karyawan pada scope periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-xs text-on-surface-variant"><span>Menampilkan {{ ($employeePaginator ?? $employeeOverview)->count() }} dari {{ $employeeOverview->count() }} karyawan pada halaman ini.</span>@if(($employeePaginator ?? null) instanceof \Illuminate\Pagination\LengthAwarePaginator)<span>{{ $employeePaginator->links() }}</span>@endif</div>
      </div>

      <div id="tab-realtime" class="hidden mt-space-xl scroll-mt-24">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low">
          <div class="flex items-center justify-between pb-space-base border-b border-surface-container-low">
            <div><h2 class="font-headline-md text-headline-md text-primary">Presensi Hari Ini</h2><p class="text-sm text-on-surface-variant">Snapshot presensi seluruh karyawan pada hari ini.</p></div>
            <span class="px-3 py-1 rounded-full bg-secondary-container text-on-surface text-xs font-semibold">Snapshot · <span id="director-live-date">-- -- ----</span> · <span id="director-live-time">--:--:--</span> WIB</span>
          </div>
          <div class="overflow-x-auto mt-4 direktur-responsive-table"><table class="w-full text-left min-w-[900px]"><thead><tr class="bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant"><th class="py-3 px-4">Karyawan</th><th class="py-3 px-4">Band</th><th class="py-3 px-4">Divisi</th><th class="py-3 px-4">Jam Masuk</th><th class="py-3 px-4">Jam Pulang</th><th class="py-3 px-4">Status</th></tr></thead><tbody class="divide-y divide-surface-container-low">
          @forelse($realtimeRows as $row)<tr><td class="py-3 px-4 font-semibold">{{ $row['nama'] }}<div class="text-xs text-on-surface-variant">{{ $row['nipeg'] }}</div></td><td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-full bg-primary-container/30 text-primary text-xs font-semibold">{{ $row['band'] !== null ? 'Band ' . $row['band'] : '-' }}</span></td><td class="py-3 px-4">{{ $row['divisi'] }}</td><td class="py-3 px-4">{{ $row['jam_masuk'] ? substr($row['jam_masuk'],0,5) : '--:--' }}</td><td class="py-3 px-4">{{ $row['jam_pulang'] ? substr($row['jam_pulang'],0,5) : '--:--' }}</td><td class="py-3 px-4">{{ $row['status_hari_ini'] }}</td></tr>@empty<tr><td colspan="6" class="py-8 text-center text-on-surface-variant">Belum ada presensi hari ini.</td></tr>@endforelse
          </tbody></table></div>
        </div>
      </div>

      <div id="tab-history" class="hidden mt-space-xl scroll-mt-24">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low">
          <div class="pb-space-base border-b border-surface-container-low"><h2 class="font-headline-md text-headline-md text-primary">Riwayat Presensi</h2><p class="text-sm text-on-surface-variant">Riwayat presensi seluruh karyawan untuk periode terpilih.</p></div>
          <div class="overflow-x-auto mt-4 direktur-responsive-table"><table class="w-full text-left min-w-[1100px]"><thead><tr class="bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant"><th class="py-3 px-4">Tanggal</th><th class="py-3 px-4">Karyawan</th><th class="py-3 px-4">NIPEG</th><th class="py-3 px-4">Band</th><th class="py-3 px-4">Divisi</th><th class="py-3 px-4">Masuk</th><th class="py-3 px-4">Pulang</th><th class="py-3 px-4">Durasi</th><th class="py-3 px-4">Status</th></tr></thead><tbody class="divide-y divide-surface-container-low">
          @forelse($historyRows->take(50) as $row)<tr><td class="py-3 px-4">{{ $row['tanggal']->format('d') }} {{ $namaBulanMap[(int) $row['tanggal']->format('n')] }} {{ $row['tanggal']->format('Y') }}</td><td class="py-3 px-4 font-semibold">{{ $row['nama'] }}</td><td class="py-3 px-4">{{ $row['nipeg'] }}</td><td class="py-3 px-4"><span class="inline-flex px-2 py-0.5 rounded-full bg-primary-container/30 text-primary text-xs font-semibold">{{ $row['band'] !== null ? 'Band ' . $row['band'] : '-' }}</span></td><td class="py-3 px-4">{{ $row['divisi'] }}</td><td class="py-3 px-4">{{ $row['jam_masuk'] ? substr($row['jam_masuk'],0,5) : '--:--' }}</td><td class="py-3 px-4">{{ $row['jam_pulang'] ? substr($row['jam_pulang'],0,5) : '--:--' }}</td><td class="py-3 px-4">{{ $row['durasi'] }}</td><td class="py-3 px-4">{{ $row['status'] }}</td></tr>@empty<tr><td colspan="9" class="py-8 text-center text-on-surface-variant">Belum ada riwayat pada periode terpilih.</td></tr>@endforelse
          </tbody></table></div>
          @if($historyRows->count() > 50)<div class="mt-3 text-xs text-on-surface-variant">Menampilkan 50 dari {{ $historyRows->count() }} baris. Gunakan filter periode/divisi untuk mempersempit data.</div>@endif
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low mt-4">
          <h2 class="font-headline-md text-headline-md text-primary mb-4">Frekuensi Anomaly per Divisi</h2>
          <div class="overflow-x-auto direktur-responsive-table"><table class="w-full text-left min-w-[720px]"><thead><tr class="bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant"><th class="py-3 px-4">Divisi</th><th class="py-3 px-4">Total</th><th class="py-3 px-4">Open</th><th class="py-3 px-4">Reviewed</th><th class="py-3 px-4">Resolved</th></tr></thead><tbody class="divide-y divide-surface-container-low">
          @forelse($anomalyByDivision as $row)<tr><td class="py-3 px-4 font-semibold">{{ $row['divisi'] }}</td><td class="py-3 px-4">{{ $row['total'] }}</td><td class="py-3 px-4">{{ $row['open'] }}</td><td class="py-3 px-4">{{ $row['reviewed'] }}</td><td class="py-3 px-4">{{ $row['resolved'] }}</td></tr>@empty<tr><td colspan="5" class="py-6 text-center text-on-surface-variant">Belum ada anomaly pada periode ini.</td></tr>@endforelse
          </tbody></table></div>
        </div>
      </div>

    </main>
</div>

<script>
  function tickJamRealtime() {
    const el = document.getElementById('jam-realtime');
    if (!el) return;
    const now = new Date();
    const value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateValue = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    el.textContent = value;
    const liveDate = document.getElementById('director-live-date');
    if (liveDate) liveDate.textContent = dateValue;
    const live = document.getElementById('director-live-time');
    if (live) live.textContent = value;
  }
  tickJamRealtime();
  setInterval(tickJamRealtime, 1000);

  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.toggle('hidden');
  }

  function toggleUserMenu() {
    document.getElementById('user-menu').classList.toggle('hidden');
  }
  document.addEventListener('click', function (e) {
    const menu = document.getElementById('user-menu');
    if (!menu || menu.classList.contains('hidden')) return;
    if (!e.target.closest('#user-menu') && !e.target.closest('button[onclick="toggleUserMenu()"]')) {
      menu.classList.add('hidden');
    }
  });

  // Ganti tab TANPA pindah halaman/URL.
  function showTab(tab) {
    const tabs = ['ringkasan', 'realtime', 'history'];
    tabs.forEach(function (t) {
      document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
      const tabButton = document.getElementById('tabbtn-' + t);
      if (tabButton) {
        tabButton.classList.toggle('border-primary', t === tab);
        tabButton.classList.toggle('text-primary', t === tab);
        tabButton.classList.toggle('border-transparent', t !== tab);
        tabButton.classList.toggle('text-on-surface-variant', t !== tab);
      }
      document.getElementById('nav-' + t).classList.toggle('bg-primary-container', t === tab);
      document.getElementById('nav-' + t).classList.toggle('text-on-primary', t === tab);
      document.getElementById('nav-' + t).classList.toggle('text-on-surface', t !== tab);
    });
    if (window.location.hash !== '#' + tab) {
      history.replaceState(null, '', '#' + tab);
    }
    // Tutup sidebar mobile setelah pilih menu.
    if (window.innerWidth < 768) {
      document.getElementById('sidebar').classList.add('-translate-x-full');
      document.getElementById('sidebar-backdrop').classList.add('hidden');
    }
  }

  // Buka tab sesuai hash URL (dipakai waktu redirect balik dari approve/reject lembur).
  document.addEventListener('DOMContentLoaded', function () {
    const initial = ['ringkasan', 'realtime', 'history'].includes(window.location.hash.replace('#', ''))
      ? window.location.hash.replace('#', '')
      : 'ringkasan';
    showTab(initial);
  });
</script>

<script>
  // ===== Data grafik, dikirim langsung dari controller (data asli, bukan dummy) =====
  const komposisiData   = @json($komposisiDenganPersen);
  const trenBulanan     = @json($trenBulanan);
  const matriksDivisi   = @json($matriksDivisi);
  const lokasiData      = { bandung: {{ $totalHariBandung }}, jakarta: {{ $totalHariJakarta }} };

  const palet = window.intiGetChartPalette ? window.intiGetChartPalette() : (document.documentElement.classList.contains('dark') ? {
    primary:'#7dd3fc', secondary:'#5eead4', error:'#fb7185', outline:'#94a3b8', grid:'rgba(148,163,184,0.20)', text:'#dbe7f3', surface:'#1b2636'
  } : {
    primary:'#0f2942', secondary:'#006a61', error:'#ba1a1a', outline:'#74777e', grid:'rgba(67,71,77,0.10)', text:'#43474d', surface:'#ffffff'
  });

  Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
  Chart.defaults.color = palet.text;

  // 1) Doughnut - Komposisi Kehadiran
  new Chart(document.getElementById('chart-komposisi'), {
    type: 'doughnut',
    data: {
      labels: Object.keys(komposisiData),
      datasets: [{
        data: Object.values(komposisiData).map(v => v.jumlah),
        backgroundColor: Object.values(komposisiData).map(v => v.warna),
        borderWidth: 2,
        borderColor: palet.surface,
      }],
    },
    options: {
      cutout: '72%',
      plugins: { legend: { display: false } },
      responsive: true,
      maintainAspectRatio: false,
    },
  });

  // 2) Line - Tren Kehadiran & Alpha 6 Bulan Terakhir (dua sumbu-Y)
  new Chart(document.getElementById('chart-tren'), {
    type: 'line',
    data: {
      labels: trenBulanan.map(t => t.label),
      datasets: [
        {
          label: 'Rata-rata Kehadiran (%)',
          data: trenBulanan.map(t => t.rata_kehadiran),
          borderColor: palet.primary,
          backgroundColor: palet.primary,
          yAxisID: 'y',
          tension: 0.35,
          fill: false,
          pointRadius: 4,
          pointHoverRadius: 6,
        },
        {
          label: 'Total Alpha',
          data: trenBulanan.map(t => t.total_alpha),
          borderColor: palet.error,
          backgroundColor: palet.error,
          yAxisID: 'y1',
          tension: 0.35,
          fill: false,
          pointRadius: 4,
          pointHoverRadius: 6,
          borderDash: [4, 3],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom' } },
      scales: {
        y:  { position: 'left',  min: 0, max: 100, grid: { color: palet.grid }, title: { display: true, text: 'Kehadiran (%)' } },
        y1: { position: 'right', min: 0, grid: { display: false }, title: { display: true, text: 'Alpha (hari)' } },
        x:  { grid: { display: false } },
      },
    },
  });

  // 3) Bar horizontal - Rata-rata Kehadiran per Divisi
  new Chart(document.getElementById('chart-divisi-kehadiran'), {
    type: 'bar',
    data: {
      labels: matriksDivisi.map(d => d.divisi),
      datasets: [{
        label: 'Rata-rata Kehadiran (%)',
        data: matriksDivisi.map(d => d.rata_kehadiran),
        backgroundColor: matriksDivisi.map(d => d.perlu_ditinjau ? palet.error : palet.secondary),
        borderRadius: 6,
        maxBarThickness: 28,
      }],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { min: 0, max: 100, grid: { color: palet.grid }, title: { display: true, text: 'Persen Kehadiran' } },
        y: { grid: { display: false } },
      },
    },
  });

  // 4) Bar grup - Alpha & Hari Telat per Divisi
  new Chart(document.getElementById('chart-divisi-alpha'), {
    type: 'bar',
    data: {
      labels: matriksDivisi.map(d => d.divisi),
      datasets: [
        {
          label: 'Total Alpha',
          data: matriksDivisi.map(d => d.total_alpha),
          backgroundColor: palet.error,
          borderRadius: 6,
          maxBarThickness: 24,
        },
        {
          label: 'Hari Telat',
          data: matriksDivisi.map(d => d.total_telat_hari),
          backgroundColor: palet.outline,
          borderRadius: 6,
          maxBarThickness: 24,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: palet.grid } },
      },
    },
  });

  // 5) Doughnut - Sebaran Lokasi
  new Chart(document.getElementById('chart-lokasi'), {
    type: 'doughnut',
    data: {
      labels: ['Bandung', 'Jakarta'],
      datasets: [{
        data: [lokasiData.bandung, lokasiData.jakarta],
        backgroundColor: [palet.primary, palet.secondary],
        borderWidth: 2,
        borderColor: palet.surface,
      }],
    },
    options: {
      cutout: '65%',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } },
    },
  });
</script>
<script>
  function directorChartPalette() {
    return document.documentElement.classList.contains('dark') ? {
      primary:'#7dd3fc', secondary:'#5eead4', error:'#fb7185', outline:'#94a3b8',
      grid:'rgba(148,163,184,0.20)', text:'#dbe7f3', surface:'#1b2636'
    } : {
      primary:'#0f2942', secondary:'#006a61', error:'#ba1a1a', outline:'#74777e',
      grid:'rgba(67,71,77,0.10)', text:'#43474d', surface:'#ffffff'
    };
  }

  function applyDirectorChartTheme() {
    if (!window.Chart) return;
    const palette = directorChartPalette();
    Chart.defaults.color = palette.text;
    Object.values(Chart.instances || {}).forEach(chart => {
      if (chart.options?.scales) {
        Object.values(chart.options.scales).forEach(scale => {
          scale.ticks = scale.ticks || {};
          scale.ticks.color = palette.text;
          scale.grid = scale.grid || {};
          if (scale.grid.display !== false) scale.grid.color = palette.grid;
          if (scale.title?.display) scale.title.color = palette.text;
        });
      }
      if (chart.options?.plugins?.legend) {
        chart.options.plugins.legend.labels = chart.options.plugins.legend.labels || {};
        chart.options.plugins.legend.labels.color = palette.text;
      }
      if (chart.options?.plugins?.tooltip) {
        chart.options.plugins.tooltip.titleColor = palette.text;
        chart.options.plugins.tooltip.bodyColor = palette.text;
        chart.options.plugins.tooltip.backgroundColor = palette.surface;
        chart.options.plugins.tooltip.borderColor = palette.outline;
        chart.options.plugins.tooltip.borderWidth = 1;
      }
      chart.data.datasets.forEach(dataset => {
        const swap = value => ({
          '#0f2942':palette.primary, '#7dd3fc':palette.primary,
          '#006a61':palette.secondary, '#5eead4':palette.secondary,
          '#ba1a1a':palette.error, '#fb7185':palette.error,
          '#74777e':palette.outline, '#94a3b8':palette.outline,
        }[value] || value);
        if (typeof dataset.backgroundColor === 'string') dataset.backgroundColor = swap(dataset.backgroundColor);
        if (typeof dataset.borderColor === 'string') dataset.borderColor = swap(dataset.borderColor);
        if (Array.isArray(dataset.backgroundColor)) dataset.backgroundColor = dataset.backgroundColor.map(swap);
        if (Array.isArray(dataset.borderColor)) dataset.borderColor = dataset.borderColor.map(swap);
        if (dataset.borderColor === '#ffffff') dataset.borderColor = palette.surface;
      });
      chart.update('none');
    });
  }

  (function () {
    const select = document.getElementById('direktur-theme');
    const apply = (theme) => {
      const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
      document.documentElement.classList.toggle('dark', dark);
      document.documentElement.dataset.theme = theme;
      localStorage.setItem('inti-theme', theme);
      if (select) select.value = theme;
      applyDirectorChartTheme();
    };
    const initial = localStorage.getItem('inti-theme') || 'system';
    if (select) {
      select.value = initial;
      select.addEventListener('change', e => apply(e.target.value));
    }
    applyDirectorChartTheme();
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener?.('change', () => {
      if ((localStorage.getItem('inti-theme') || 'system') === 'system') apply('system');
    });
  })();
</script>
</body>
</html>