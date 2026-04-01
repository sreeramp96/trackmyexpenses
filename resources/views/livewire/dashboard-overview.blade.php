<div class="space-y-6">
    <!-- Filters & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
            <select wire:model.live="month" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
            <select wire:model.live="year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="downloadCsv" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Income -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Income</p>
                    <p class="text-2xl font-bold text-green-600">₹{{ number_format($summary['income'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
        </div>

        <!-- Expense -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Expense</p>
                    <p class="text-2xl font-bold text-red-600">₹{{ number_format($summary['expense'], 2) }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
        </div>

        <!-- Net Savings -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Net Savings</p>
                    <p class="text-2xl font-bold {{ $summary['net'] >= 0 ? 'text-indigo-600' : 'text-orange-600' }}">
                        ₹{{ number_format($summary['net'], 2) }}
                    </p>
                </div>
                <div class="p-3 bg-indigo-50 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Savings Rate -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Savings Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['savings_rate'] }}%</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense by Category Chart -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense by Category</h3>
            <div class="h-64" x-data="{
                init() {
                    new Chart($refs.canvas, {
                        type: 'doughnut',
                        data: {
                            labels: {{ json_encode($categorySpending->pluck('category')) }},
                            datasets: [{
                                data: {{ json_encode($categorySpending->pluck('total')) }},
                                backgroundColor: {{ json_encode($categorySpending->pluck('color')) }},
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'right' }
                            }
                        }
                    });
                }
            }">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Recent Debts -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Unsettled Debts</h3>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    {{ $debtsSummary['unsettled_count'] }} Active
                </span>
            </div>
            <div class="space-y-4">
                @forelse($activeDebts as $debt)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <div>
                            <p class="font-medium text-gray-900">{{ $debt['contact_name'] }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ $debt['label'] }} • Due: {{ $debt['due_date'] ?? 'No date' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">₹{{ number_format($debt['remaining_amount'], 2) }}</p>
                            @if($debt['is_overdue'])
                                <span class="text-[10px] text-red-600 font-bold uppercase">Overdue</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No active debts found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Budgets Row -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Budget Progress</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($budgets as $budget)
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-700">{{ $budget['category'] }}</span>
                        <span class="text-gray-500">₹{{ number_format($budget['spent_amount']) }} / ₹{{ number_format($budget['budgeted_amount']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $budget['is_over_budget'] ? 'bg-red-600' : 'bg-indigo-600' }}" 
                             style="width: {{ min(100, $budget['percent_used']) }}%"></div>
                    </div>
                    @if($budget['is_over_budget'])
                        <p class="text-xs text-red-600 font-medium">Over budget by ₹{{ number_format($budget['spent_amount'] - $budget['budgeted_amount'], 2) }}</p>
                    @endif
                </div>
            @empty
                <p class="text-center text-gray-500 py-4 col-span-2">No active budgets for this period.</p>
            @endforelse
        </div>
    </div>

    <!-- Chart.js Script (Should be in layout usually, but for speed adding here or assuming layout) -->
    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endpush
    @endonce
</div>
