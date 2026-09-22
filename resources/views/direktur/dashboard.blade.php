<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Direktur Utama - PT. INTI</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
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
  @layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}
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
  <div class="flex flex-col">
    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60">
      <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
    </div>
    <div class="px-space-md py-space-xs">
      <div class="px-space-sm pb-space-xs flex items-center justify-between">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Eksekutif &amp; Direksi</span>
        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">BOD</span>
      </div>
      <nav class="flex flex-col gap-space-xs">
        <button type="button" onclick="showTab('ringkasan')" id="nav-ringkasan"
                class="flex items-center px-space-md py-space-sm rounded-lg font-title-sm text-left bg-primary-container text-on-primary">
          Dashboard Direktur Utama
        </button>
      </nav>
    </div>
  </div>

  <div class="p-space-base m-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Sumber Data</span>
      <span class="inline-flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant font-semibold">
        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>Live
      </span>
    </div>
    <span class="font-title-sm text-title-sm text-on-surface">Rekap Absensi Terverifikasi</span>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
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
      <span class="font-title-sm text-title-sm text-primary">Portal Eksekutif</span>
    </div>
    <div class="flex items-center gap-space-md">
      <div class="hidden xl:flex items-center gap-space-xs px-space-md py-1.5 rounded-full bg-surface-container-low">
        <span class="material-symbols-outlined text-[16px] text-on-surface-variant">schedule</span>
        <span id="jam-realtime" class="font-label-sm text-label-sm text-on-surface font-semibold tabular-nums">--:--:--</span>
        <span class="text-outline text-xs">&bull;</span>
        <span class="font-body-sm text-body-sm text-on-surface-variant">WIB</span>
      </div>
      @php($divisiPerluDitinjau = $matriksDivisi->where('perlu_ditinjau', true)->count())
      <button type="button" onclick="showTab('ringkasan'); document.getElementById('matriks-divisi').scrollIntoView({behavior:'smooth'})"
              class="relative p-2 rounded-lg hover:bg-surface-container-low text-on-surface-variant hover:text-primary transition-colors" title="Divisi yang perlu ditinjau">
        <span class="material-symbols-outlined text-[20px]">notifications</span>
        @if ($divisiPerluDitinjau > 0)
          <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
        @endif
      </button>
      <div class="h-8 w-[1px] bg-surface-container-low hidden sm:block"></div>
      <div class="relative">
        <button onclick="toggleUserMenu()" class="flex items-center gap-space-sm">
          <div class="hidden sm:flex flex-col text-right">
            <span class="font-title-sm text-title-sm text-on-surface">{{ auth()->user()->name }}</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Direktur Utama &bull; PT. INTI</span>
          </div>
          <div class="w-10 h-10 rounded-full flex items-center justify-center bg-primary-container text-on-primary font-title-sm">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
        </button>
        <div id="user-menu" class="hidden absolute right-0 top-14 w-52 bg-surface-container-lowest rounded-lg shadow-lg border border-surface-container-low py-1 z-50">
          <div class="px-4 py-2 border-b border-surface-container-low sm:hidden">
            <div class="font-title-sm text-title-sm text-on-surface truncate">{{ auth()->user()->name }}</div>
            <div class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</div>
          </div>
          <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low">Profil Saya</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-error hover:bg-surface-container-low">Logout</button>
          </form>
        </div>
      </div>
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
    <div class="flex items-center gap-2 mb-space-lg border-b border-surface-container-low">
      <button type="button" onclick="showTab('ringkasan')" id="tabbtn-ringkasan"
              class="px-4 py-2.5 font-title-sm text-title-sm border-b-2 border-primary text-primary -mb-px">
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
                <option value="{{ $m }}" @selected($bulan == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-xl">
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
        <div class="py-space-base h-72">
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
          <div class="py-space-base h-80">
            <canvas id="chart-divisi-kehadiran"></canvas>
          </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg border border-surface-container-low">
          <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
            <span class="material-symbols-outlined text-primary text-[22px]">error</span>
            <h2 class="font-headline-md text-headline-md text-primary">Alpha &amp; Hari Telat per Divisi</h2>
          </div>
          <div class="py-space-base h-80">
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
              <div class="w-72 bg-surface-container-high text-primary p-3 rounded-xl shadow-md border-2 border-outline-variant flex flex-col items-center text-center">
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
          <div class="w-48 h-48 shrink-0 relative">
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

    {{-- ============ TAB 2: OTORISASI KHUSUS ============ --}}</main>
</div>

<script>
  function tickJamRealtime() {
    const el = document.getElementById('jam-realtime');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
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
    const tabs = ['ringkasan'];
    tabs.forEach(function (t) {
      document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
      document.getElementById('tabbtn-' + t).classList.toggle('border-primary', t === tab);
      document.getElementById('tabbtn-' + t).classList.toggle('text-primary', t === tab);
      document.getElementById('tabbtn-' + t).classList.toggle('border-transparent', t !== tab);
      document.getElementById('tabbtn-' + t).classList.toggle('text-on-surface-variant', t !== tab);
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
    const initial = 'ringkasan';
    showTab(initial);
  });
</script>

<script>
  // ===== Data grafik, dikirim langsung dari controller (data asli, bukan dummy) =====
  const komposisiData   = @json($komposisiDenganPersen);
  const trenBulanan     = @json($trenBulanan);
  const matriksDivisi   = @json($matriksDivisi);
  const lokasiData      = { bandung: {{ $totalHariBandung }}, jakarta: {{ $totalHariJakarta }} };

  const palet = {
    primary: '#0f2942',
    secondary: '#006a61',
    error: '#ba1a1a',
    outline: '#74777e',
    grid: 'rgba(67,71,77,0.08)',
    text: '#43474d',
  };

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
        borderColor: '#ffffff',
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
        borderColor: '#ffffff',
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
</body>
</html>