@if ($showSupport)
<div class="flex items-center justify-center gap-2 border-t px-8 py-6 text-center text-sm tracking-tight text-zinc-500 dark:border-zinc-700">
    Powered by
    <a href="https://cachethq.io" title="The open-source status page." rel="noopener" class="inline-flex items-center font-semibold transition hover:opacity-80">
        <x-cachet::logo class="hidden h-5 w-auto sm:block" />
        <x-cachet::logomark class="h-5 w-auto sm:hidden" />
        <span class="ml-2">{{ $cachetVersion }}</span>
    </a>
</div>
@endif
