<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>
            (function () {
                const theme = localStorage.getItem('inti-theme') || 'system';
                const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.dataset.theme = theme;
            })();
        </script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html.dark body { background:#141c29 !important; color:#edf3fa !important; }
            html.dark .bg-white { background:#1b2636 !important; }
            html.dark .bg-gray-100 { background:#141c29 !important; }
            html.dark .bg-gray-800 { background:#1b2636 !important; }
            html.dark .bg-gray-900 { background:#111b2b !important; }
            html.dark .text-gray-900, html.dark .text-gray-800, html.dark .text-gray-700 { color:#edf3fa !important; }
            html.dark .text-gray-600, html.dark .text-gray-500, html.dark .text-gray-400 { color:#b7c3d2 !important; }
            html.dark input, html.dark textarea, html.dark select { background:#111b2b !important; color:#edf3fa !important; border-color:#52657d !important; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
