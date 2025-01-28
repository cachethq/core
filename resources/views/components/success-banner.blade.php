@session('success')
<div x-cloak x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)">
    <div
        x-show="show"
        class="z-10 fixed bottom-5 right-5 flex gap-2 items-center justify-between rounded bg-accent-content dark:bg-accent-content p-4 text-sm text-accent-foreground transform-gpu transition-transform duration-400 ease"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"

    >
        <x-heroicon-o-check-circle class="size-5" />

        <span>
            {{ session('success') }}
        </span>

        <button @click="show = false">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
@endsession
