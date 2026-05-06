{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_NAVIGATION_BEFORE) }}
<header class="border-b border-zinc-900/10 bg-white/70 backdrop-blur dark:border-white/15 dark:bg-zinc-900/50">
    <div class="container mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('cachet.status-page') }}" class="inline-flex items-center transition hover:opacity-80">
            @if($appBanner)
                <img src="{{ Storage::url($appBanner) }}" alt="{{ $siteName }}" class="h-8 w-auto" />
            @else
                <x-cachet::logo class="hidden h-8 w-auto sm:block" />
                <x-cachet::logomark class="h-8 w-auto sm:hidden" />
            @endif
        </a>

        @if ($dashboardLoginLink)
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ Cachet\Cachet::dashboardPath() }}" class="inline-flex items-center rounded-md bg-accent px-3 py-1.5 text-sm font-semibold text-accent-foreground shadow-sm ring-1 ring-accent/30 transition hover:opacity-90">
                    {{ __('filament-panels::pages/dashboard.title') }}
                </a>
                @auth
                    <form action="{{ \Cachet\Cachet::dashboardPath() }}/logout" method="POST">
                        @csrf
                        <button class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                            {{ __('filament-panels::layout.actions.logout.label') }}
                        </button>
                    </form>
                @endauth
            </div>
        @endif
    </div>
</header>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_NAVIGATION_AFTER) }}
