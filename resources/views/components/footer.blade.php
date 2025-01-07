@if ($showSupport || $showTimezone)
<footer class="flex flex-col items-center justify-center gap-2 border-t px-8 py-6 text-center text-sm tracking-tight text-zinc-500 dark:text-zinc-400 dark:border-zinc-700">
    @if($showSupport)
    <div class="flex items-center justify-center gap-2">
        {{ __('cachet::cachet.powered_by') }}
        <a href="https://cachethq.io" title="{{ __('cachet::cachet.open_source_stats_page') }}" rel="noopener" class="inline-flex items-center font-semibold transition hover:opacity-80">
            <x-cachet::logo class="hidden h-5 w-auto sm:block" />
            <x-cachet::logomark class="h-5 w-auto sm:hidden" />
            <span class="ml-2">{{ $cachetVersion }}</span>
        </a>
    </div>
    @endif
    @if($showTimezone)
    <div>
        {!! preg_replace(
            '/\*(.*?)\*/',
            '<span class="font-semibold">$1</span>',
            __('cachet::cachet.all_times_shown_in', ['timezone' => $timezone])
        ) !!}
    </div>
    @endif
</footer>
@endif
