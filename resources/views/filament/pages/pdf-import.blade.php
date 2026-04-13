<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap');

        .import-container {
            font-family: 'Bricolage Grotesque', sans-serif;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Sleek Technical Stepper */
        .stepper-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
            padding: 0 1rem;
        }

        .stepper-header::before {
            content: '';
            position: absolute;
            top: 1.25rem;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 0;
        }

        .step-pill {
            position: relative;
            z-index: 1;
            background: #f5f4f0;
            padding: 0 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .step-dot {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 800;
            border: 1px solid #d1d5db;
            background: #fff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-pill.active .step-dot {
            border-color: #1a1916;
            background: #1a1916;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(26, 25, 22, 0.1);
            transform: scale(1.1);
        }

        .step-pill.done .step-dot {
            border-color: #166534;
            background: #166534;
            color: #fff;
        }

        .step-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
        }

        /* Staging Area Card */
        .main-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
        }

        .card-header {
            margin-bottom: 2.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #1a1916;
        }

        /* Interactive Staging Table */
        .staging-table-container {
            margin-top: 2rem;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            overflow-x: auto;
            background: #fff;
        }

        .staging-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .staging-table th {
            background: #f9fafb;
            padding: 1rem;
            text-align: left;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .staging-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .input-minimal {
            width: 100%;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 0.5rem;
            padding: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            outline: none;
        }

        .input-minimal:focus {
            background: #fff;
            border-color: #1a1916;
        }

        /* Type Toggles */
        .type-pill-group {
            display: inline-flex;
            background: #f3f4f6;
            padding: 0.25rem;
            border-radius: 0.75rem;
        }

        .type-pill {
            padding: 0.35rem 1rem;
            font-size: 0.65rem;
            font-weight: 800;
            border-radius: 0.6rem;
            cursor: pointer;
        }

        .type-pill.active-in { background: #166534; color: #fff; }
        .type-pill.active-out { background: #991b1b; color: #fff; }
        .type-pill.inactive { color: #6b7280; }

        .bank-badge {
            background: #3b82f6;
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>

    <div class="import-container">
        {{-- Stepper --}}
        <div class="stepper-header">
            <div class="step-pill {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' }}">
                <div class="step-dot">{{ $step > 1 ? '✓' : '1' }}</div>
                <div class="step-label">Upload</div>
            </div>
            <div class="step-pill {{ $step === 2 ? 'active' : '' }}">
                <div class="step-dot">2</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <form wire:submit="import">
            @if($step === 1)
                <div class="main-card animate-in fade-in zoom-in-95 duration-500">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">HDFC PDF Data</h2>
                            <p class="card-subtitle">Upload your bank statement for automated transaction extraction.</p>
                        </div>
                        <div class="bank-badge">
                            <x-heroicon-m-shield-check class="w-4 h-4" />
                            SECURE PARSING
                        </div>
                    </div>

                    <div class="space-y-8 pb-8">
                        {{ $this->form }}
                    </div>
                </div>
            @endif

            @if($step === 2)
                <div class="animate-in fade-in slide-in-from-bottom-8 duration-700 space-y-8">

                    <div class="main-card border-dashed bg-[#f9fafb] p-6">
                        <h3 class="text-xs font-black uppercase tracking-widest text-[#6b7280] mb-4">Target Account</h3>
                        <div class="pb-16 relative z-20">
                            {{ $this->form }}
                        </div>
                    </div>

                    <div class="flex items-end justify-between">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight uppercase">Extractor Review</h2>
                            <p class="text-sm text-[#6b7280]">Verify and tune the data extracted from your HDFC statement.</p>
                        </div>
                        <div class="bg-[#1a1916] text-white px-5 py-2 rounded-xl text-sm font-black shadow-xl flex items-center gap-3">
                            <span class="w-2 h-2 bg-[#166534] rounded-full animate-pulse"></span>
                            {{ count($previewData) }} EXTRACTED
                        </div>
                    </div>

                    <div class="staging-table-container shadow-2xl">
                        <table class="staging-table">
                            <thead>
                                <tr>
                                    <th class="w-40">Date</th>
                                    <th>Narration</th>
                                    <th class="w-48">Category</th>
                                    <th class="w-32 text-center">Direction</th>
                                    <th class="text-right w-36">Amount</th>
                                    <th class="w-16 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $index => $tx)
                                    <tr wire:key="pdf-tx-{{ $index }}">
                                        <td>
                                            <input type="date" wire:model="previewData.{{ $index }}.transaction_date" class="input-minimal mono font-semibold">
                                        </td>
                                        <td>
                                            <input type="text" wire:model="previewData.{{ $index }}.note" class="input-minimal font-bold">
                                        </td>
                                        <td>
                                            <select wire:model="previewData.{{ $index }}.category_id" class="input-minimal text-xs appearance-none cursor-pointer">
                                                <option value="">Uncategorized</option>
                                                @foreach($categories as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <div class="type-pill-group">
                                                <div wire:click="$set('previewData.{{ $index }}.type', 'income')" class="type-pill {{ $tx['type'] === 'income' ? 'active-in' : 'inactive' }}">IN</div>
                                                <div wire:click="$set('previewData.{{ $index }}.type', 'expense')" class="type-pill {{ $tx['type'] === 'expense' ? 'active-out' : 'inactive' }}">OUT</div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end font-mono font-black {{ $tx['type'] === 'income' ? 'text-[#166534]' : 'text-[#991b1b]' }}">
                                                <span class="mr-1">{{ $tx['type'] === 'expense' ? '-' : '+' }}</span>
                                                <input type="number" step="0.01" wire:model="previewData.{{ $index }}.amount" class="w-24 bg-transparent border-none p-1 text-right focus:outline-none">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-gray-400 hover:text-[#991b1b] transition-all transform hover:rotate-90 hover:scale-125" title="Remove row">
                                                <x-heroicon-m-trash class="w-5 h-5 mx-auto" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center bg-white p-8 border border-[#e5e7eb] rounded-2xl shadow-lg">
                        <x-filament::button wire:click="$set('step', 1)" color="gray" variant="ghost" icon="heroicon-m-arrow-left">
                            Upload Different File
                        </x-filament::button>

                        <x-filament::button type="submit" size="xl" color="success" icon="heroicon-m-check-badge" class="px-20 shadow-2xl font-black italic">
                            COMMIT TO LEDGER
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>
