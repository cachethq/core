<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [200, 400, 700, 900],
        ),
    ]),
])->merge(['title' => $title]) }}>
    <div class="absolute -left-[59px] top-4 hidden h-7 w-7 items-center justify-center rounded-full bg-custom-200 text-custom-700 shadow-sm ring-1 ring-custom-400/30 isolate dark:bg-custom-200/80 dark:ring-custom-400/20 lg:flex">
        @svg($icon, 'size-5')
    </div>
</div>
