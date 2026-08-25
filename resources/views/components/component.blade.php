{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENTS_BEFORE) }}
<li data-component="component" data-component-id="{{ $component->getKey() }}" class="relative px-4 py-4 transition hover:bg-zinc-50/60 dark:hover:bg-white/[0.02] sm:px-6 sm:py-5">
    <div data-slot="content" class="flex flex-col items-start gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-3">
        <div class="flex min-w-0 items-center gap-1.5">
            @if ($nested ?? false)
                <h3 data-slot="title" class="truncate tracking-tight text-zinc-600 dark:text-zinc-300">
                    @if($component->formattedLink())
                        <a href="{{ $component->formattedLink() }}" target="_blank" rel="nofollow noopener" class="before:absolute before:inset-0 before:content-['']">{{ $component->name }}</a>
                    @else
                        {{ $component->name }}
                    @endif
                </h3>
            @else
                <h2 data-slot="title" class="truncate font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                    @if($component->formattedLink())
                        <a href="{{ $component->formattedLink() }}" target="_blank" rel="nofollow noopener" class="before:absolute before:inset-0 before:content-['']">{{ $component->name }}</a>
                    @else
                        {{ $component->name }}
                    @endif
                </h2>
            @endif

            @if($component->description)
                <div data-slot="description" x-data x-popover class="relative flex shrink-0 items-center">
                    <button type="button" x-ref="anchor" x-popover:button aria-label="{{ __('cachet::component.description_label', ['component' => $component->name]) }}" class="flex size-10 items-center justify-center rounded-full text-zinc-400 transition hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200">
                        <x-heroicon-o-information-circle class="size-4" />
                    </button>
                    <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="z-10 w-max max-w-sm rounded-md bg-zinc-900 px-3 py-2 text-xs font-medium text-white shadow-lg dark:bg-zinc-100 dark:text-zinc-900">
                        <span class="pointer-events-none absolute -left-1 top-2 size-2 rotate-45 bg-zinc-900 dark:bg-zinc-100" aria-hidden="true"></span>
                        <p class="relative">{!! $component->formattedDescription() !!}</p>
                    </div>
                </div>
            @endif

            @if (app(\Cachet\Settings\AppSettings::class)->show_component_tags && $component->tags->isNotEmpty())
                <div data-slot="tags" class="mt-2 flex flex-wrap gap-1.5">
                    @foreach ($component->tags as $tag)
                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        @unless ($hideStatus ?? false)
            <div x-data="{ tooltipOpen: false }"
                 @mouseenter="tooltipOpen = true"
                 @mouseleave="tooltipOpen = false"
                 @focusin="tooltipOpen = true"
                 @focusout="tooltipOpen = false"
                 data-slot="status"
                 class="relative shrink-0">
                <div x-ref="badgeAnchor">
                    @if ($component->impacting_incident)
                        <a href="{{ route('cachet.status-page.incident', [$component->impacting_incident]) }}" aria-describedby="component-{{ $component->id }}-updated" class="inline-flex text-sm font-semibold tracking-tight {{ $component->latest_status->getTextColorClasses() }}">
                            {{ $component->latest_status->getLabel() }}
                        </a>
                    @else
                        <span tabindex="0" aria-describedby="component-{{ $component->id }}-updated" class="text-sm font-semibold tracking-tight {{ $component->latest_status->getTextColorClasses() }}">{{ $component->latest_status->getLabel() }}</span>
                    @endif
                </div>

                <div x-show="tooltipOpen"
                     id="component-{{ $component->id }}-updated"
                     role="tooltip"
                     x-cloak
                     x-transition.opacity
                     x-anchor.left.offset.8="$refs.badgeAnchor"
                     class="pointer-events-none z-10 w-max max-w-sm rounded-md bg-zinc-900 px-3 py-2 text-xs font-medium text-white shadow-lg dark:bg-zinc-100 dark:text-zinc-900">
                    {{ __('cachet::component.last_updated', ['timestamp' => $component->updated_at]) }}
                </div>
            </div>
        @endunless
    </div>
</li>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENTS_AFTER) }}
