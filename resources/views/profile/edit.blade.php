<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - PT. INTI</title>
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
        "space-base":"1rem","space-sm":"0.5rem","space-xl":"2rem","space-xs":"0.25rem","space-2xs":"0.125rem"},
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
    font-family:'Material Symbols Outlined';font-weight:normal;font-style:normal;line-height:1;
    letter-spacing:normal;text-transform:none;display:inline-block;white-space:nowrap;word-wrap:normal;
    direction:ltr;-webkit-font-feature-settings:'liga';-webkit-font-smoothing:antialiased;
    font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
  }
</style>
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<header class="h-20 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] flex items-center justify-between px-4 md:px-space-xl sticky top-0 z-30">
  <div class="flex items-center gap-space-md">
    <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-8 w-auto object-contain">
    <div class="h-6 w-[1px] bg-surface-container-low hidden sm:block"></div>
    <span class="font-title-sm text-title-sm text-primary hidden sm:inline">Profil Saya</span>
  </div>
  <a href="{{ auth()->user()->isDirekturUtama() ? route('direktur.dashboard') : route('employees.index') }}"
     class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-container-low text-primary font-title-sm text-title-sm hover:bg-surface-container transition-colors">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
    <span class="hidden sm:inline">Kembali ke Dashboard</span>
  </a>
</header>

<main class="max-w-4xl mx-auto px-4 md:px-space-xl py-space-xl">

  <div class="rounded-2xl bg-primary-container p-space-xl mb-space-xl flex flex-col sm:flex-row sm:items-center gap-space-lg">
    <div class="w-20 h-20 rounded-2xl bg-surface-container-lowest/10 flex items-center justify-center text-on-primary font-headline-lg text-headline-lg shrink-0">
      {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
    <div class="flex flex-col gap-1">
      <span class="font-label-sm text-label-sm text-surface-variant uppercase tracking-wider">Portal Kepegawaian &amp; Infrastruktur Presensi</span>
      <h1 class="font-headline-lg text-headline-lg text-on-primary tracking-tight">{{ auth()->user()->name }}</h1>
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-surface-container-lowest/10 text-on-primary font-label-sm text-label-sm font-semibold w-fit">
        {{ auth()->user()->isDirekturUtama() ? 'Direktur Utama' : 'HR & Administrasi' }}
      </span>
    </div>
  </div>

  @if (session('status') === 'profile-updated')
    <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
      Profil berhasil diperbarui.
    </div>
  @endif
  @if (session('status') === 'password-updated')
    <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
      Password berhasil diperbarui.
    </div>
  @endif

  <div class="w-full bg-surface-container-lowest rounded-xl p-1.5 mb-space-xl shadow-sm overflow-x-auto">
    <div class="flex items-center gap-1 min-w-max">
      <button type="button" onclick="showTab('biodata')" id="tabbtn-biodata"
              class="px-space-base py-space-sm rounded-lg bg-primary text-on-primary font-title-sm text-title-sm flex items-center gap-space-xs transition-all">
        <span class="material-symbols-outlined text-[18px]">badge</span> Biodata Akun
      </button>
      <button type="button" onclick="showTab('keamanan')" id="tabbtn-keamanan"
              class="px-space-base py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-title-sm text-title-sm flex items-center gap-space-xs transition-all">
        <span class="material-symbols-outlined text-[18px]">lock</span> Keamanan
      </button>
    </div>
  </div>

  <div id="tab-biodata" class="flex flex-col gap-space-xl">

    @php($employee = auth()->user()->employee)

    <div class="bg-surface-container-lowest rounded-2xl p-space-xl shadow-sm">
      <div class="flex items-center gap-space-sm pb-space-lg">
        <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
          <span class="material-symbols-outlined text-[22px]">badge</span>
        </div>
        <div class="flex flex-col">
          <span class="font-headline-sm text-headline-sm text-on-surface">Data Kepegawaian</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Jabatan &amp; divisi berdasarkan data karyawan yang terhubung</span>
        </div>
      </div>

      @if ($employee)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-base">
          <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">NIPEG</span>
            <span class="font-title-md text-title-md text-on-surface">{{ $employee->nipeg }}</span>
          </div>
          <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Jabatan</span>
            <span class="font-title-md text-title-md text-on-surface">{{ $employee->jabatan ?: '-' }}</span>
          </div>
          <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Divisi</span>
            <span class="font-title-md text-title-md text-on-surface">{{ $employee->division->nama }}</span>
          </div>
        </div>
      @else
        <p class="font-body-md text-body-md text-on-surface-variant">
          Akun ini belum dihubungkan ke data karyawan manapun. Hubungi HR untuk menghubungkan akun ini
          lewat halaman <span class="font-semibold text-on-surface">Data Karyawan &rarr; Edit &rarr; Akun Login</span>,
          supaya Jabatan &amp; Divisi bisa tampil di sini.
        </p>
      @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-space-xl shadow-sm">
      <div class="flex items-center gap-space-sm pb-space-lg">
        <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
          <span class="material-symbols-outlined text-[22px]">domain</span>
        </div>
        <div class="flex flex-col">
          <span class="font-headline-sm text-headline-sm text-on-surface">Kontak &amp; Lokasi Kantor</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Kantor Pusat &mdash; Bandung</span>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-base">
        <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low sm:col-span-2">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Alamat Kantor</span>
          <span class="font-title-md text-title-md text-on-surface">Jl. Moch. Toha No.77, Cigereleng, Kec. Regol, Kota Bandung, Jawa Barat 40253</span>
        </div>
        <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Telepon Kantor</span>
          <span class="font-title-md text-title-md text-on-surface">(022) 520-1501</span>
        </div>
        <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Email Korporat Anda</span>
          <span class="font-title-md text-title-md text-on-surface break-all">{{ auth()->user()->email }}</span>
        </div>
      </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl p-space-xl shadow-sm">
      <div class="flex items-center gap-space-sm pb-space-lg">
        <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
          <span class="material-symbols-outlined text-[22px]">contact_emergency</span>
        </div>
        <div class="flex flex-col">
          <span class="font-headline-sm text-headline-sm text-on-surface">Data Akun</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Nama &amp; email dipakai untuk login ke sistem</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-space-base mb-space-lg">
        <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Role</span>
          <span class="font-title-md text-title-md text-on-surface">{{ auth()->user()->isDirekturUtama() ? 'Direktur Utama' : 'HR & Administrasi' }}</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Diatur administrator, tidak bisa diubah sendiri</span>
        </div>
        <div class="flex flex-col gap-space-2xs p-space-md rounded-xl bg-surface-container-low">
          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Terdaftar Sejak</span>
          <span class="font-title-md text-title-md text-on-surface">{{ auth()->user()->created_at->translatedFormat('d F Y') }}</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Tanggal akun dibuat di sistem</span>
        </div>
      </div>

      <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-space-base max-w-lg">
        @csrf
        @method('PATCH')

        <div>
          <label class="block font-title-sm text-title-sm text-on-surface mb-1">Nama Lengkap</label>
          <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                 class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
          @error('name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block font-title-sm text-title-sm text-on-surface mb-1">Email</label>
          <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                 class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
          @error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <div id="tab-keamanan" class="hidden flex flex-col gap-space-xl">
    <div class="bg-surface-container-lowest rounded-2xl p-space-xl shadow-sm">
      <div class="flex items-center gap-space-sm pb-space-lg">
        <div class="w-10 h-10 rounded-xl bg-surface-container-low flex items-center justify-center text-primary">
          <span class="material-symbols-outlined text-[22px]">phonelink_lock</span>
        </div>
        <div class="flex flex-col">
          <span class="font-headline-sm text-headline-sm text-on-surface">Ganti Password</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Pastikan pakai password yang panjang &amp; unik</span>
        </div>
      </div>

      <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-space-base max-w-lg">
        @csrf
        @method('PUT')

        <div>
          <label class="block font-title-sm text-title-sm text-on-surface mb-1">Password Saat Ini</label>
          <input type="password" name="current_password" autocomplete="current-password"
                 class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
          @error('current_password', 'updatePassword') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block font-title-sm text-title-sm text-on-surface mb-1">Password Baru</label>
          <input type="password" name="password" autocomplete="new-password"
                 class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
          @error('password', 'updatePassword') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block font-title-sm text-title-sm text-on-surface mb-1">Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation" autocomplete="new-password"
                 class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
          @error('password_confirmation', 'updatePassword') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">Perbarui Password</button>
        </div>
      </form>
    </div>
  </div>

</main>

<script>
  function showTab(tab) {
    const tabs = ['biodata', 'keamanan'];
    tabs.forEach(function (t) {
      const isActive = t === tab;
      document.getElementById('tab-' + t).classList.toggle('hidden', !isActive);
      const btn = document.getElementById('tabbtn-' + t);
      btn.classList.toggle('bg-primary', isActive);
      btn.classList.toggle('text-on-primary', isActive);
      btn.classList.toggle('text-on-surface-variant', !isActive);
    });
    if (window.location.hash !== '#' + tab) {
      history.replaceState(null, '', '#' + tab);
    }
  }
  document.addEventListener('DOMContentLoaded', function () {
    const initial = window.location.hash === '#keamanan' ? 'keamanan' : 'biodata';
    showTab(initial);
  });
</script>
</body>
</html>