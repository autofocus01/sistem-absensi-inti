<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Absensi Saya - PT. INTI')</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.googleapis.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: { extend: {
      colors: {
        'on-surface-variant':'#43474d','primary':'#001428','on-primary':'#ffffff',
        'surface-container-low':'#eff4ff','on-background':'#0d1c2e','surface-container-highest':'#d5e3fc',
        'on-surface':'#0d1c2e','outline-variant':'#c3c6ce','secondary':'#006a61','error-container':'#ffdad6',
        'background':'#f8f9ff','surface-bright':'#f8f9ff','surface':'#f8f9ff','outline':'#74777e',
        'error':'#ba1a1a','surface-container-lowest':'#ffffff','primary-container':'#0f2942',
        'surface-container':'#e6eeff','surface-container-high':'#dce9ff','secondary-container':'#86f2e4'
      },
      spacing: {'space-2xl':'3rem','space-md':'0.75rem','space-lg':'1.5rem','space-base':'1rem','space-sm':'0.5rem','space-xl':'2rem','space-xs':'0.25rem'},
      fontFamily: {body:['Plus Jakarta Sans'],title:['Plus Jakarta Sans']},
      fontSize: { 'body-md':['14px',{'lineHeight':'20px'}], 'body-sm':['12px',{'lineHeight':'16px'}], 'title-sm':['14px',{'lineHeight':'20px','fontWeight':'600'}], 'headline-md':['22px',{'lineHeight':'28px','fontWeight':'600'}], 'headline-lg':['28px',{'lineHeight':'36px','fontWeight':'600'}] }
    }}
  }
</script>
<style>
  @layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}}
  ::-webkit-scrollbar{display:none;}
  .material-symbols-outlined{font-family:'Material Symbols Outlined';font-weight:normal;font-style:normal;line-height:1;letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;direction:ltr;-webkit-font-feature-settings:'liga';-webkit-font-smoothing:antialiased;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
</style>
</head>
<body class="bg-background font-body text-body-md text-on-surface antialiased">
<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
<aside id="sidebar" class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between transform -translate-x-full transition-transform duration-200 ease-in-out md:translate-x-0">
  <div class="flex flex-col overflow-y-auto">
    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
      </a>
    </div>
    <div class="px-space-md py-space-md">
      <div class="px-space-sm pb-space-sm">
        <span class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">Portal Karyawan</span>
      </div>
      <nav class="flex flex-col gap-space-xs">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
          <span class="material-symbols-outlined text-[20px]">dashboard</span> Dashboard
        </a>
        <a href="{{ route('attendance.my') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm {{ request()->routeIs('attendance.my*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
          <span class="material-symbols-outlined text-[20px]">event_available</span> Absensi Saya
        </a>
        <a href="{{ route('overtime.index') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm {{ request()->routeIs('overtime.index') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
          <span class="material-symbols-outlined text-[20px]">more_time</span> Overtime
        </a>
        <a href="{{ route('timesheet.index') }}" class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
          <span class="material-symbols-outlined text-[20px]">calendar_clock</span> Riwayat &amp; Timesheet
        </a>
      </nav>
    </div>
  </div>
  <div class="p-space-md border-t border-surface-container-low">
    <div class="flex items-center gap-2 px-1 pb-3">
      <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-title-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="min-w-0">
        <div class="font-title-sm text-on-surface truncate">{{ auth()->user()->name }}</div>
        <div class="text-body-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</div>
      </div>
    </div>
    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-on-surface"><span class="material-symbols-outlined text-[18px]">account_circle</span> Profil Saya</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-error"><span class="material-symbols-outlined text-[18px]">logout</span> Logout</button>
    </form>
  </div>
</aside>
<div class="md:pl-72 flex flex-col min-h-screen">
  <header class="fixed top-0 left-0 md:left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-30 flex items-center px-4 md:px-space-xl">
    <div class="flex items-center gap-3">
      <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-primary" aria-label="Buka menu"><span class="material-symbols-outlined">menu</span></button>
      <span class="material-symbols-outlined text-[18px] text-secondary hidden sm:inline">badge</span>
      <span class="font-title-sm text-primary">Portal Karyawan</span>
    </div>
  </header>
  <main class="w-full pt-28 px-4 md:px-space-xl pb-space-2xl bg-background flex-1">
    @if (session('status'))
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-space-base px-space-base py-3 rounded-lg bg-error/10 text-error text-body-md">
        <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
    @yield('content')
  </main>
</div>
<script>function toggleSidebar(){document.getElementById('sidebar').classList.toggle('-translate-x-full');document.getElementById('sidebar-backdrop').classList.toggle('hidden');}</script>
@yield('scripts')
</body>
</html>
