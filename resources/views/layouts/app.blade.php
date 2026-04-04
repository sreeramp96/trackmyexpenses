<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TrackMyExpenses' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .loading-bar {
            height: 2px;
            background: #2563eb;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="bg-canvas text-ink font-sans antialiased" x-data="{ collapsed: false, mobileOpen: false }" x-cloak>
    <x-flash-messages />

    {{-- Global Loading Indicator --}}
    <div wire:loading.delay.shortest class="loading-bar w-full"></div>

    <div class="flex h-screen overflow-hidden relative">

        {{-- Mobile Backdrop --}}
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 bg-ink/40 z-40 md:hidden"></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 bg-surface border-r border-edge flex flex-col transition-all duration-300 transform md:relative"
               :class="{
                   'w-48 translate-x-0': !collapsed && !mobileOpen,
                   'w-16 translate-x-0': collapsed && !mobileOpen,
                   'w-48 translate-x-0': mobileOpen,
                   '-translate-x-full md:translate-x-0': !mobileOpen
               }">

            <div class="flex items-center justify-between px-4 py-3 border-b border-edge h-12">
                <div class="flex items-center gap-2 overflow-hidden">
                    <div class="w-6 h-6 bg-ink rounded flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" viewBox="0 0 12 12">
                            <path d="M6 1v10M1 6h10"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium tracking-tight truncate transition-opacity duration-300" :class="collapsed ? 'opacity-0 w-0' : 'opacity-100'">TrackMyExp</span>
                </div>
                <button @click="collapsed = !collapsed" class="hidden md:block text-ink-3 hover:text-ink transition-colors">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                </button>
                <button @click="mobileOpen = false" class="md:hidden text-ink-3 hover:text-ink transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="flex-1 py-2 overflow-y-auto custom-scrollbar">
                <p class="px-4 pt-3 pb-1 text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest transition-opacity duration-300" :class="collapsed ? 'opacity-0' : 'opacity-100'">
                    Overview</p>

                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="1" width="5" height="5" rx="1"/><rect x="8" y="1" width="5" height="5" rx="1"/><rect x="1" y="8" width="5" height="5" rx="1"/><rect x="8" y="8" width="5" height="5" rx="1"/></svg>
                    </x-slot>
                    Dashboard
                </x-sidebar-link>

                <x-sidebar-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 4h12M1 7h8M1 10h5"/></svg>
                    </x-slot>
                    Transactions
                </x-sidebar-link>

                <x-sidebar-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="1" y="3" width="12" height="8" rx="1.5"/><path d="M1 6h12"/></svg>
                    </x-slot>
                    Accounts
                </x-sidebar-link>

                <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 4h5l1 1h6v6a1 1 0 01-1 1H2a1 1 0 01-1-1V4z"/></svg>
                    </x-slot>
                    Categories
                </x-sidebar-link>

                <x-sidebar-link :href="route('import.csv')" :active="request()->routeIs('import.csv')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7 10V1M4 4l3-3 3 3M1 10v3h12v-3"/></svg>
                    </x-slot>
                    Import CSV
                </x-sidebar-link>

                <x-sidebar-link :href="route('import.pdf')" :active="request()->routeIs('import.pdf')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M1 3h12v8a1 1 0 01-1 1H2a1 1 0 01-1-1V3zM4 1h6M1 6h12"/></svg>
                    </x-slot>
                    Import PDF
                </x-sidebar-link>

                <div class="h-px bg-edge my-2 mx-4" :class="collapsed ? 'mx-2' : 'mx-4'"></div>
                <p class="px-4 pb-1 text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest transition-opacity duration-300" :class="collapsed ? 'opacity-0' : 'opacity-100'">Planning</p>

                <x-sidebar-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="7" cy="7" r="5.5"/><path d="M7 3.5v3.5l2.5 1.5"/></svg>
                    </x-slot>
                    Budgets
                </x-sidebar-link>

                <x-sidebar-link :href="route('debts.index')" :active="request()->routeIs('debts.*')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7 1v12M3 5l4-4 4 4"/></svg>
                    </x-slot>
                    Debts
                </x-sidebar-link>

                <x-sidebar-link :href="route('settings')" :active="request()->routeIs('settings')">
                    <x-slot name="icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7 9a2 2 0 100-4 2 2 0 000 4z"/><path d="M12.94 6.07a5.29 5.29 0 00-.3-1.22l1.2-.7a.38.38 0 00.14-.47l-1-1.73a.38.38 0 00-.47-.14l-1.2.7a5.39 5.39 0 00-1.06-.61V.62a.38.38 0 00-.38-.38h-2a.38.38 0 00-.38.38v1.28a5.39 5.39 0 00-1.06.61l-1.2-.7a.38.38 0 00-.47.14l-1 1.73a.38.38 0 00.14.47l1.2.7a5.29 5.29 0 00-.3 1.22H1.06a.38.38 0 00-.38.38v2a.38.38 0 00.38.38h1.28a5.29 5.29 0 00.3 1.22l-1.2.7a.38.38 0 00-.14.47l1 1.73a.38.38 0 00.47.14l1.2-.7a5.39 5.39 0 001.06.61v1.28a.38.38 0 00.38.38h2a.38.38 0 00.38-.38v-1.28a5.39 5.39 0 001.06-.61l1.2.7a.38.38 0 00.47-.14l1-1.73a.38.38 0 00-.14-.47l-1.2-.7a5.29 5.29 0 00.3-1.22h1.28a.38.38 0 00.38-.38v-2a.38.38 0 00-.38-.38h-1.28z"/></svg>
                    </x-slot>
                    Settings
                </x-sidebar-link>
            </nav>

            <div class="border-t border-edge px-4 py-3 flex items-center justify-between group overflow-hidden">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-full bg-surface-3 border border-edge-2 flex items-center justify-center text-[10px] font-mono font-medium text-ink-2 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 transition-opacity duration-300" :class="collapsed ? 'opacity-0 w-0' : 'opacity-100'">
                        <p class="text-xs font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-ink-3 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" :class="collapsed ? 'hidden' : 'block'">
                    @csrf
                    <button type="submit" class="text-ink-3 hover:text-finance-red transition-colors p-1" title="Log out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M19 12l-3 3m3-3l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Mobile Header --}}
            <header class="md:hidden h-12 shrink-0 bg-surface border-b border-edge flex items-center justify-between px-4">
                <button @click="mobileOpen = true" class="text-ink-3 hover:text-ink p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <h1 class="text-sm font-medium">{{ $heading ?? 'Dashboard' }}</h1>
                <div class="w-8"></div> {{-- Spacer --}}
            </header>

            {{-- Desktop Header --}}
            <header class="hidden md:flex h-12 shrink-0 bg-surface border-b border-edge items-center justify-between px-5">
                <h1 class="text-sm font-medium transition-all duration-300" :class="collapsed ? 'ml-2' : ''">{{ $heading ?? 'Dashboard' }}</h1>
                <div class="flex items-center gap-2">
                    {{ $actions ?? '' }}
                    @livewire('transaction-form-modal')
                </div>
            </header>

            <main class="flex-1 overflow-y-auto custom-scrollbar" :class="collapsed ? 'md:pl-0' : ''">
                <div class="p-4 md:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
