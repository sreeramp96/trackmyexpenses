<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackMyExpenses - Smart Personal Finance Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body{
            font-family: 'Bricolage Grotesque', sans-serif;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-slideInLeft { animation: slideInLeft 0.8s ease-out forwards; }
        .animate-pulse-subtle { animation: pulse-subtle 2s ease-in-out infinite; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .gradient-text {
            background: linear-gradient(135deg, #1e3a5f 0%, #166534 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-canvas text-ink font-sans antialiased overflow-x-hidden">

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-surface/80 backdrop-blur-md border-b border-edge">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center gap-3 animate-slideInLeft">
                <div class="w-8 h-8 bg-ink rounded-lg flex items-center justify-center shrink-0 transform hover:rotate-12 transition-transform duration-300">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 12 12" stroke-linecap="round">
                        <path d="M6 1v10M1 6h10"/>
                    </svg>
                </div>
                <span class="text-xl font-semibold tracking-tight">TrackMyExpenses</span>
            </div>

            <div class="flex items-center gap-3 animate-fadeInUp">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-ink-2 hover:text-ink transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 14 14" stroke-linecap="round">
                            <rect x="1" y="1" width="5" height="5" rx="1"/>
                            <rect x="8" y="1" width="5" height="5" rx="1"/>
                            <rect x="1" y="8" width="5" height="5" rx="1"/>
                            <rect x="8" y="8" width="5" height="5" rx="1"/>
                        </svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="sm:inline-flex items-center px-4 py-2 text-sm font-medium text-ink-2 hover:text-ink transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-ink text-white text-sm font-medium rounded-lg hover:bg-ink-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Get Started
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                            <path d="M2 6h8M7 3l3 3-3 3"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-finance-green/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-finance-blue/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="space-y-4 opacity-0 animate-fadeInUp">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-finance-green-bg border border-finance-green-border rounded-full text-xs font-sans font-medium text-finance-green">
                        <span class="w-2 h-2 bg-finance-green rounded-full animate-pulse-subtle"></span>
                        Smart Finance Management
                    </div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-tight">
                        Take Control of Your
                        <span class="gradient-text">Finances</span>
                    </h1>
                    <p class="text-xl text-ink-2 leading-relaxed">
                        Track expenses, manage budgets, and achieve your financial goals with our powerful yet simple expense tracker.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 opacity-0 animate-fadeInUp delay-200">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-ink text-white text-base font-medium rounded-lg hover:bg-ink-2 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                            Go to Dashboard
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                                <path d="M2 6h8M7 3l3 3-3 3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-ink text-white text-base font-medium rounded-lg hover:bg-ink-2 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                            Start Free Today
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                                <path d="M2 6h8M7 3l3 3-3 3"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-surface border-2 border-edge text-ink text-base font-medium rounded-lg hover:bg-surface-2 transition-all duration-300">
                            Sign In
                        </a>
                    @endauth
                </div>

                <div class="flex items-center gap-8 opacity-0 animate-fadeInUp delay-300">
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">100%</div>
                        <div class="text-xs text-ink-3 font-sans uppercase tracking-wider">Free Forever</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">∞</div>
                        <div class="text-xs text-ink-3 font-sans uppercase tracking-wider">Transactions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold gradient-text">24/7</div>
                        <div class="text-xs text-ink-3 font-sans uppercase tracking-wider">Access</div>
                    </div>
                </div>
            </div>

            <div class="relative opacity-0 animate-fadeInUp delay-400">
                <div class="relative z-10 bg-surface border border-edge rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-transform duration-500">
                    <div class="bg-surface-2 border-b border-edge px-6 py-4 flex items-center gap-2">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-finance-red"></div>
                            <div class="w-3 h-3 rounded-full bg-finance-amber"></div>
                            <div class="w-3 h-3 rounded-full bg-finance-green"></div>
                        </div>
                        <span class="text-xs font-sans text-ink-3 ml-4">Dashboard Preview</span>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-finance-green-bg border border-finance-green-border p-4 rounded-lg">
                                <div class="text-[10px] font-sans font-bold text-finance-green uppercase tracking-widest mb-1">Income</div>
                                <div class="text-2xl font-sans font-medium text-finance-green">₹45,000</div>
                            </div>
                            <div class="bg-finance-red-bg border border-finance-red-border p-4 rounded-lg">
                                <div class="text-[10px] font-sans font-bold text-finance-red uppercase tracking-widest mb-1">Expenses</div>
                                <div class="text-2xl font-sans font-medium text-finance-red">₹32,450</div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-ink-2">Food & Dining</span>
                                <span class="font-sans text-ink">75%</span>
                            </div>
                            <div class="h-2 bg-surface-3 rounded-full overflow-hidden">
                                <div class="h-full bg-finance-green rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-ink-2">Transportation</span>
                                <span class="font-sans text-ink">45%</span>
                            </div>
                            <div class="h-2 bg-surface-3 rounded-full overflow-hidden">
                                <div class="h-full bg-finance-blue rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-ink-2">Entertainment</span>
                                <span class="font-sans text-ink">90%</span>
                            </div>
                            <div class="h-2 bg-surface-3 rounded-full overflow-hidden">
                                <div class="h-full bg-finance-amber rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-finance-blue/20 rounded-full blur-2xl -z-10"></div>
                <div class="absolute -top-4 -left-4 w-32 h-32 bg-finance-green/20 rounded-full blur-2xl -z-10"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-surface">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 opacity-0 animate-fadeInUp">
            <h2 class="text-4xl sm:text-5xl font-bold mb-4">Everything You Need</h2>
            <p class="text-xl text-ink-2">Powerful features to manage your money effectively</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-100">
                <div class="w-14 h-14 bg-finance-green-bg border border-finance-green-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-green" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Track Expenses</h3>
                <p class="text-ink-2 leading-relaxed">Record and categorize every transaction with ease. Know exactly where your money goes.</p>
            </div>

            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-200">
                <div class="w-14 h-14 bg-finance-blue-bg border border-finance-blue-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-blue" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Budget Planning</h3>
                <p class="text-ink-2 leading-relaxed">Set monthly budgets for different categories and get alerts when you're close to limits.</p>
            </div>

            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-300">
                <div class="w-14 h-14 bg-finance-amber-bg border border-finance-amber-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-amber" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Visual Reports</h3>
                <p class="text-ink-2 leading-relaxed">Beautiful charts and graphs to understand your spending patterns at a glance.</p>
            </div>

            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-100">
                <div class="w-14 h-14 bg-finance-red-bg border border-finance-red-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="6" width="18" height="12" rx="2"/>
                        <path stroke-linecap="round" d="M3 10h18"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Multiple Accounts</h3>
                <p class="text-ink-2 leading-relaxed">Manage bank accounts, cash, credit cards, and wallets all in one place.</p>
            </div>

            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-200">
                <div class="w-14 h-14 bg-finance-green-bg border border-finance-green-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-green" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Debt Tracking</h3>
                <p class="text-ink-2 leading-relaxed">Keep track of money lent and borrowed. Never forget who owes what.</p>
            </div>

            <div class="card-hover bg-white border border-edge rounded-xl p-8 opacity-0 animate-fadeInUp delay-300">
                <div class="w-14 h-14 bg-finance-blue-bg border border-finance-blue-border rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-finance-blue" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3">Export Data</h3>
                <p class="text-ink-2 leading-relaxed">Download your financial data as CSV for further analysis or backup.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-finance-blue/5 to-finance-green/5"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <div class="opacity-0 animate-fadeInUp space-y-8">
            <h2 class="text-4xl sm:text-5xl font-bold">Ready to Take Control?</h2>
            <p class="text-xl text-ink-2">Join thousands of users managing their finances smarter.</p>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-ink text-white text-lg font-medium rounded-lg hover:bg-ink-2 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                    Go to Your Dashboard
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                        <path d="M2 6h8M7 3l3 3-3 3"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-ink text-white text-lg font-medium rounded-lg hover:bg-ink-2 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                    Get Started Free
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                        <path d="M2 6h8M7 3l3 3-3 3"/>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-surface border-t border-edge py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-ink rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 12 12" stroke-linecap="round">
                            <path d="M6 1v10M1 6h10"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold">TrackMyExpenses</span>
                </div>
                <p class="text-ink-2 text-sm leading-relaxed max-w-md">
                    A simple, powerful expense tracker to help you manage your personal finances with ease.
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-sm">Quick Links</h4>
                <ul class="space-y-2 text-sm text-ink-2">
                    <li><a href="#" class="hover:text-ink transition-colors">Features</a></li>
                    <li><a href="#" class="hover:text-ink transition-colors">Pricing</a></li>
                    <li><a href="#" class="hover:text-ink transition-colors">About</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-sm">Support</h4>
                <ul class="space-y-2 text-sm text-ink-2">
                    <li><a href="#" class="hover:text-ink transition-colors">Help Center</a></li>
                    <li><a href="#" class="hover:text-ink transition-colors">Privacy</a></li>
                    <li><a href="#" class="hover:text-ink transition-colors">Terms</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-edge pt-8 text-center text-sm text-ink-3">
            <p>&copy; {{ date('Y') }} TrackMyExpenses. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
