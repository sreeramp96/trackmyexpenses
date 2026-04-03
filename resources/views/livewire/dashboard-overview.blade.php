<div>
    <x-slot name="heading">Dashboard</x-slot>
    <x-slot name="actions">
        <span
            class="hidden md:inline-block text-[11px] font-mono text-ink-3 border border-edge px-2 py-1 rounded bg-surface">{{ date('M Y', mktime(0,0,0,$month,1,$year)) }}</span>
        <select wire:model.live="month"
                class="text-xs border-edge bg-surface rounded pl-2 pr-8 py-1.5 font-mono text-ink-2 focus:ring-1 focus:ring-ink focus:border-ink outline-none transition-all">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
            @endforeach
        </select>
        <select wire:model.live="year"
                class="text-xs border-edge bg-surface rounded pl-2 pr-8 py-1.5 font-mono text-ink-2 focus:ring-1 focus:ring-ink focus:border-ink outline-none transition-all">
            @foreach(range(now()->year-2,now()->year) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
        <button wire:click="downloadCsv"
                class="flex items-center gap-1.5 text-xs border border-edge bg-surface text-ink-2 px-3 py-1.5 rounded hover:bg-surface-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v7M3 6l3 3 3-3M1 10h10"/>
            </svg>
            <span class="hidden sm:inline">Export</span>
        </button>
        <button onclick="Livewire.dispatch('open-tx-modal')"
                class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v10M1 6h10"/>
            </svg>
            <span class="hidden sm:inline">New transaction</span>
            <span class="sm:hidden">New</span>
        </button>
    </x-slot>

    <div class="p-5 space-y-6">
        {{-- KPI strip --}}
        <div
            class="grid grid-cols-2 md:grid-cols-5 border border-edge rounded overflow-hidden divide-x divide-y md:divide-y-0 divide-edge">
            <x-kpi-card label="Income" :value="'₹'.number_format($summary['income'],2)" color="green"
                        :sub="date('M Y',mktime(0,0,0,$month,1,$year))">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
            <x-kpi-card label="Expenses" :value="'₹'.number_format($summary['expense'],2)" color="red"
                        :sub="'of ₹'.number_format($budgetHealth['total_budgeted'],0).' budgeted'">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
            <x-kpi-card label="Net savings" :value="'₹'.number_format($summary['net'],2)" color="blue"
                        :sub="$summary['savings_rate'].'% savings rate'">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
            <x-kpi-card label="Budget used" :value="$budgetHealth['global_percent_used'].'%'" color="amber"
                        :sub="'₹'.number_format($budgetHealth['total_remaining'],0).' remaining'">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
            <x-kpi-card label="Net Worth" :value="'₹'.number_format($netWorth,0)" color="default"
                        :sub="'Assets - Liabilities'">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
        </div>

        {{-- Spending Trends Chart --}}
        <x-panel title="Daily Spending Trends — {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}" color="blue">
            <div class="p-4 h-[300px] font-sans">
                <x-chart type="line" :labels="$dailyTrends['labels']" :datasets="[
                    [
                        'label' => 'Expenses',
                        'data' => $dailyTrends['data'],
                        'borderColor' => '#991b1b',
                        'backgroundColor' => 'rgba(153, 27, 27, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]
                ]" :options="[
                    'scales' => [
                        'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                        'x' => ['grid' => ['display' => false]]
                    ]
                ]" />
            </div>
        </x-panel>

        {{-- Analysis Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-panel title="Category Breakdown" color="green">
                <div class="p-4 h-[300px]">
                    <x-chart type="doughnut" :labels="$categoryChart['labels']" :datasets="[
                        [
                            'data' => $categoryChart['data'],
                            'backgroundColor' => $categoryChart['colors'],
                            'borderWidth' => 0,
                        ]
                    ]" :options="[
                        'plugins' => [
                            'legend' => ['position' => 'right', 'labels' => ['boxWidth' => 12, 'font' => ['family' => 'IBM Plex Mono', 'size' => 10]]]
                        ]
                    ]" />
                </div>
            </x-panel>

            <x-panel title="Income vs Expense (Last 6 Months)" color="blue">
                <div class="p-4 h-[300px]">
                    <x-chart type="bar" :labels="$historicalChart['labels']" :datasets="[
                        [
                            'label' => 'Income',
                            'data' => $historicalChart['income'],
                            'backgroundColor' => '#166534',
                        ],
                        [
                            'label' => 'Expense',
                            'data' => $historicalChart['expense'],
                            'backgroundColor' => '#991b1b',
                        ]
                    ]" :options="[
                        'scales' => [
                            'y' => ['beginAtZero' => true, 'grid' => ['display' => false]],
                            'x' => ['grid' => ['display' => false]]
                        ]
                    ]" />
                </div>
            </x-panel>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)] gap-4">

            {{-- Recent transactions table --}}
            <x-panel title="Recent transactions" color="blue">
                <x-slot name="action">
                    <a href="{{ route('transactions.index') }}"
                       class="text-[10px] text-finance-blue hover:underline font-bold uppercase tracking-wider">View
                        all →</a>
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse min-w-[500px]">
                        <thead class="bg-surface-2">
                        <tr>
                            <th class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Description
                            </th>
                            <th class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Category
                            </th>
                            <th class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Amount
                            </th>
                            <th class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Date
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentTransactions as $tx)
                            <tr class="border-b border-edge last:border-0 hover:bg-surface-2 transition-colors cursor-pointer"
                                onclick="Livewire.dispatch('open-tx-modal', {id: {{ $tx->id }}})">
                                <td class="px-3.5 py-2 font-medium">{{ $tx->note ?? '—' }}</td>
                                <td class="px-3.5 py-2 text-ink-3">{{ $tx->category?->name ?? 'Uncategorized' }}</td>
                                <td class="px-3.5 py-2 text-right font-mono {{ $tx->type === 'income' ? 'text-finance-green' : ($tx->type === 'transfer' ? 'text-finance-blue' : 'text-finance-red') }}">
                                    {{ $tx->type === 'expense' ? '−' : '+' }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="px-3.5 py-2 font-mono text-ink-3">{{ $tx->transaction_date->format('M d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </x-panel>

            {{-- Right column --}}
            <div class="space-y-4">
                {{-- Budget bars --}}
                <x-panel title="Budget utilisation" color="green">
                    <div class="px-3.5 py-3 space-y-3">
                        @forelse($budgets as $b)
                            <div>
                                <div class="flex justify-between items-baseline mb-1">
                                    <span class="text-xs">{{ $b['category'] }}</span>
                                    <span class="text-[10px] font-mono text-ink-3">{{ $b['percent_used'] }}%</span>
                                </div>
                                <div class="h-1 bg-surface-3 rounded-none overflow-hidden">
                                    <div class="h-full transition-all"
                                         style="width: {{ min(100,$b['percent_used']) }}%; background: {{ $b['is_over_budget'] ? '#991b1b' : ($b['percent_used'] > 85 ? '#92400e' : '#166534') }}">
                                    </div>
                                </div>
                                <div class="flex justify-between mt-0.5">
                                    <span
                                        class="text-[10px] font-mono text-ink-3">₹{{ number_format($b['spent_amount'],0) }}</span>
                                    <span
                                        class="text-[10px] font-mono text-ink-3">₹{{ number_format($b['budgeted_amount'],0) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-ink-3 text-center py-2">No active budgets.</p>
                        @endforelse
                    </div>
                </x-panel>

                {{-- Debts --}}
                <x-panel title="Active debts" color="red">
                    <x-slot name="action">
                        @if($debtsSummary['overdue_count'] > 0)
                            <x-badge type="red">{{ $debtsSummary['overdue_count'] }} overdue</x-badge>
                        @endif
                    </x-slot>
                    @forelse($activeDebts as $debt)
                        <div
                            class="flex items-center justify-between px-3.5 py-2 border-b border-edge last:border-0 hover:bg-surface-2">
                            <div>
                                <p class="text-xs font-medium">{{ $debt['contact_name'] }}</p>
                                <p class="text-[10px] font-mono text-ink-3">{{ $debt['label'] }}
                                    · {{ $debt['due_date'] ?? 'No date' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-mono font-medium {{ $debt['direction'] === 'lent' ? 'text-finance-green' : 'text-finance-red' }}">
                                    ₹{{ number_format($debt['remaining_amount'], 0) }}
                                </p>
                                @if($debt['is_overdue'])
                                    <x-badge type="red">Overdue</x-badge>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-ink-3 text-center px-3.5 py-3">No active debts.</p>
                    @endforelse
                </x-panel>
            </div>
        </div>

        {{-- Full category breakdown --}}
        <x-panel title="Category breakdown — {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}" color="blue">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse min-w-[600px]">
                    <thead class="bg-surface-2">
                    <tr>
                        <th class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                            Category
                        </th>
                        <th class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                            Spent
                        </th>
                        <th class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                            Budget
                        </th>
                        <th class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                            Remaining
                        </th>
                        <th class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                            Used %
                        </th>
                        <th class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge w-28">
                            Bar
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($budgets as $b)
                        <tr class="border-b border-edge last:border-0 hover:bg-surface-2">
                            <td class="px-3.5 py-2">{{ $b['category'] }}</td>
                            <td class="px-3.5 py-2 text-right font-mono {{ $b['is_over_budget'] ? 'text-finance-red' : 'text-finance-green' }}">
                                ₹{{ number_format($b['spent_amount'],0) }}</td>
                            <td class="px-3.5 py-2 text-right font-mono text-ink-2">
                                ₹{{ number_format($b['budgeted_amount'],0) }}</td>
                            <td class="px-3.5 py-2 text-right font-mono {{ $b['is_over_budget'] ? 'text-finance-red' : 'text-ink-2' }}">
                                {{ $b['is_over_budget'] ? '−' : '' }}₹{{ number_format(abs($b['remaining_amount']),0) }}
                            </td>
                            <td class="px-3.5 py-2 text-right font-mono {{ $b['is_over_budget'] ? 'text-finance-red' : ($b['percent_used']>85 ? 'text-finance-amber' : 'text-ink-2') }}">
                                {{ $b['percent_used'] }}%
                            </td>
                            <td class="px-3.5 py-2">
                                <div class="h-1 bg-surface-3 overflow-hidden">
                                    <div class="h-full"
                                         style="width:{{ min(100,$b['percent_used']) }}%;background:{{ $b['is_over_budget']?'#991b1b':($b['percent_used']>85?'#92400e':'#166534') }}"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>
    </div>
</div>
