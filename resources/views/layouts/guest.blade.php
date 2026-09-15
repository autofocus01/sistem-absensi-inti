<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Absensi PT. INTI') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: {
        colors: {"primary":"#001428","primary-container":"#0f2942","on-primary":"#ffffff","secondary":"#006a61",
          "background":"#f8f9ff","surface-container-lowest":"#ffffff","surface-container-low":"#eff4ff",
          "on-surface":"#0d1c2e","on-surface-variant":"#43474d","error":"#ba1a1a"},
        fontFamily: { sans: ["Plus Jakarta Sans"] }
      }}}
    </script>
</head>
<body class="bg-background font-sans text-on-surface antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
        <div class="flex flex-col items-center gap-3 mb-2">
            <img src="{{ asset('images/logo-inti.png') }}" alt="PT. INTI" class="h-10 w-auto object-contain">
            <span class="font-semibold text-primary text-sm tracking-wide uppercase">Sistem Absensi PT. INTI</span>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-surface-container-lowest shadow-sm rounded-2xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>