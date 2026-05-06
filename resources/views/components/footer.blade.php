@if ($showSupport || $showTimezone)
    <footer class="mt-auto border-t border-zinc-900/10 dark:border-white/15">
        <div class="container mx-auto flex max-w-5xl flex-col items-center justify-center gap-3 px-4 py-8 text-center text-xs text-zinc-500 dark:text-zinc-400 sm:px-6 lg:px-8">
            @if($showSupport)
                <div class="inline-flex items-center gap-2 tracking-tight">
                    <span>{{ __('cachet::cachet.powered_by') }}</span>
                    <a href="https://cachethq.io" title="{{ __('cachet::cachet.open_source_status_page') }}" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-zinc-700 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                        <x-cachet::logo class="hidden h-4 w-auto sm:block" />
                        <x-cachet::logomark class="h-4 w-auto sm:hidden" />
                        <span class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-[10px] font-medium text-zinc-600 ring-1 ring-zinc-900/10 dark:bg-zinc-800 dark:text-zinc-400 dark:ring-white/15">{{ $cachetVersion }}</span>
                    </a>
                </div>
            @endif
            @if($showTimezone)
                <div id="cachet-footer-timezone" data-timezone="{{ $timezone }}" data-label="" class="text-[11px] tracking-tight text-zinc-400 dark:text-zinc-500"></div>
                <script defer async>
                    document.addEventListener('DOMContentLoaded', function () {
                        const timeZoneLabel = '{!! preg_replace(
                            '/\*(.*?)\*/',
                            '<span class="font-semibold text-zinc-600 dark:text-zinc-300">$1</span>',
                            __('cachet::cachet.all_times_shown_in', ['timezone' => ':timezone:'])
                        ) !!}'
                        let footerTimeZone = document.getElementById('cachet-footer-timezone').dataset.timezone;
                        if (footerTimeZone === '-') {
                            footerTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        }
                        document.getElementById('cachet-footer-timezone').innerHTML = timeZoneLabel.replace(':timezone:', footerTimeZone);
                    });
                </script>
            @endif
        </div>
    </footer>
@endif
