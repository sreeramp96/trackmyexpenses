<div>
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-4 pt-4">
        <div class="flex justify-between items-center mb-4 px-1">
            <h1 class="text-lg font-medium text-ink">Manage Accounts</h1>
            <button type="button" wire:click="openModal()" class="flex items-center gap-1.5 text-[10px] bg-ink text-white px-2.5 py-1.5 rounded hover:bg-ink-2 transition-colors font-bold uppercase tracking-widest">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12" stroke-linecap="round">
                    <path d="M6 1v10M1 6h10"/>
                </svg>
                Add Account
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($accounts as $account)
                <div wire:key="acc-{{ $account->id }}" class="bg-surface border border-edge rounded shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <div class="px-3 py-2 border-b border-edge bg-surface-2 flex justify-between items-center">
                        <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-ink-3">{{ $account->type }}</span>
                        <div class="flex gap-2">
                             <span class="w-1.5 h-1.5 rounded-full {{ $account->balance >= 0 ? 'bg-finance-green' : 'bg-finance-red' }}"></span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-medium mb-0.5 truncate text-ink">{{ $account->name }}</h3>
                        <p class="text-2xl font-mono font-medium tracking-tight mb-3">
                            ₹{{ number_format($account->balance, 2) }}
                        </p>
                        <div class="flex justify-between items-center text-[9px] font-mono text-ink-3 uppercase tracking-tighter">
                            <span>Currency: {{ $account->currency }}</span>
                            <a href="#" class="text-finance-blue hover:underline">Details →</a>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 bg-surface-3 border-t border-edge flex justify-between">
                         <button type="button" wire:click="openModal({{ $account->id }})" class="text-[9px] text-ink-2 hover:text-ink font-bold uppercase tracking-widest">Edit</button>
                         <button type="button" wire:click="delete({{ $account->id }})" wire:confirm="Are you sure you want to delete this account?" class="text-[9px] text-finance-red hover:text-red-700 font-bold uppercase tracking-widest">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Account Modal --}}
    @if($showModal)
        <div wire:key="acc-modal-bg" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40" wire:click.self="$set('showModal', false)">
            <div class="bg-surface border border-edge-2 rounded w-full max-w-md shadow-xl" @click.stop>
                <div class="flex items-center justify-between px-4 py-3 border-b border-edge">
                    <h2 class="text-sm font-medium">{{ $editingId ? 'Edit' : 'New' }} account</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="text-ink-3 hover:text-ink text-lg leading-none px-1">
                        ×
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Account Name</label>
                        <input wire:model="name" type="text" placeholder="e.g. HDFC Savings"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('name')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Type --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Type</label>
                            <select wire:model="type"
                                    class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                                <option value="bank">Bank</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="wallet">Digital Wallet</option>
                                <option value="loan">Loan</option>
                            </select>
                            @error('type')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>

                        {{-- Currency --}}
                        <div>
                            <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Currency</label>
                            <input wire:model="currency" type="text" placeholder="INR"
                                   class="w-full border border-edge-2 rounded px-3 py-2 text-sm font-mono bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none uppercase">
                            @error('currency')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Initial Balance --}}
                    <div>
                        <label class="block text-[10px] font-mono font-medium text-ink-3 uppercase tracking-widest mb-1">Initial Balance</label>
                        <input wire:model="balance" type="number" step="0.01" placeholder="0.00"
                               class="w-full border border-edge-2 rounded px-3 py-2 text-lg font-mono font-medium bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none">
                        @error('balance')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-4 py-3 border-t border-edge bg-surface-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="text-xs px-3 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="save"
                            class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                        {{ $editingId ? 'Update' : 'Save Account' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
