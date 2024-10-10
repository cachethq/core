<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [200, 900],
        ),
    ]),
])->merge(['title' => $title]) }}>
    <div class="absolute -left-[calc(28px+10px+13px)] top-4 flex h-7 w-7 items-center justify-center rounded-full bg-custom-200 dark:text-zinc-900">
        @if ($icon)
        @svg($icon, 'size-5')
        @else
        <x-heroicon-m-flag class="size-4" />
        @endif
    </div>
</div>
