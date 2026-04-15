<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="stageForReview">
            {{ $this->form }}

            <div class="mt-6 flex items-center justify-between gap-3">
                @if($step === 2)
                    <x-filament::button
                        wire:click="$set('step', 1)"
                        color="gray"
                        variant="ghost"
                        icon="heroicon-m-arrow-left"
                    >
                        Change File
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        icon="heroicon-m-sparkles"
                    >
                        Analyze & Stage Transactions
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>
</x-filament-panels::page>
