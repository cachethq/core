{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_ABOUT_BEFORE) }}

@if ($about !== '')
<div>
    <h1 class="text-3xl font-semibold">{{ $title }}</h1>
    <div class="prose-sm md:prose prose-zinc mt-1 dark:prose-invert prose-a:text-accent-content prose-a:underline">
        {!! $about !!}
    </div>
</div>
@endif

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_ABOUT_AFTER) }}
