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
<script>
  // Config disamain persis dengan mockup asli (code0.html) biar konsisten desainnya.
  tailwind.config = {
    darkMode: "class",
    theme: { extend: {
      colors: {"on-surface-variant":"#43474d","primary":"#001428","on-primary":"#ffffff",
        "surface-container-low":"#eff4ff","on-background":"#0d1c2e","surface-container-highest":"#d5e3fc",
        "on-surface":"#0d1c2e","outline-variant":"#c3c6ce","secondary":"#006a61","error-container":"#ffdad6",
        "background":"#f8f9ff","surface-bright":"#f8f9ff","surface":"#f8f9ff","outline":"#74777e",
        "error":"#ba1a1a","surface-container-lowest":"#ffffff","primary-container":"#0f2942",
        "surface-container":"#e6eeff","surface-container-high":"#dce9ff","secondary-container":"#86f2e4",
        "tertiary-container":"#401f00","on-tertiary":"#ffffff"},
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
<style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style>
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between select-none">
  <div class="flex flex-col">
    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60">
      <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
    </div>
    <div class="px-space-md py-space-xs">
      <div class="px-space-sm pb-space-xs flex items-center justify-between">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Eksekutif &amp; Direksi</span>
        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-tertiary-container text-on-tertiary">BOD</span>
      </div>
      <nav class="flex flex-col gap-space-xs">
        <a class="flex items-center px-space-md py-space-sm rounded-lg bg-primary-container text-on-primary font-title-sm" href="{{ route('direktur.dashboard') }}">
          Dashboard Direktur Utama
        </a>
      </nav>
    </div>
  </div>

  <div class="p-space-base m-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Sumber Data</span>
      <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-secondary/10 text-secondary font-label-sm text-label-sm font-semibold">
        <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>Live
      </span>
    </div>
    <span class="font-title-sm text-title-sm text-on-surface">Rekap Absensi Terverifikasi</span>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
  </div>
</aside>

<div class="pl-72 flex flex-col min-h-screen">
  <header class="fixed top-0 left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-space-xl">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-[18px] text-secondary">shield</span>
      <span class="font-title-sm text-title-sm text-primary">Portal Eksekutif</span>
    </div>
    <div class="flex items-center gap-space-md">
      <div class="hidden xl:flex items-center gap-space-xs px-space-md py-1.5 rounded-full bg-surface-container-low">
        <span class="material-symbols-outlined text-[16px] text-secondary">schedule</span>
        <span id="jam-realtime" class="font-label-sm text-label-sm text-on-surface font-semibold tabular-nums">--:--:--</span>
        <span class="text-outline text-xs">&bull;</span>
        <span class="font-body-sm text-body-sm text-on-surface-variant">WIB</span>
      </div>
      <div class="h-8 w-[1px] bg-surface-container-low"></div>
      <div class="flex items-center gap-space-sm">
        <div class="flex flex-col text-right">
          <span class="font-title-sm text-title-sm text-on-surface">{{ auth()->user()->name }}</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Direktur Utama &bull; PT. INTI</span>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-primary-container text-on-primary font-title-sm">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
      </div>
    </div>
  </header>

  <main class="w-full pt-28 px-space-xl pb-space-2xl bg-background flex-1">

    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-space-base pb-space-lg">
      <div class="flex flex-col gap-1">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-container text-surface-container-lowest font-label-sm text-label-sm uppercase tracking-wider w-fit">
          <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
          MONITORING &amp; REKAP ABSENSI &bull; PORTAL DIREKSI PT. INTI
        </span>
        <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Dashboard Eksekutif Direktur Utama</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
          Rekap kehadiran berdasarkan 5 divisi resmi PT. INTI, dihitung langsung dari data absensi yang tersimpan di sistem.
        </p>
      </div>

      <form method="GET" class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-surface-container-lowest shadow-sm">
        <span class="material-symbols-outlined text-[18px] text-secondary">calendar_today</span>
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

      <div class="flex items-center gap-2">
        <a href="{{ route('direktur.export-pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
           class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-lowest shadow-sm hover:bg-surface-container-low transition-colors text-primary font-title-sm text-title-sm">
          <span class="material-symbols-outlined text-[18px] text-primary">picture_as_pdf</span>
          Unduh PDF
        </a>
        <a href="{{ route('direktur.export-excel', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
           class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">
          <span class="material-symbols-outlined text-[18px] text-secondary-container">download</span>
          Unduh Excel
        </a>
      </div>
    </div>

    {{-- KPI Cards --}}
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
          <span class="p-1.5 rounded-lg bg-secondary/10 text-secondary"><span class="material-symbols-outlined text-[20px]">how_to_reg</span></span>
        </div>
        <div class="font-headline-lg text-headline-lg text-secondary tabular-nums">{{ $rataKehadiran }}%</div>
        <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Hadir {{ $totalHadir }} &bull; Dinas {{ $totalPerdin }}</div>
      </div>

      <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between pb-space-xs">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Alpha/Mangkir</span>
          <span class="p-1.5 rounded-lg bg-error-container text-error"><span class="material-symbols-outlined text-[20px]">person_off</span></span>
        </div>
        <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ $totalAlpha }}</div>
      </div>

      <div class="p-space-base rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between pb-space-xs">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Menit Telat</span>
          <span class="p-1.5 rounded-lg bg-surface-container-low text-primary"><span class="material-symbols-outlined text-[20px]">timer</span></span>
        </div>
        <div class="font-headline-lg text-headline-lg text-primary tabular-nums">{{ number_format($totalMenitTelat) }}</div>
        <div class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">&asymp; {{ round($totalMenitTelat / 60, 1) }} jam se-perusahaan</div>
      </div>
    </div>

    {{-- Bagan struktur: Direktur Utama -> 5 Divisi real (bukan entitas fiktif dari mockup) --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-xl border border-surface-container-low">
      <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
        <span class="material-symbols-outlined text-secondary text-[24px]">account_tree</span>
        <h2 class="font-headline-md text-headline-md text-primary">Hierarki &amp; Kesehatan Presensi per Divisi</h2>
      </div>

      <div class="py-space-base overflow-x-auto">
        <div class="min-w-[900px] flex flex-col items-center">
          <div class="relative flex flex-col items-center">
            <div class="w-72 bg-[#70bce6] text-primary p-3 rounded-xl shadow-md border-2 border-[#54a4d4] flex flex-col items-center text-center">
              <span class="font-label-sm text-[10px] tracking-wider uppercase font-bold text-primary/80">Puncak Pimpinan Perseroan</span>
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
              <div class="w-full bg-[#0f2942] text-white p-2.5 rounded-lg shadow-sm border border-slate-700 flex flex-col gap-1 hover:bg-[#163859] transition-colors">
                <span class="font-title-sm text-xs font-semibold text-white leading-tight">{{ $row['divisi'] }}</span>
                <div class="flex items-center justify-between">
                  <span class="text-[10px] text-slate-300">{{ $row['total_personel'] }} Karyawan</span>
                  <span class="text-xs font-bold {{ $row['perlu_ditinjau'] ? 'text-error-container' : 'text-secondary-container' }}">{{ $row['rata_kehadiran'] }}%</span>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Matriks tabel per divisi --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
      <h2 class="font-headline-md text-headline-md text-primary mb-space-base">Matriks Kehadiran per Divisi</h2>
      <table class="w-full text-left">
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
                <span class="px-2 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-semibold">Normal</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </main>
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
</script>
</body>
</html>