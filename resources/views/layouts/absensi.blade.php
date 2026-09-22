<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sistem Absensi PT. INTI')</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  // Token desain SAMA PERSIS dengan dashboard Direktur, biar satu bahasa visual di semua halaman.
  tailwind.config = {
    darkMode: "class",
    theme: { extend: {
      colors: {"on-surface-variant":"#43474d","primary":"#001428","on-primary":"#ffffff",
        "surface-container-low":"#eff4ff","on-background":"#0d1c2e","surface-container-highest":"#d5e3fc",
        "on-surface":"#0d1c2e","outline-variant":"#c3c6ce","secondary":"#006a61","error-container":"#ffdad6",
        "background":"#f8f9ff","surface-bright":"#f8f9ff","surface":"#f8f9ff","outline":"#74777e",
        "error":"#ba1a1a","surface-container-lowest":"#ffffff","primary-container":"#0f2942",
        "surface-container":"#e6eeff","surface-container-high":"#dce9ff","secondary-container":"#86f2e4",
        "tertiary-container":"#401f00","on-tertiary":"#ffffff","tertiary":"#220e00","on-tertiary-container":"#d77503"},
      spacing: {"space-2xl":"3rem","space-md":"0.75rem","gutter-desktop":"1.5rem","space-lg":"1.5rem",
        "space-base":"1rem","space-sm":"0.5rem","space-xl":"2rem","space-xs":"0.25rem"},
      fontFamily: {"title-md":["Plus Jakarta Sans"],"label-md":["Plus Jakarta Sans"],
        "headline-sm":["Plus Jakarta Sans"],"body-sm":["Plus Jakarta Sans"],
        "headline-lg":["Plus Jakarta Sans"],"body-md":["Plus Jakarta Sans"],
        "title-sm":["Plus Jakarta Sans"],"label-sm":["Plus Jakarta Sans"],"headline-md":["Plus Jakarta Sans"]},
      fontSize: {"title-md":["16px",{"lineHeight":"22px","fontWeight":"600"}],
        "label-md":["13px",{"lineHeight":"16px","fontWeight":"500"}],
        "headline-sm":["18px",{"lineHeight":"24px","fontWeight":"600"}],
        "body-sm":["12px",{"lineHeight":"16px","fontWeight":"400"}],
        "headline-lg":["28px",{"lineHeight":"36px","letterSpacing":"-0.015em","fontWeight":"600"}],
        "body-md":["14px",{"lineHeight":"20px","fontWeight":"400"}],
        "title-sm":["14px",{"lineHeight":"20px","fontWeight":"600"}],
        "label-sm":["11px",{"lineHeight":"14px","letterSpacing":"0.04em","fontWeight":"600"}],
        "headline-md":["22px",{"lineHeight":"28px","letterSpacing":"-0.01em","fontWeight":"600"}]}
    }}
  }
</script>
<style>
  @layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}}
  ::-webkit-scrollbar{display:none;}
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
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between select-none
                            transform -translate-x-full transition-transform duration-200 ease-in-out md:translate-x-0">
  <div class="flex flex-col overflow-y-auto">
    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60 shrink-0">
      <a href="#" onclick="window.location.reload(); return false;" class="flex items-center focus:outline-none transition-opacity hover:opacity-80" title="Refresh Halaman">
      <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
      </a>
    </div>
<div class="px-space-md py-space-xs">

    {{-- =========================================================
         KARYAWAN
         ========================================================= --}}
    @if(auth()->user()->isKaryawan())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                Portal Karyawan
            </span>

            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
                KARYAWAN
            </span>
        </div>

        <nav class="flex flex-col gap-space-xs">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('timesheet.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">event_available</span>
                Absensi & Timesheet
            </a>

            <a href="{{ route('overtime.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('overtime.index') || request()->routeIs('overtime.store') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">more_time</span>
                Pengajuan Lembur
            </a>

            <a href="{{ route('overtime.history') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('overtime.history') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">history</span>
                Riwayat Lembur
            </a>

        </nav>


    {{-- =========================================================
         VP
         ========================================================= --}}
    @elseif(auth()->user()->isVp())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                Portal VP Divisi
            </span>

            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
                VP
            </span>
        </div>

        <nav class="flex flex-col gap-space-xs">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('timesheet.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">event_available</span>
                Absensi Saya
            </a>

            <a href="{{ route('overtime.vp') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('overtime.vp') || request()->routeIs('overtime.vp.pending') || request()->routeIs('overtime.vp.approve') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">approval</span>
                Persetujuan Lembur
            </a>

            <a href="{{ route('overtime.vp.history') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('overtime.vp.history') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">history</span>
                Riwayat Keputusan
            </a>

            <a href="{{ route('team-recap.index') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm {{ request()->routeIs('reports.team') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">summarize</span> Rekap Divisi
            </a>

        </nav>


    {{-- =========================================================
         HR ADMIN
         ========================================================= --}}
    @elseif(auth()->user()->isHrAdmin())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">
<span class="font-title-sm text-title-sm text-primary">
    @if(auth()->user()->isKaryawan())
        Portal Karyawan
    @elseif(auth()->user()->isVp())
        Portal VP Divisi
    @elseif(auth()->user()->isHrAdmin())
        Portal HR &amp; Administrasi
    @elseif(auth()->user()->isDirekturUtama())
        Portal Direktur Utama
    @else
        Portal
    @endif
</span>

            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
                HR
            </span>
        </div>

        <nav class="flex flex-col gap-space-xs">

            <a href="{{ route('employees.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('employees.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">badge</span>
                Data Karyawan
            </a>

            @if (\Illuminate\Support\Facades\Route::has('employees.import'))
                <a href="{{ route('employees.import') }}"
                   class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
                   {{ request()->routeIs('employees.import*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    Import Karyawan
                </a>
            @endif

            <a href="{{ route('divisions.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('divisions.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">account_tree</span>
                Data Divisi
            </a>

            <a href="{{ route('attendance.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('attendance.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">event_available</span>
                Rekap Absensi
            </a>

            <a href="{{ route('timesheet.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">calendar_clock</span>
                Riwayat &amp; Timesheet
            </a>

            <a href="{{ route('team-recap.index') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('team-recap.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">groups</span>
                Rekapitulasi Tim
            </a>

            @if (\Illuminate\Support\Facades\Route::has('attendance.anomalies'))
                <a href="{{ route('attendance.anomalies') }}"
                   class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
                   {{ request()->routeIs('attendance.anomalies*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    <span class="material-symbols-outlined text-[20px]">fact_check</span>
                    Review Anomali
                </a>
            @endif

            @if (\Illuminate\Support\Facades\Route::has('attendance.import-errors'))
                <a href="{{ route('attendance.import-errors') }}"
                   class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
                   {{ request()->routeIs('attendance.import-errors*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    <span class="material-symbols-outlined text-[20px]">error_outline</span>
                    Error Import
                </a>
            @endif

            @if (\Illuminate\Support\Facades\Route::has('attendance.import.production'))
                <a href="{{ route('attendance.import.production') }}"
                   class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
                   {{ request()->routeIs('attendance.import.production*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    Import Absensi
                </a>
            @endif

            @if (\Illuminate\Support\Facades\Route::has('overtime.hr.verification'))
            <a href="{{ route('overtime.hr.verification') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('overtime.hr.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">verified_user</span>
                Verifikasi Lembur
            </a>
            @endif

            @if (\Illuminate\Support\Facades\Route::has('reports.index'))
            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm {{ request()->routeIs('reports.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">summarize</span> Laporan HR
            </a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('audit-logs.index'))
            <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm {{ request()->routeIs('audit-logs.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">manage_search</span> Audit Trail
            </a>
            @endif

        </nav>


    {{-- =========================================================
         DIREKTUR UTAMA
         ========================================================= --}}
    @elseif(auth()->user()->isDirekturUtama())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                Portal Direktur Utama
            </span>

            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
                DIREKTUR
            </span>
        </div>

        <nav class="flex flex-col gap-space-xs">

            <a href="{{ route('direktur.dashboard') }}"
               class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
               {{ request()->routeIs('direktur.dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                Dashboard
            </a>

        </nav>

    @endif

</div>
  </div>

  <div class="p-space-md shrink-0 border-t border-surface-container-low">
    <div class="flex items-center gap-2 px-1 pb-2">
      <div class="w-9 h-9 shrink-0 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-title-sm text-title-sm">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <div class="min-w-0">
        <div class="font-title-sm text-title-sm text-on-surface truncate">{{ auth()->user()->name }}</div>
        <div class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</div>
      </div>
    </div>
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-body-sm text-on-surface">
      <span class="material-symbols-outlined text-[18px]">account_circle</span> Profil Saya
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-body-sm text-error">
        <span class="material-symbols-outlined text-[18px]">logout</span> Logout
      </button>
    </form>
  </div>
</aside>

<div class="md:pl-72 flex flex-col min-h-screen">
  <header class="fixed top-0 left-0 md:left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-30 flex items-center justify-between px-4 md:px-space-xl">
    <div class="flex items-center gap-3">
      <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-primary" aria-label="Buka menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <span class="material-symbols-outlined text-[18px] text-secondary hidden sm:inline">shield</span>
      <span class="font-title-sm text-title-sm text-primary">Portal HR &amp; Administrasi</span>
    </div>
  </header>

  <main class="w-full pt-28 px-4 md:px-space-xl pb-space-2xl bg-background flex-1">
    @if (session('status'))
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-error/10 text-error font-body-md text-body-md">
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

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.toggle('hidden');
  }

  // Palet warna dipakai bareng oleh semua chart di halaman HR - konsisten dengan token Tailwind di atas.
  const chartPalet = {
    primary: '#0f2942',
    secondary: '#006a61',
    error: '#ba1a1a',
    outline: '#74777e',
    tertiary: '#d77503',
    grid: 'rgba(67,71,77,0.08)',
    text: '#43474d',
  };
  if (window.Chart) {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = chartPalet.text;
  }
</script>
@yield('scripts')
</body>
</html>