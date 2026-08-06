<div {{ $attributes->class(['status-summary'])->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $status->getColor(),
            shades: [100, 200, 400, 500, 800],
        ),
    ]),
]) }}>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-3" role="status" aria-live="polite">
        <div class="flex items-center gap-3">
        <span class="status-summary__icon text-custom-800 dark:text-custom-200">
            @svg($status->getIcon(), 'size-5 shrink-0 text-custom-800 dark:text-custom-200')
        </span>
            @if ($isHeading)
                <h1 class="text-base font-semibold tracking-tight text-custom-800 dark:text-custom-200">{{ $status->getLabel() }}</h1>
            @else
                <p class="text-base font-semibold tracking-tight text-custom-800 dark:text-custom-200">{{ $status->getLabel() }}</p>
            @endif
        </div>

        @if ($lastUpdated)
            <span class="text-xs font-medium text-custom-800/70 dark:text-custom-200/70">
                {{ __('cachet::component.last_updated', ['timestamp' => $lastUpdated->diffForHumans()]) }}
            </span>
        @endif
    </div>
</div>
