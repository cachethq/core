<x-cachet::cachet :title="__('cachet::subscriber.status_page.subscribe.title')">
    <x-cachet::header />

    <div class="container mx-auto flex max-w-md flex-col px-4 py-16 sm:px-6 lg:px-8">
        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SUBSCRIBE_BEFORE) }}

        <div class="flex flex-col gap-6 rounded-xl border border-zinc-900/10 bg-white/70 p-8 shadow-sm dark:border-white/15 dark:bg-zinc-900/50 sm:p-10">
            @if (session('cachet_subscriber_status') === 'subscribed')
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </span>
                    <div class="flex flex-col gap-1.5">
                        <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ __('cachet::subscriber.status_page.subscribe.subscribed_heading') }}
                        </h1>
                        <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                            {{ __('cachet::subscriber.status_page.subscribe.subscribed_body') }}
                        </p>
                    </div>
                </div>
            @elseif (session('cachet_subscriber_status') === 'unsubscribed')
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-zinc-500/10 text-zinc-500 ring-1 ring-inset ring-zinc-500/20 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                        </svg>
                    </span>
                    <div class="flex flex-col gap-1.5">
                        <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ __('cachet::subscriber.status_page.subscribe.unsubscribed_heading') }}
                        </h1>
                        <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                            {{ __('cachet::subscriber.status_page.subscribe.unsubscribed_body') }}
                        </p>
                    </div>
                </div>
            @elseif (session('cachet_subscriber_status') === 'verified')
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </span>
                    <div class="flex flex-col gap-1.5">
                        <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ __('cachet::subscriber.status_page.subscribe.verified_heading') }}
                        </h1>
                        <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                            {{ __('cachet::subscriber.status_page.subscribe.verified_body') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </span>
                    <div class="flex flex-col gap-1.5">
                        <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ __('cachet::subscriber.status_page.subscribe.heading') }}
                        </h1>
                        <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                            {{ __('cachet::subscriber.status_page.subscribe.description') }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cachet.subscribers.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label for="subscriber-email" class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            {{ __('cachet::subscriber.status_page.email_label') }}
                        </label>
                        <input
                            id="subscriber-email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            placeholder="{{ __('cachet::subscriber.status_page.email_placeholder') }}"
                            class="w-full rounded-md border-0 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-accent dark:bg-zinc-800 dark:text-zinc-100 dark:ring-zinc-700"
                        />
                        @error('email')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-sm ring-1 ring-accent/30 transition hover:opacity-90">
                        {{ __('cachet::subscriber.status_page.subscribe_button') }}
                    </button>
                </form>

                <p class="text-center text-xs leading-relaxed text-zinc-400 dark:text-zinc-500">
                    {{ __('cachet::subscriber.status_page.subscribe.consent') }}
                </p>
            @endif
        </div>

        <a href="{{ route('cachet.status-page') }}" class="mt-6 inline-flex items-center justify-center gap-1.5 text-sm font-medium text-zinc-500 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('cachet::subscriber.status_page.subscribe.back') }}
        </a>

        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SUBSCRIBE_AFTER) }}
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
