<div class="flex items-center justify-between border-b border-zinc-200 px-8 py-4 dark:border-zinc-700">
    <div>
        <a href="{{ route('cachet.status-page') }}" class="transition hover:opacity-80">
            @if($appBanner)
            <img src="{{ Storage::url($appBanner) }}" alt="{{ $siteName }}" class="h-8 w-auto" />
            @else
            <x-cachet::logo class="hidden h-8 w-auto sm:block" />
            <x-cachet::logomark class="h-8 w-auto sm:hidden" />
            @endif
        </a>
    </div>

    @if ($dashboardLoginLink)
    <div class="flex items-center gap-2.5 sm:gap-5">
        <a href="{{ Cachet\Cachet::dashboardPath() }}" class="text-sm font-medium text-zinc-800 transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 sm:text-base">{{ __('Dashboard') }}</a>
        @auth
        {{-- TODO: This form sucks... --}}
        <form action="{{ \Cachet\Cachet::dashboardPath() }}/logout" method="POST">
            @csrf
            <button class="text-sm font-medium text-zinc-800 transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 sm:text-base">Logout</button>
        </form>
        @endauth
        {{-- Conditional Button. --}}
{{--        <a href="{{ route('cachet.dashboard.index') }}" class="rounded bg-primary-500 px-2 py-1 text-sm font-semibold text-zinc-800 dark:text-zinc-400 transition hover:bg-primary-600">Subscribers</a>--}}
    </div>
    @endif
</div>
