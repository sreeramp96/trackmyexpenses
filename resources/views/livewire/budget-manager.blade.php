<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div class="flex items-center gap-3 bg-surface border border-edge rounded px-2 py-1 shadow-sm">
                <button wire:click="previousMonth" class="p-1.5 hover:bg-surface-2 rounded transition-colors text-ink-3 hover:text-ink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <span class="text-sm font-mono font-bold uppercase tracking-widest min-w-[120px] text-center">
                    {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
                </span>
                <button wire:click="nextMonth" class="p-1.5 hover:bg-surface-2 rounded transition-colors text-ink-3 hover:text-ink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>

            <button type="button" wire:click="openModal()" class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                    <path d="M6 1v10M1 6h10"/>
                </svg>
                Set Budget
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($budgets as $b)
                <div wire:key="budget-{{ $b['id'] }}" class="bg-surface border border-edge rounded overflow-hidden shadow-sm hover:shadow-md transition-shadow group relative">
                    <div class="p-4 border-b border-edge bg-surface-2 flex justify-between items-center">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3">Budget: {{ $b['category'] }}</span>
                        <x-badge :type="$b['is_over_budget'] ? 'red' : ($b['percent_used'] > 85 ? 'amber' : 'green')">
                            {{ $b['percent_used'] }}%
                        </x-badge>
                    </div>
                    <div class="p-5">
                         <div class="flex justify-between items-end mb-4">
                            <div>
                                <p class="text-[10px] font-mono font-bold uppercase text-ink-3 opacity-60">Spent</p>
                                <p class="text-xl font-mono font-medium {{ $b['is_over_budget'] ? 'text-finance-red' : 'text-finance-green' }}">₹{{ number_format($b['spent_amount'], 0) }}</p>
                            </div>
                             <div class="text-right">
                                <p class="text-[10px] font-mono font-bold uppercase text-ink-3 opacity-60">Limit</p>
                                <p class="text-xl font-mono font-medium text-ink-2">₹{{ number_format($b['budgeted_amount'], 0) }}</p>
                            </div>
                        </div>

                        <div class="h-2 bg-surface-3 rounded-full overflow-hidden mb-4">
                            <div class="h-full transition-all duration-500 rounded-full"
                                 style="width: {{ min(100,$b['percent_used']) }}%; background: {{ $b['is_over_budget'] ? '#991b1b' : ($b['percent_used'] > 85 ? '#92400e' : '#166534') }}">
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-mono text-ink-3 uppercase">
                            <span>Remaining: ₹{{ number_format($b['remaining_amount'], 0) }}</span>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="openModal({{ $b['id'] }})" class="text-finance-blue hover:underline font-bold">Edit</button>
                                <button type="button" wire:click="delete({{ $b['id'] }})" wire:confirm="Are you sure you want to delete this budget?" class="text-finance-red hover:underline font-bold">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-surface border border-dashed border-edge-2 rounded-lg">
                    <div class="w-12 h-12 bg-surface-2 rounded-full flex items-center justify-center mx-auto mb-4">
                         <svg class="w-6 h-6 text-ink-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-sm font-medium text-ink-2">No budgets set for this month.</p>
                    <p class="text-[10px] text-ink-3 uppercase tracking-widest mt-1">Plan your expenses to save more.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Budget Modal --}}
    @if($showModal)
        <div wire:key="budget-modal-bg" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40" wire:click.self="$set('showModal', false)">
            <div class="bg-surface border border-edge-2 rounded w-full max-w-md shadow-xl" @click.stop>
                <div class="flex items-center justify-between px-4 py-3 border-b border-edge">
                    <h2 class="text-sm font-medium">{{ $editingId ? 'Edit' : 'New' }} budget</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-ink-3 hover:text-ink text-lg leading-none px-1">
                        ×
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    {{-- Category --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Category</label>
                        <select wire:model="categoryId"
                                class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                            <option value="">All Categories (Global)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Monthly Limit (₹)</label>
                        <input wire:model="amount" type="number" step="0.01" placeholder="0.00"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-lg font-mono font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('amount')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Period --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Period</label>
                            <select wire:model="period"
                                    class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            @error('period')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>

                        {{-- Start Date --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Start Date</label>
                            <input wire:model="startDate" type="date"
                                   class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                            @error('startDate')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">End Date (Optional)</label>
                        <input wire:model="endDate" type="date"
                               class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('endDate')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        <p class="text-[9px] text-ink-3 italic mt-1 uppercase tracking-tight">Leave blank for a recurring perpetual budget.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-4 py-3 border-t border-edge bg-surface-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="text-xs px-3 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="save"
                            class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                        {{ $editingId ? 'Update' : 'Set Budget' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
