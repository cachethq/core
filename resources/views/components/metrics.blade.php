{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_METRICS_BEFORE) }}

@if ($metrics->isNotEmpty())
    <div data-component="metrics" class="flex flex-col gap-8">
        @foreach ($metrics as $metric)
            <x-cachet::metric :metric="$metric" />
        @endforeach
    </div>
@endif

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_METRICS_AFTER) }}
