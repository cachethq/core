<x-cachet::cachet :title="__('cachet::subscriber.status_page.unsubscribe.title')" page="unsubscribe">
    <x-cachet::header />

    <main data-component="unsubscribe" data-slot="main" class="container mx-auto flex max-w-md flex-col px-4 py-16 sm:px-6 lg:px-8">
        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_UNSUBSCRIBE_BEFORE) }}

        <div data-slot="content" class="flex flex-col gap-6 rounded-xl border border-zinc-900/10 bg-white/70 p-8 shadow-sm dark:border-white/15 dark:bg-zinc-900/50 sm:p-10">
            <div class="flex flex-col items-center gap-4 text-center">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-zinc-500/10 text-zinc-500 ring-1 ring-inset ring-zinc-500/20 dark:text-zinc-400">
                    <x-heroicon-o-bell-slash class="size-6" aria-hidden="true" />
                </span>
                <div class="flex flex-col gap-1.5">
                    <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                        {{ __('cachet::subscriber.status_page.unsubscribe.heading') }}
                    </h1>
                    <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">
                        {{ __('cachet::subscriber.status_page.unsubscribe.body', ['email' => $subscriber->email]) }}
                    </p>
                </div>
            </div>

            <form data-slot="form" method="POST" action="{{ request()->fullUrl() }}" class="flex flex-col gap-3">
                @csrf

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500">
                    {{ __('cachet::subscriber.status_page.unsubscribe.button') }}
                </button>

                <a data-slot="navigation" href="{{ route('cachet.status-page') }}" class="inline-flex w-full items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-zinc-600 ring-1 ring-inset ring-zinc-300 transition hover:bg-zinc-50 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800/50">
                    {{ __('cachet::subscriber.status_page.unsubscribe.cancel') }}
                </a>
            </form>
        </div>

        {{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_UNSUBSCRIBE_AFTER) }}
    </main>

    <x-cachet::footer />
</x-cachet::cachet>
