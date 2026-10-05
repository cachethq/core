@if (! $themeMode->isForced())
    <div
        x-data
        x-cloak
        data-theme-toggle
        role="radiogroup"
        aria-label="{{ __('cachet::cachet.theme.label') }}"
        class="flex items-center gap-0.5 rounded-full p-0.5 ring-1 ring-inset ring-zinc-900/10 dark:ring-white/15"
    >
        <button
            type="button"
            role="radio"
            :aria-checked="$store.theme.mode === 'light'"
            @click="$store.theme.select('light')"
            :class="$store.theme.mode === 'light' ? 'bg-zinc-900/10 text-zinc-900 dark:bg-white/15 dark:text-white' : 'text-zinc-400 hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-300'"
            class="rounded-full p-1 transition"
            title="{{ __('cachet::cachet.theme.light') }}"
        >
            <x-heroicon-m-sun class="size-4" />
            <span class="sr-only">{{ __('cachet::cachet.theme.light') }}</span>
        </button>
        <button
            type="button"
            role="radio"
            :aria-checked="$store.theme.mode === 'auto'"
            @click="$store.theme.select('auto')"
            :class="$store.theme.mode === 'auto' ? 'bg-zinc-900/10 text-zinc-900 dark:bg-white/15 dark:text-white' : 'text-zinc-400 hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-300'"
            class="rounded-full p-1 transition"
            title="{{ __('cachet::cachet.theme.auto') }}"
        >
            <x-heroicon-m-computer-desktop class="size-4" />
            <span class="sr-only">{{ __('cachet::cachet.theme.auto') }}</span>
        </button>
        <button
            type="button"
            role="radio"
            :aria-checked="$store.theme.mode === 'dark'"
            @click="$store.theme.select('dark')"
            :class="$store.theme.mode === 'dark' ? 'bg-zinc-900/10 text-zinc-900 dark:bg-white/15 dark:text-white' : 'text-zinc-400 hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-300'"
            class="rounded-full p-1 transition"
            title="{{ __('cachet::cachet.theme.dark') }}"
        >
            <x-heroicon-m-moon class="size-4" />
            <span class="sr-only">{{ __('cachet::cachet.theme.dark') }}</span>
        </button>
    </div>
@endif
