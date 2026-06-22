{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENTS_BEFORE) }}
<li class="relative px-4 py-3 transition hover:bg-zinc-50/60 dark:hover:bg-white/[0.02] sm:px-6 sm:py-4">
    <div class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-1.5">
            <h4 class="truncate font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                @if($component->link)
                    <a href="{{ $component->link }}" target="_blank" rel="nofollow noopener" class="before:absolute before:inset-0 before:content-['']">{{ $component->name }}</a>
                @else
                    {{ $component->name }}
                @endif
            </h4>

            @if($component->description)
                <div x-data x-popover class="relative flex shrink-0 items-center">
                    <button type="button" x-ref="anchor" x-popover:button class="flex items-center justify-center text-zinc-400 transition hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200">
                        <x-heroicon-o-information-circle class="size-4" />
                    </button>
                    <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="z-10 w-max max-w-sm rounded-md bg-zinc-900 px-3 py-2 text-xs font-medium text-white shadow-lg dark:bg-zinc-100 dark:text-zinc-900">
                        <span class="pointer-events-none absolute -left-1 top-2 size-2 rotate-45 bg-zinc-900 dark:bg-zinc-100" aria-hidden="true"></span>
                        <p class="relative">{!! $component->description !!}</p>
                    </div>
                </div>
            @endif
        </div>

        @unless ($hideStatus ?? false)
            <div x-data="{ tooltipOpen: false }"
                 @mouseenter="tooltipOpen = true"
                 @mouseleave="tooltipOpen = false"
                 @focusin="tooltipOpen = true"
                 @focusout="tooltipOpen = false"
                 class="relative shrink-0">
                <div x-ref="badgeAnchor">
                    @if ($component->incidents_count > 0)
                        <a href="{{ route('cachet.status-page.incident', [$component->incidents->first()]) }}" class="inline-flex">
                            <x-cachet::badge :status="$component->latest_status" />
                        </a>
                    @else
                        <x-cachet::badge :status="$status" />
                    @endif
                </div>

                <div x-show="tooltipOpen"
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
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_BODY_AFTER) }}
