<x-cachet::cachet :title="__('cachet::subscriber.status_page.unsubscribe.title')">
    <x-cachet::header />

    <div class="container mx-auto flex max-w-md flex-col px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 rounded-xl border border-zinc-900/10 bg-white/70 p-8 shadow-sm dark:border-white/15 dark:bg-zinc-900/50 sm:p-10">
            <div class="flex flex-col items-center gap-4 text-center">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-zinc-500/10 text-zinc-500 ring-1 ring-inset ring-zinc-500/20 dark:text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                    </svg>
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

            <form method="POST" action="{{ request()->fullUrl() }}" class="flex flex-col gap-3">
                @csrf

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500">
                    {{ __('cachet::subscriber.status_page.unsubscribe.button') }}
                </button>

                <a href="{{ route('cachet.status-page') }}" class="inline-flex w-full items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-zinc-600 ring-1 ring-inset ring-zinc-300 transition hover:bg-zinc-50 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800/50">
                    {{ __('cachet::subscriber.status_page.unsubscribe.cancel') }}
                </a>
            </form>
        </div>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
