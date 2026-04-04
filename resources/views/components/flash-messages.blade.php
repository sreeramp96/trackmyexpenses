<div
    x-data="{ 
        messages: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.messages.push({ id, message, type });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    @flash.window="add($event.detail.message, $event.detail.type)"
    class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none"
>
    {{-- Handle Session Flashes --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.duration.500ms
             class="pointer-events-auto bg-surface border border-finance-green-border shadow-lg rounded-lg px-4 py-3 min-w-[300px] flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-finance-green shrink-0"></div>
            <p class="text-sm font-medium text-ink">{{ session('success') }}</p>
            <button @click="show = false" class="ml-auto text-ink-3 hover:text-ink text-lg leading-none">×</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.duration.500ms
             class="pointer-events-auto bg-surface border border-finance-red-border shadow-lg rounded-lg px-4 py-3 min-w-[300px] flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-finance-red shrink-0"></div>
            <p class="text-sm font-medium text-ink">{{ session('error') }}</p>
            <button @click="show = false" class="ml-auto text-ink-3 hover:text-ink text-lg leading-none">×</button>
        </div>
    @endif

    {{-- Handle JS dynamic flashes --}}
    <template x-for="msg in messages" :key="msg.id">
        <div x-show="true" x-transition.duration.500ms
             class="pointer-events-auto bg-surface border shadow-lg rounded-lg px-4 py-3 min-w-[300px] flex items-center gap-3"
             :class="msg.type === 'success' ? 'border-finance-green-border' : 'border-finance-red-border'">
            <div class="w-2 h-2 rounded-full shrink-0" :class="msg.type === 'success' ? 'bg-finance-green' : 'bg-finance-red'"></div>
            <p class="text-sm font-medium text-ink" x-text="msg.message"></p>
            <button @click="remove(msg.id)" class="ml-auto text-ink-3 hover:text-ink text-lg leading-none">×</button>
        </div>
    </template>
</div>
