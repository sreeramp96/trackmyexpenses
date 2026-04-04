<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        
        {{-- Filter Bar --}}
        <div class="bg-surface border border-edge rounded-lg p-4 shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">Search</label>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by note or reference..."
                           class="w-full border border-edge-2 rounded px-3 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">Type</label>
                    <select wire:model.live="type" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        <option value="">All Types</option>
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">Account</label>
                    <select wire:model.live="accountId" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">Category</label>
                    <select wire:model.live="categoryId" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">From Date</label>
                    <input wire:model.live="startDate" type="date" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1">To Date</label>
                    <input wire:model.live="endDate" type="date" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                </div>
                <div class="flex items-end">
                    <button wire:click="resetFilters" class="w-full text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 hover:text-finance-red transition-colors py-2 border border-dashed border-edge-2 rounded">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <x-panel title="Transaction History">
            <x-slot name="action">
                <button type="button" onclick="Livewire.dispatch('open-tx-modal')" class="text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 hover:text-ink transition-colors">
                    + New
                </button>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse min-w-[900px]">
                    <thead class="bg-surface-2">
                        <tr>
                            <th wire:click="sort('transaction_date')" class="px-4 py-2 text-left text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge cursor-pointer hover:bg-surface-3">
                                Date {!! $sortBy === 'transaction_date' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge">Description</th>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge">Category</th>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge">Account</th>
                            <th wire:click="sort('amount')" class="px-4 py-2 text-right text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge cursor-pointer hover:bg-surface-3">
                                Amount {!! $sortBy === 'amount' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="px-4 py-2 text-center text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge">Status</th>
                            <th class="px-4 py-2 text-right text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 border-b border-edge">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-edge">
                        @forelse($transactions as $tx)
                            <tr wire:key="tx-{{ $tx->id }}" class="hover:bg-surface-2 transition-colors group">
                                <td class="px-4 py-3 font-mono text-ink-2">{{ $tx->transaction_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-ink">{{ $tx->note ?? '—' }}</p>
                                    @if($tx->reference_number)
                                        <p class="text-[9px] font-mono text-ink-3 uppercase tracking-tighter">Ref: {{ $tx->reference_number }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($tx->category)
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $tx->category->color }}"></span>
                                            {{ $tx->category->name }}
                                        </span>
                                    @else
                                        <span class="text-ink-3 italic">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ink-2">
                                    {{ $tx->account->name }}
                                    @if($tx->type === 'transfer')
                                        <span class="text-ink-3 mx-1">→</span> {{ $tx->toAccount->name }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-medium {{ $tx->type === 'income' ? 'text-finance-green' : ($tx->type === 'transfer' ? 'text-finance-blue' : 'text-finance-red') }}">
                                    {{ $tx->type === 'expense' ? '−' : ($tx->type === 'income' ? '+' : '') }}₹{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tx->is_reconciled)
                                        <x-badge type="green">Reconciled</x-badge>
                                    @else
                                        <x-badge type="default">Pending</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="Livewire.dispatch('open-tx-modal', {id: {{ $tx->id }}})" class="text-[10px] text-ink-3 hover:text-ink font-bold uppercase tracking-tighter">Edit</button>
                                        <button type="button" wire:click="deleteTransaction({{ $tx->id }})" wire:confirm="Are you sure you want to delete this transaction?" class="text-[10px] text-finance-red hover:text-red-700 font-bold uppercase tracking-tighter">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-ink-3 italic uppercase tracking-widest bg-surface-2/30">
                                    No transactions found matching your criteria
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-edge bg-surface-2">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-panel>
    </div>
</div>
