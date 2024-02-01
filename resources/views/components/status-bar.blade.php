<div {{ $attributes->class(['rounded-md p-4 bg-custom-100'])->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [100, 400, 800],
        ),
    ]),
]) }}>
    <div class="flex sm:items-center gap-3">
{{--            <CheckCircleIcon class="w-5 h-5 text-green-400" aria-hidden="true" />--}}
        <div class="flex flex-1 justify-between items-center">
            <p class="text-sm md:text-base text-custom-800">{{ $label }}</p>
{{--            <p class="mt-2 sm:mt-3 text-xs sm:text-sm md:ml-6 md:mt-0 text-green-800">--}}
{{--                Updated <time datetime="2008-02-14 20:00" title="2008-02-14 20:00" class="underline decoration-dotted underline-offset-2 cursor-help">10 minutes</time> ago--}}
{{--            </p>--}}
        </div>
    </div>
</div>
