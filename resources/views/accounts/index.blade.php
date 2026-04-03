<x-app-layout>
    <x-slot name="heading">Accounts</x-slot>
    <x-slot name="actions">
        <button class="flex items-center gap-1.5 text-xs bg-ink text-white px-3 py-1.5 rounded hover:bg-ink-2 transition-colors font-medium">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"
                 stroke-linecap="round">
                <path d="M6 1v10M1 6h10"/>
            </svg>
            Add Account
        </button>
    </x-slot>

    <div class="p-5 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($accounts as $account)
                <div class="bg-surface border border-edge rounded overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-4 border-b border-edge bg-surface-2 flex justify-between items-center">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3">{{ $account->type }}</span>
                        <div class="flex gap-2">
                             <span class="w-2 h-2 rounded-full {{ $account->balance >= 0 ? 'bg-finance-green' : 'bg-finance-red' }}"></span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-medium mb-1">{{ $account->name }}</h3>
                        <p class="text-3xl font-mono font-medium tracking-tight mb-4">
                            ₹{{ number_format($account->balance, 2) }}
                        </p>
                        <div class="flex justify-between items-center text-[10px] font-mono text-ink-3 uppercase">
                            <span>Currency: {{ $account->currency }}</span>
                            <a href="#" class="text-finance-blue hover:underline">Details →</a>
                        </div>
                    </div>
                    <div class="px-4 py-2 bg-surface-3 border-t border-edge flex justify-between">
                         <button class="text-[10px] text-ink-2 hover:text-ink font-medium uppercase">Edit</button>
                         <button class="text-[10px] text-finance-red hover:text-red-700 font-medium uppercase">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
