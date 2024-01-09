<div class="flex items-center justify-between border-b border-zinc-200 px-8 py-6 dark:border-zinc-700">
    <div>
        <a href="{{ route('cachet.status-page') }}" class="transition hover:opacity-80">
            <x-cachet::logo class="hidden h-8 w-auto sm:block" />
            <x-cachet::logomark class="h-8 w-auto sm:hidden" />
        </a>
    </div>

    <div class="flex items-center gap-5">
        <a href="{{ route('cachet.dashboard.index') }}" class="font-medium text-zinc-500 transition hover:text-zinc-700 dark:text-white dark:hover:text-zinc-300">Dashboard</a>
        {{-- Condition Button. --}}
        <a href="{{ route('cachet.dashboard.index') }}" class="font-medium text-zinc-500 transition hover:text-zinc-700 dark:text-white dark:hover:text-zinc-300">Logout</a>
        {{-- Conditional Button. --}}
        <a href="{{ route('cachet.dashboard.index') }}" class="rounded bg-primary-500 px-2 py-1 text-sm font-semibold text-zinc-800 transition hover:bg-primary-600">Subscribers</a>
    </div>
</div>
