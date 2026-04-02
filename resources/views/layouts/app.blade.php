<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TrackMyExpenses' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Code:ital,wght@0,300..800;1,300..800&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-canvas text-ink font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <aside class="w-48 shrink-0 bg-surface border-r border-edge flex flex-col">

        <div class="flex items-center gap-2 px-4 py-3 border-b border-edge">
            <div class="w-6 h-6 bg-ink rounded flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" viewBox="0 0 12 12">
                    <path d="M6 1v10M1 6h10"/>
                </svg>
            </div>
            <span class="text-sm font-medium tracking-tight">TrackMyExp</span>
        </div>

        <nav class="flex-1 py-2">
            <p class="px-4 pt-3 pb-1 text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest">
                Overview</p>

            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-slot name="icon">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round">
                        <rect x="1" y="1" width="5" height="5" rx="1"/>
                        <rect x="8" y="1" width="5" height="5" rx="1"/>
                        <rect x="1" y="8" width="5" height="5" rx="1"/>
                        <rect x="8" y="8" width="5" height="5" rx="1"/>
                    </svg>
                </x-slot>
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round">
                        <path d="M1 4h12M1 7h8M1 10h5"/>
                    </svg>
                </x-slot>
                Transactions
            </x-sidebar-link>

            <x-sidebar-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round">
                        <rect x="1" y="3" width="12" height="8" rx="1.5"/>
                        <path d="M1 6h12"/>
                    </svg>
                </x-slot>
                Accounts
            </x-sidebar-link>

            <div class="h-px bg-edge my-2 mx-4"></div>
            <p class="px-4 pb-1 text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest">Planning</p>

            <x-sidebar-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round">
                        <circle cx="7" cy="7" r="5.5"/>
                        <path d="M7 3.5v3.5l2.5 1.5"/>
                    </svg>
                </x-slot>
                Budgets
            </x-sidebar-link>

            <x-sidebar-link :href="route('debts.index')" :active="request()->routeIs('debts.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"
                         stroke-linecap="round">
                        <path d="M7 1v12M3 5l4-4 4 4"/>
                    </svg>
                </x-slot>
                Debts
            </x-sidebar-link>
        </nav>

        <div class="border-t border-edge px-4 py-3 flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-full bg-surface-3 border border-edge-2 flex items-center justify-center text-[10px] font-mono font-medium text-ink-2 shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-ink-3 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="h-10 shrink-0 bg-surface border-b border-edge flex items-center justify-between px-5">
            <h1 class="text-sm font-medium">{{ $heading ?? 'Dashboard' }}</h1>
            <div class="flex items-center gap-2">
                {{ $actions ?? '' }}
                @livewire('transaction-form-modal')
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
