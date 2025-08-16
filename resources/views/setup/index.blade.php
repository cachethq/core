<x-cachet::cachet :title="$title">
    <div class="flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div>
            <a href="{{ route('cachet.status-page') }}" class="transition hover:opacity-80">
                <x-cachet::logo class="hidden h-10 w-auto sm:block" />
                <x-cachet::logomark class="h-10 w-auto sm:hidden" />
            </a>
        </div>
    </div>

    <div class="flex-1">
        <div class="mx-auto w-full max-w-5xl overflow-x-auto py-5">
            <div class="flex max-w-none gap-8 px-4 sm:px-6 lg:px-8">
                <div class="flex-1 shrink-0 whitespace-nowrap border-t-4 border-primary-500 pt-2 text-sm">
                    <div class="font-semibold text-primary-500">Step 1</div>
                    <div class="mt-1">Environment Setup</div>
                </div>
                <div class="flex-1 shrink-0 whitespace-nowrap border-t-4 border-primary-500 pt-2 text-sm">
                    <div class="font-semibold text-primary-500">Step 2</div>
                    <div class="mt-1">Status Page Setup</div>
                </div>
                <div class="flex-1 shrink-0 whitespace-nowrap border-t-4 border-zinc-300 pt-2 text-sm dark:border-zinc-600">
                    <div class="font-semibold text-zinc-400 dark:text-zinc-500">Step 3</div>
                    <div class="mt-1">Administrator Account</div>
                </div>
                <div class="flex-1 shrink-0 whitespace-nowrap border-t-4 border-zinc-300 pt-2 text-sm dark:border-zinc-600">
                    <div class="font-semibold text-zinc-400 dark:text-zinc-500">Step 4</div>
                    <div class="mt-1">Complete Setup</div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-5xl px-4 py-5 sm:px-6 lg:px-8">
            <form action="#" method="POST" class="rounded-md border bg-white p-5 shadow-lg dark:border-zinc-700 dark:bg-white/5 sm:px-8 sm:pb-8 sm:pt-10">
                <div class="grid gap-x-5 gap-y-8 lg:grid-cols-6">
                    <div class="lg:col-span-3">
                        <label for="site-name" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Site Name</label>
                        <input id="site-name" type="text" class="mt-1 block w-full rounded-md border border-zinc-400 px-3 py-2.5 dark:border-zinc-600 dark:bg-white/5" />
                    </div>
                    <div class="lg:col-span-3">
                        <label for="site-domain" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Site Domain</label>
                        <input id="site-domain" type="text" class="mt-1 block w-full rounded-md border border-zinc-400 px-3 py-2.5 dark:border-zinc-600 dark:bg-white/5" />
                    </div>
                    <div class="lg:col-span-3">
                        <label for="timezone" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Timezone</label>
                        <select id="timezone" class="mt-1 block w-full rounded-md border border-zinc-400 px-3 py-2.5 dark:border-zinc-600 dark:bg-white/5"></select>
                    </div>
                    <div class="lg:col-span-3">
                        <label for="language" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Language</label>
                        <select id="language" class="mt-1 block w-full rounded-md border border-zinc-400 px-3 py-2.5 dark:border-zinc-600 dark:bg-white/5"></select>
                    </div>
                    <div class="lg:col-span-3">
                        <label for="show-support" class="flex items-start gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <input id="show-support" type="checkbox" class="block h-5 w-5 rounded-sm border border-zinc-400 text-primary-500 dark:border-zinc-600 dark:bg-white/5" />
                            <span>Show support for Cachet</span>
                        </label>
                    </div>
                </div>
                <div class="mt-5 flex items-center justify-end gap-5">
                    <button class="inline-flex items-center gap-2 rounded-lg border border-zinc-800 bg-white px-3 py-2 font-semibold text-zinc-800 transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-white/5 dark:text-zinc-300 dark:hover:bg-zinc-900/50">
                        @svg('cachet-chevron-right', 'rotate-180 size-4')
                        <span>Back</span>
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-primary-500 px-3 py-2 font-semibold text-zinc-800 transition hover:bg-primary-600">
                        <span>Next</span>
                        @svg('cachet-chevron-right', 'size-4')
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
