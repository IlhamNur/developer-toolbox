<!DOCTYPE html>
<html lang="en" x-data="toolboxShell()" x-bind:class="themeClass" x-init="init()">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'HMNR Developer Toolbox') }}</title>
        <script>
            (() => {
                const saved = localStorage.getItem('hmnr-theme') || 'system';
                const dark = saved === 'dark' || (saved === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-canvas text-ink antialiased">
        {{ $slot ?? '' }}
        @yield('content')
        @livewireScripts
    </body>
</html>