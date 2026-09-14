<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Otorisasi Khusus - PT. INTI</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-tertiary-container text-on-tertiary">BOD</span>
      </div>
      <nav class="flex flex-col gap-space-xs">
        <a class="flex items-center px-space-md py-space-sm rounded-lg font-title-sm text-on-surface hover:bg-surface-container-low" href="{{ route('direktur.dashboard') }}">
          Dashboard Direktur Utama
        </a>
        <a class="flex items-center justify-between px-space-md py-space-sm rounded-lg bg-primary-container text-on-primary font-title-sm" href="{{ route('direktur.otorisasi-khusus') }}">
          <span>Otorisasi Khusus</span>
          @if ($logs->count() > 0)
            <span class="px-1.5 py-0.5 rounded-full bg-error text-white text-[10px] font-bold">{{ $logs->count() }}</span>
          @endif
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
    <span class="font-title-sm text-title-sm text-on-surface">Log Absensi Harian Terverifikasi</span>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Ambang otorisasi: lembur &gt; 3 jam/hari</p>
  </div>
</aside>

<div class="md:pl-72 flex flex-col min-h-screen">
  <header class="fixed top-0 left-0 md:left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-4 md:px-space-xl">
    <div class="flex items-center gap-3">
      <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-primary" aria-label="Buka menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <span class="material-symbols-outlined text-[18px] text-secondary hidden sm:inline">shield</span>
      <span class="font-title-sm text-title-sm text-primary hidden sm:inline">Portal Eksekutif</span>
      <div class="h-5 w-[1px] bg-outline-variant/40 hidden lg:block"></div>
      <span class="hidden lg:inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm font-semibold">BUMN Holding IT &amp; Telco</span>
    </div>
    <div class="flex items-center gap-space-md">
      <div class="hidden xl:flex items-center gap-space-xs px-space-md py-1.5 rounded-full bg-surface-container-low">
        <span class="material-symbols-outlined text-[16px] text-secondary">schedule</span>
        <span id="jam-realtime" class="font-label-sm text-label-sm text-on-surface font-semibold tabular-nums">--:--:--</span>
        <span class="text-outline text-xs">&bull;</span>
        <span class="font-body-sm text-body-sm text-on-surface-variant">WIB</span>
      </div>
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

    <div class="flex flex-col gap-1 pb-space-lg">
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-container text-surface-container-lowest font-label-sm text-label-sm uppercase tracking-wider w-fit">
        <span class="material-symbols-outlined text-[14px]">assignment_turned_in</span> OTORISASI KHUSUS &bull; DIREKTUR UTAMA
      </span>
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Otorisasi Lembur Khusus</h1>
      <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
        Lembur harian di atas 3 jam butuh persetujuan langsung Direktur Utama, tidak cukup lewat approval HR biasa.
      </p>
    </div>

    @if (session('status'))
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
        {{ session('status') }}
      </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
      <table class="w-full text-left min-w-[800px]">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
            <th class="py-3 px-space-base rounded-l-lg">Tanggal</th>
            <th class="py-3 px-space-base">Karyawan</th>
            <th class="py-3 px-space-base">Divisi</th>
            <th class="py-3 px-space-base">Jam Pulang</th>
            <th class="py-3 px-space-base">Total Lembur</th>
            <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
          @forelse ($logs as $log)
          <tr class="hover:bg-surface-container-low/40 transition-colors">
            <td class="py-space-base px-space-base">{{ $log->tanggal->translatedFormat('d M Y') }}</td>
            <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $log->employee->nama }}</td>
            <td class="py-space-base px-space-base"><span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">{{ $log->employee->division->nama }}</span></td>
            <td class="py-space-base px-space-base tabular-nums">{{ \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') }}</td>
            <td class="py-space-base px-space-base tabular-nums font-title-sm text-title-sm text-error">{{ $log->lemburFormat() }}</td>
            <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
              <form action="{{ route('direktur.otorisasi-khusus.approve', $log) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-secondary font-title-sm text-title-sm">Setuju</button>
              </form>
              <form action="{{ route('direktur.otorisasi-khusus.reject', $log) }}" method="POST" class="inline" onsubmit="return confirm('Tolak lembur ini?')">
                @csrf
                <button type="submit" class="text-error font-title-sm text-title-sm">Tolak</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="py-8 px-4 text-center text-on-surface-variant">Tidak ada lembur yang butuh otorisasi khusus saat ini.</td></tr>
          @endforelse
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
</script>
</body>
</html>