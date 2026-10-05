@props([
    'model',
    'emphasized' => false,
])

<div data-slot="component-name" class="flex min-w-0 items-center gap-1.5">
    <h3 class="truncate {{ $emphasized ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-sm font-medium text-zinc-800 dark:text-zinc-200' }}">
        @if ($model->formattedLink())
            <a href="{{ $model->formattedLink() }}" target="_blank" rel="nofollow noopener" class="hover:underline focus-visible:underline focus-visible:outline-none">{{ $model->name }}</a>
        @else
            {{ $model->name }}
        @endif
    </h3>

    @if ($model->description)
        <span data-slot="description"
              x-data="{ tooltipOpen: false }"
              @mouseenter="tooltipOpen = true"
              @mouseleave="tooltipOpen = false"
              @focusin="tooltipOpen = true"
              @focusout="tooltipOpen = false"
              class="relative flex shrink-0 items-center">
            <button type="button"
                    x-ref="anchor"
                    aria-describedby="system-status-component-{{ $model->getKey() }}-description"
                    aria-label="{{ __('cachet::component.description_label', ['component' => $model->name]) }}"
                    class="inline-flex size-5 items-center justify-center rounded-full text-zinc-400 transition hover:text-zinc-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent dark:text-zinc-500 dark:hover:text-zinc-200">
                <x-heroicon-o-information-circle class="size-4" />
            </button>

            <template x-teleport="body">
                <span x-show="tooltipOpen"
                      x-cloak
                      x-anchor.top.offset.8="$refs.anchor"
                      id="system-status-component-{{ $model->getKey() }}-description"
                      role="tooltip"
                      class="pointer-events-none z-30 w-max max-w-[min(24rem,calc(100vw-2rem))] rounded-lg bg-white px-3 py-2 text-sm text-zinc-900 shadow-lg ring-1 ring-zinc-900/10 dark:bg-zinc-800 dark:text-zinc-100 dark:ring-white/15">
                    {!! $model->formattedDescription() !!}
                </span>
            </template>
        </span>
    @endif
</div>
