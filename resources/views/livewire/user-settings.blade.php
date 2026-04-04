<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Preferences Section --}}
            <x-panel title="General Preferences">
                <div class="p-6 space-y-4">
                    {{-- Currency --}}
                    <div>
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Default Currency</label>
                        <select wire:model="currency" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                            @foreach($currencies as $code => $label)
                                <option value="{{ $code }}">{{ $label }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-ink-3 italic mt-1 uppercase tracking-tight">This affects how all money values are displayed in the app.</p>
                        @error('currency')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    {{-- Timezone --}}
                    <div>
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-widest text-ink-3 mb-1.5">Timezone</label>
                        <select wire:model="timezone" class="w-full border border-edge-2 rounded px-2.5 py-1.5 text-xs bg-surface text-ink focus:border-ink focus:ring-0 focus:outline-none pr-8">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}">{{ $tz }}</option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-ink-3 italic mt-1 uppercase tracking-tight">Your current local time: {{ now()->setTimezone($timezone)->format('H:i, M d') }}</p>
                        @error('timezone')<p class="text-[10px] text-finance-red mt-1 uppercase font-mono tracking-tighter">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-2">
                        <button type="button" wire:click="save" class="text-xs px-4 py-1.5 bg-ink text-white rounded hover:bg-ink-2 transition-colors font-medium">
                            Save Preferences
                        </button>
                    </div>
                </div>
            </x-panel>

            {{-- Profile / Account Section Link --}}
            <x-panel title="Account Security">
                <div class="p-6 space-y-4">
                    <p class="text-sm text-ink-2 mb-4">Manage your name, email address, and account password.</p>
                    
                    <a href="{{ route('profile.edit') }}" class="inline-block text-xs px-4 py-1.5 border border-edge rounded bg-surface text-ink-2 hover:bg-surface-3 transition-colors font-medium">
                        Update Account Security →
                    </a>

                    <div class="mt-8 pt-8 border-t border-edge">
                        <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-finance-red mb-2">Danger Zone</p>
                        <p class="text-[10px] text-ink-3 mb-4">Deleting your account will permanently remove all your transactions, budgets, and historical data. This cannot be undone.</p>
                        
                        <a href="{{ route('profile.edit') }}#delete-account" class="text-[10px] font-bold text-finance-red hover:underline uppercase tracking-widest">
                            Delete Account permanently
                        </a>
                    </div>
                </div>
            </x-panel>
        </div>
    </div>
</div>
