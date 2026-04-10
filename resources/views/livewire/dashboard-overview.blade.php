<div>
    <x-slot name="heading">Dashboard</x-slot>
    <x-slot name="actions">
        <span
            class="hidden md:inline-block text-[11px] font-mono text-ink-3 border border-edge px-2 py-1 rounded bg-surface">{{ date('M Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }}</span>
        <select wire:model.live="month"
                class="text-xs border-edge bg-surface rounded pl-2 pr-8 py-1.5 font-mono text-ink-2 focus:ring-1 focus:ring-ink focus:border-ink outline-none transition-all">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endforeach
        </select>
        <select wire:model.live="year"
                class="text-xs border-edge bg-surface rounded pl-2 pr-8 py-1.5 font-mono text-ink-2 focus:ring-1 focus:ring-ink focus:border-ink outline-none transition-all">
            @foreach(range(now()->year - 2, now()->year) as $y)
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

    <div class="p-5 space-y-6 transition-opacity duration-500" wire:loading.class="opacity-60 pointer-events-none">

        {{-- KPI strip - Only Income & Expenses --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 border border-edge rounded overflow-hidden divide-x divide-edge shadow-sm">
            <x-kpi-card label="Income" :value="'₹' . number_format($summary['income'], 2)" color="green"
                        :sub="date('M Y', mktime(0, 0, 0, (int) $month, 1, (int) $year))">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
            <x-kpi-card label="Expenses" :value="'₹' . number_format($summary['expense'], 2)" color="red"
                        :sub="'Current Month Expenditure'">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"/>
                    </svg>
                </x-slot>
            </x-kpi-card>
        </div>

        {{-- Account Breakdown Strip --}}
        <div class="space-y-2">
            <p class="text-[10px] font-sans font-bold uppercase tracking-[0.2em] text-ink-3 px-1">Your Accounts</p>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($accounts as $acc)
                    <div class="bg-surface border border-edge rounded-lg p-3 shadow-sm hover:border-ink-3 transition-colors cursor-default">
                        <p class="text-[9px] font-sans font-bold uppercase text-ink-3 truncate mb-1">{{ $acc->name }}</p>
                        <p class="text-sm font-mono font-bold {{ $acc->balance >= 0 ? 'text-ink' : 'text-finance-red' }}">
                            ₹{{ number_format($acc->balance, 2) }}
                        </p>
                    </div>
                @endforeach
                <div class="bg-surface-2 border border-edge-2 rounded-lg p-3 flex flex-col justify-center">
                    <p class="text-[9px] font-sans font-bold uppercase text-ink-3 mb-1">Total Balance</p>
                    <p class="text-sm font-mono font-bold text-ink">
                        ₹{{ number_format($accounts->sum('balance'), 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Spending Trends Chart --}}
        <x-panel title="Daily Spending Trends — {{ date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }}" color="blue">
            <div class="p-4 h-[300px] font-sans">
                <x-chart type="line" :labels="$dailyTrends['labels']" :datasets="[
        [
            'label' => 'Expenses',
            'data' => $dailyTrends['data'],
            'borderColor' => '#991b1b',
            'backgroundColor' => 'rgba(153, 27, 27, 0.1)',
            'fill' => true,
            'tension' => 0.4,
            'pointRadius' => 0,
            'pointHoverRadius' => 4,
            'borderWidth' => 2,
        ]
    ]" :options="[
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'grid' => ['display' => true, 'color' => '#e2e0d8', 'drawBorder' => false],
                'ticks' => ['font' => ['family' => 'IBM Plex Mono', 'size' => 10], 'color' => '#9b9890']
            ],
            'x' => [
                'grid' => ['display' => false],
                'ticks' => ['font' => ['family' => 'IBM Plex Mono', 'size' => 10], 'color' => '#9b9890']
            ]
        ],
        'plugins' => [
            'tooltip' => [
                'backgroundColor' => '#1a1916',
                'titleFont' => ['family' => 'Bricolage Grotesque', 'size' => 12],
                'bodyFont' => ['family' => 'IBM Plex Mono', 'size' => 12],
                'padding' => 10,
                'displayColors' => false,
            ]
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
            'hoverOffset' => 10,
        ]
    ]" :options="[
        'cutout' => '70%',
        'plugins' => [
            'legend' => [
                'position' => 'right',
                'labels' => [
                    'boxWidth' => 8,
                    'usePointStyle' => true,
                    'font' => ['family' => 'IBM Plex Sans', 'size' => 11],
                    'color' => '#6b6860',
                    'padding' => 15
                ]
            ],
            'tooltip' => [
                'backgroundColor' => '#1a1916',
                'padding' => 10,
            ]
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
            'borderRadius' => 4,
        ],
        [
            'label' => 'Expense',
            'data' => $historicalChart['expense'],
            'backgroundColor' => '#991b1b',
            'borderRadius' => 4,
        ]
    ]" :options="[
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'grid' => ['display' => true, 'color' => '#e2e0d8', 'drawBorder' => false],
                'ticks' => ['font' => ['family' => 'IBM Plex Mono', 'size' => 10], 'color' => '#9b9890']
            ],
            'x' => [
                'grid' => ['display' => false],
                'ticks' => ['font' => ['family' => 'IBM Plex Mono', 'size' => 10], 'color' => '#9b9890']
            ]
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
                <div class="overflow-x-auto custom-scrollbar">
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
        <x-panel title="Category breakdown — {{ date('F Y', mktime(0, 0, 0, (int) $month, 1, (int) $year)) }}" color="blue">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-xs border-collapse min-w-[600px]">
                    <thead class="bg-surface-2">
                        <tr>
                            <th
                                class="px-3.5 py-1.5 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Category
                            </th>
                            <th
                                class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Spent
                            </th>
                            <th
                                class="px-3.5 py-1.5 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">
                                Total %
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalExp = collect($categorySpending)->sum('total');
                        @endphp
                        @foreach($categorySpending as $cs)
                            <tr class="border-b border-edge last:border-0 hover:bg-surface-2">
                                <td class="px-3.5 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $cs['color'] }}"></span>
                                        {{ $cs['category'] }}
                                    </div>
                                </td>
                                <td class="px-3.5 py-2 text-right font-mono text-finance-red">
                                    ₹{{ number_format($cs['total'], 0) }}</td>
                                <td class="px-3.5 py-2 text-right font-mono text-ink-2">
                                    {{ $totalExp > 0 ? round(($cs['total'] / $totalExp) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>
    </div>
</div>
