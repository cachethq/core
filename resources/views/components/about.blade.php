{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_ABOUT_BEFORE) }}

@if ($about !== '')
    <div class="flex flex-col gap-3">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100 sm:text-3xl lg:text-4xl">{{ $title }}</h1>
        <div class="prose prose-zinc max-w-none dark:prose-invert
                    prose-headings:tracking-tight prose-headings:text-zinc-900 dark:prose-headings:text-zinc-100
                    prose-p:leading-relaxed prose-p:text-zinc-600 dark:prose-p:text-zinc-300
                    prose-a:font-medium prose-a:text-accent-content prose-a:underline prose-a:underline-offset-2
                    prose-strong:text-zinc-900 dark:prose-strong:text-zinc-100
                    prose-code:rounded prose-code:bg-zinc-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:text-[0.85em] prose-code:font-medium prose-code:text-zinc-800 prose-code:before:content-[''] prose-code:after:content-[''] dark:prose-code:bg-zinc-800 dark:prose-code:text-zinc-200">
            {!! $about !!}
        </div>
    </div>
@endif

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_ABOUT_AFTER) }}
