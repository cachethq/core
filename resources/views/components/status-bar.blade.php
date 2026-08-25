{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_STATUS_SUMMARY_BEFORE) }}
<div {{ $attributes->merge(['data-component' => 'status-summary'])->class(['status-summary'])->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $status->getColor(),
            shades: [100, 200, 400, 500, 800],
        ),
    ]),
]) }}>
    <div data-slot="content" class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-3" role="status" aria-live="polite">
        <div data-slot="status" class="flex items-center gap-3">
        <span data-slot="indicator" class="status-summary__icon text-custom-800 dark:text-custom-200">
            {{ \Filament\Support\generate_icon_html(
                $status->getIcon(),
                attributes: new \Illuminate\View\ComponentAttributeBag([
                    'class' => 'size-5 shrink-0 text-custom-800 dark:text-custom-200',
                ]),
            ) }}
        </span>
            @if ($isHeading)
                <h1 data-slot="title" class="text-base font-semibold tracking-tight text-custom-800 dark:text-custom-200">{{ $status->getLabel() }}</h1>
            @else
                <p data-slot="title" class="text-base font-semibold tracking-tight text-custom-800 dark:text-custom-200">{{ $status->getLabel() }}</p>
            @endif
        </div>

        @if ($lastUpdated)
            <span data-slot="last-updated" class="text-xs font-medium text-custom-800/70 dark:text-custom-200/70">
                {{ __('cachet::component.last_updated', ['timestamp' => $lastUpdated->diffForHumans()]) }}
            </span>
        @endif
    </div>
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_STATUS_SUMMARY_AFTER) }}
