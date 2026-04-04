<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-kpi-card label="To Collect" :value="'₹' . number_format($summary['total_to_collect'], 2)" color="green" />
            <x-kpi-card label="To Pay" :value="'₹' . number_format($summary['total_to_pay'], 2)" color="red" />
            <x-kpi-card label="Net Balance" :value="'₹' . number_format($summary['net_debt'], 2)" :color="$summary['net_debt'] >= 0 ? 'blue' : 'amber'" />
            <x-kpi-card label="Overdue" :value="$summary['overdue_count']" color="red" :sub="$summary['unsettled_count'] . ' unsettled debts'" />
        </div>

        {{-- Filters & Action --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <select wire:model.live="filterDirection" class="text-xs border-edge rounded bg-surface text-ink-2 focus:ring-0 focus:border-ink">
                    <option value="">All Directions</option>
                    <option value="lent">Lent (To Collect)</option>
                    <option value="borrowed">Borrowed (To Pay)</option>
                </select>
                <select wire:model.live="filterStatus" class="text-xs border-edge rounded bg-surface text-ink-2 focus:ring-0 focus:border-ink">
                    <option value="active">Active Only</option>
                    <option value="settled">Settled Only</option>
                    <option value="overdue">Overdue Only</option>
                    <option value="all">All Statuses</option>
                </select>
            </div>

            <button type="button" wire:click="openModal()" class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                    <path d="M6 1v10M1 6h10"/>
                </svg>
                Add Debt
            </button>
        </div>

        {{-- Debt List --}}
        <x-panel title="Debt Records">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse min-w-[800px]">
                    <thead class="bg-surface-2">
                        <tr>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Contact</th>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Type</th>
                            <th class="px-4 py-2 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Amount</th>
                            <th class="px-4 py-2 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Remaining</th>
                            <th class="px-4 py-2 text-left text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Due Date</th>
                            <th class="px-4 py-2 text-center text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Status</th>
                            <th class="px-4 py-2 text-right text-[10px] font-mono font-medium text-ink-3 uppercase tracking-wider border-b border-edge">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-edge">
                        @forelse($debts as $debt)
                            <tr wire:key="debt-{{ $debt->id }}" class="hover:bg-surface-2 transition-colors">
                                <td class="px-4 py-3 font-medium">{{ $debt->contact_name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono uppercase tracking-tighter {{ $debt->direction === 'lent' ? 'bg-finance-green-bg text-finance-green' : 'bg-finance-red-bg text-finance-red' }}">
                                        {{ $debt->direction }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-ink-3">₹{{ number_format($debt->amount, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold {{ $debt->direction === 'lent' ? 'text-finance-green' : 'text-finance-red' }}">
                                    ₹{{ number_format($debt->remaining_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 font-mono text-ink-2">
                                    {{ $debt->due_date ? $debt->due_date->format('M d, Y') : '—' }}
                                    @if($debt->isOverdue())
                                        <span class="block text-[8px] text-finance-red uppercase font-bold">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($debt->is_settled)
                                        <x-badge type="green">Settled</x-badge>
                                    @else
                                        <x-badge :type="$debt->isOverdue() ? 'red' : 'amber'">
                                            {{ $debt->isOverdue() ? 'Overdue' : 'Active' }}
                                        </x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 group-hover:block transition-opacity">
                                        {{-- We use absolute positioning context or just display: flex here --}}
                                    </div>
                                    {{-- For simplicity and standard UI, we'll keep actions visible or in a menu --}}
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$debt->is_settled)
                                            <button type="button" wire:click="recordPayment({{ $debt->id }})" class="text-[10px] text-finance-blue hover:underline font-bold uppercase tracking-tighter" title="Record Payment">Pay</button>
                                            <button type="button" wire:click="settle({{ $debt->id }})" wire:confirm="Mark this debt as settled without full payment?" class="text-[10px] text-ink-3 hover:text-ink font-bold uppercase tracking-tighter" title="Settle Manually">Settle</button>
                                        @endif
                                        <button type="button" wire:click="openModal({{ $debt->id }})" class="text-[10px] text-ink-3 hover:text-ink font-bold uppercase tracking-tighter">Edit</button>
                                        <button type="button" wire:click="delete({{ $debt->id }})" wire:confirm="Delete this debt record?" class="text-[10px] text-finance-red hover:text-red-700 font-bold uppercase tracking-tighter">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-ink-3 italic uppercase tracking-widest">No debt records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-panel>
    </div>

    {{-- Debt Modal --}}
    @if($showModal)
        <div wire:key="debt-modal-bg" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40" wire:click.self="$set('showModal', false)">
            <div class="bg-surface border border-edge-2 rounded w-full max-w-md shadow-xl" @click.stop>
                <div class="flex items-center justify-between px-4 py-3 border-b border-edge">
                    <h2 class="text-sm font-medium">{{ $editingId ? 'Edit' : 'New' }} debt record</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-ink-3 hover:text-ink text-lg leading-none px-1">
                        ×
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    {{-- Contact --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Contact Name</label>
                        <input wire:model="contact_name" type="text" placeholder="Who did you lend/borrow from?"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('contact_name')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    {{-- Type Selector --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1.5">Direction</label>
                        <div class="flex border border-edge rounded overflow-hidden">
                            <button type="button" wire:click="$set('direction','lent')"
                                    class="flex-1 py-1.5 text-xs font-medium transition-all border-r border-edge
                      {{ $direction === 'lent' ? 'bg-finance-green-bg text-finance-green' : 'bg-surface text-ink-2 hover:bg-surface-2' }}">
                                Lent (To Collect)
                            </button>
                            <button type="button" wire:click="$set('direction','borrowed')"
                                    class="flex-1 py-1.5 text-xs font-medium transition-all
                      {{ $direction === 'borrowed' ? 'bg-finance-red-bg text-finance-red' : 'bg-surface text-ink-2 hover:bg-surface-2' }}">
                                Borrowed (To Pay)
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Amount --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Original Amount</label>
                            <input wire:model.live="amount" type="number" step="0.01" placeholder="0.00"
                                   class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-mono font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                            @error('amount')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>

                        {{-- Remaining --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Remaining</label>
                            <input wire:model="remaining_amount" type="number" step="0.01" placeholder="0.00"
                                   class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-mono font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                            @error('remaining_amount')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Due Date (Optional)</label>
                        <input wire:model="due_date" type="date"
                               class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('due_date')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Note</label>
                        <textarea wire:model="note" placeholder="What was this for?" rows="2"
                                  class="w-full border border-edge-2 rounded px-3 py-2 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none resize-none"></textarea>
                        @error('note')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-4 py-3 border-t border-edge bg-surface-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="text-xs px-3 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="save"
                            class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                        {{ $editingId ? 'Update' : 'Record Debt' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
