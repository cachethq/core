@props([
    'color',
    'icon',
    'label',
])
<div {{ $attributes->style([
    Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 900],
        ),
    ]),
]) }}>
    <div {{ $attributes->class([
        'flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold leading-tight shadow bg-custom-400 text-custom-900',
    ]) }}>
        @svg($icon, 'h-5 w-5')

        <span>{{ $label }}</span>
    </div>
</div>
