<div x-data="{
    open: false,
    email: '',
    loading: false,
    message: '',
    success: false,

    async subscribe() {
        if (!this.email || this.loading) return;

        this.loading = true;
        this.message = '';

        try {
            const response = await fetch('{{ route("cachet.subscriber.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ email: this.email })
            });

            const data = await response.json();

            this.message = data.message;
            this.success = data.success;

            if (data.success && !data.already_subscribed) {
                this.email = '';
                setTimeout(() => {
                    this.open = false;
                    this.message = '';
                }, 3000);
            }
        } catch (error) {
            this.message = 'An error occurred. Please try again.';
            this.success = false;
        } finally {
            this.loading = false;
        }
    }
}" class="relative">
    {{-- Subscribe button --}}
    <button
        @click="open = !open"
        class="inline-flex items-center gap-1.5 rounded-md border border-zinc-300 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700"
    >
        <x-heroicon-o-bell class="w-4 h-4" />
        <span class="hidden sm:inline">Subscribe</span>
    </button>

    {{-- Dropdown form --}}
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 top-full mt-2 w-80 rounded-lg border border-zinc-200 bg-white p-4 shadow-lg dark:border-zinc-700 dark:bg-zinc-800 z-50"
        x-cloak
    >
        <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">
            Get notified about service updates and incidents.
        </p>

        <form @submit.prevent="subscribe" class="space-y-3">
            <input
                type="email"
                x-model="email"
                placeholder="Enter your email"
                required
                :disabled="loading"
                class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent disabled:opacity-50 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-100 dark:placeholder-zinc-500"
            />
            <button
                type="submit"
                :disabled="loading || !email"
                class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-accent px-4 py-2 text-sm font-medium text-accent-foreground transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Subscribing...' : 'Subscribe'"></span>
            </button>
        </form>

        {{-- Status message --}}
        <div
            x-show="message"
            x-transition
            :class="success ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
            class="mt-3 text-sm text-center"
            x-text="message"
        ></div>
    </div>
</div>
