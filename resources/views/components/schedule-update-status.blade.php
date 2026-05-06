<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [200, 400, 700, 900],
        ),
    ]),
])->merge(['title' => $title]) }}>
    <div class="absolute -left-[calc(28px+10px+13px)] top-4 flex h-7 w-7 items-center justify-center rounded-full bg-custom-200 dark:bg-custom-200/80 text-custom-700 isolate">
        @svg($icon, 'size-5')
    </div>
</div>
