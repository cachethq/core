{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_NAVIGATION_BEFORE) }}
<div class="flex items-center justify-between border-b border-zinc-200 px-4 sm:px-6 lg:px-8 py-4 dark:border-zinc-700">
    <div>
        <a href="{{ route('cachet.status-page') }}" class="flex items-center gap-2 transition hover:opacity-80">
            @if($appBanner)
            <img src="{{ Storage::url($appBanner) }}" alt="{{ $siteName }}" class="h-8 w-auto" />
            @else
            <span class="text-xl font-bold text-zinc-800 dark:text-white">{{ $siteName }}</span>
            @endif
        </a>
    </div>

    <div class="flex items-center gap-2.5 sm:gap-5">
        {{-- Subscribe Button --}}
        <x-cachet::subscribe-button />

        @if ($dashboardLoginLink)
        <a href="{{ Cachet\Cachet::dashboardPath() }}" class="rounded-sm bg-accent px-3 py-2 text-sm font-semibold text-accent-foreground">
            {{ __('filament-panels::pages/dashboard.title') }}
        </a>
        @auth
        {{-- TODO: This form sucks... --}}
        <form action="{{ \Cachet\Cachet::dashboardPath() }}/logout" method="POST">
            @csrf
            <button class="text-sm font-medium text-zinc-800 transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 sm:text-base">
                {{ __('filament-panels::layout.actions.logout.label') }}
            </button>
        </form>
        @endauth
        @endif
    </div>
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_NAVIGATION_AFTER) }}
