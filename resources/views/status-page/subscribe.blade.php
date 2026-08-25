<x-cachet::cachet :title="__('cachet::subscriber.status_page.subscribe.title')" page="subscribe">
    <x-cachet::header />

    <main data-component="subscribe" data-slot="main" class="container mx-auto flex max-w-md flex-col px-4 py-16 sm:px-6 lg:px-8">
        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SUBSCRIBE_BEFORE) }}

        <div data-slot="content" class="flex flex-col gap-6 rounded-xl border border-zinc-900/10 bg-white/70 p-8 shadow-sm dark:border-white/15 dark:bg-zinc-900/50 sm:p-10">
            @if (session('cachet_subscriber_status') === 'subscribed')
                <div data-slot="state" data-state="subscribed" class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <x-heroicon-o-paper-airplane class="size-6" aria-hidden="true" />
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
                <div data-slot="state" data-state="unsubscribed" class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-zinc-500/10 text-zinc-500 ring-1 ring-inset ring-zinc-500/20 dark:text-zinc-400">
                        <x-heroicon-o-bell-slash class="size-6" aria-hidden="true" />
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
                <div data-slot="state" data-state="verified" class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <x-heroicon-o-check class="size-6" aria-hidden="true" />
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
                <div data-slot="state" data-state="form" class="flex flex-col items-center gap-4 text-center">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-accent/10 text-accent-content ring-1 ring-inset ring-accent/20">
                        <x-heroicon-o-envelope class="size-6" aria-hidden="true" />
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

                <form data-slot="form" method="POST" action="{{ route('cachet.subscribers.store') }}" class="flex flex-col gap-4">
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

        <a data-slot="navigation" href="{{ route('cachet.status-page') }}" class="mt-6 inline-flex items-center justify-center gap-1.5 text-sm font-medium text-zinc-500 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
            <x-heroicon-m-arrow-left class="size-4" aria-hidden="true" />
            {{ __('cachet::subscriber.status_page.subscribe.back') }}
        </a>

        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_SUBSCRIBE_AFTER) }}
    </main>

    <x-cachet::footer />
</x-cachet::cachet>
