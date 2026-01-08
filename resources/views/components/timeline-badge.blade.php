<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [200, 400, 700, 900],
        ),
    ]),
])->merge(['title' => $label]) }}>
<div class="absolute -left-[calc(28px+10px+13px)] top-1 flex h-7 w-7 items-center justify-center rounded-full bg-custom-200 dark:bg-custom-200/80 text-custom-700 isolate">
    @svg($icon, 'size-5')
</div>
</div>
{{--    <x-filament::badge :color="$color" :icon="$icon" :icon-size="\Filament\Support\Enums\IconSize::Large" class="bg-custom-400 text-custom-900 dark:text-custom-400">--}}
{{--        {{ $label }}--}}
{{--    </x-filament::badge>--}}
