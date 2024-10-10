<div {{ $attributes->class(['rounded-md p-4 bg-custom-200 dark:bg-custom-400/40 border border-custom-400'])->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $status->getColor(),
            shades: [200, 400, 800],
        ),
    ]),
]) }}>
    <div class="flex sm:items-center gap-3">
        @svg($status->getIcon(), 'size-5 text-custom-800 dark:text-custom-200')
        <div class="flex flex-1 justify-between items-center">
            <p class="text-sm md:text-base text-custom-800 dark:text-custom-200">{{ $status->getLabel() }}</p>
{{--            <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-custom-800">--}}
{{--                Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago--}}
{{--            </p>--}}
        </div>
    </div>
</div>
