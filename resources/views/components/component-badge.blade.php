<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 500, 600],
        ),
    ]),
]) }}>
    <div {{ $attributes->class([
        'flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold leading-tight shadow bg-custom-400',
    ]) }}>
        @svg($icon, 'h-5 w-5 dark:text-zinc-700')

        <div class="dark:text-zinc-700">{{ $label }}</div>
    </div>
</div>
