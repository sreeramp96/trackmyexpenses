<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TrackMyExpenses') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="bg-canvas text-ink font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-8">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-ink rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 12 12" stroke-linecap="round">
                        <path d="M6 1v10M1 6h10" />
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tighter">TrackMyExpenses</span>
            </a>
        </div>

    <div
        class="w-full sm:max-w-md px-8 py-10 bg-surface border border-edge shadow-xl sm:rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-ink opacity-10"></div>

        {{ $slot }}
    </div>
<div class="mt-8 text-center">
    <p class="text-[10px] font-mono text-ink-3 uppercase tracking-[0.2em]">Finance Minimalist v1.0</p>
</div>
</div>
</body>
</html>
