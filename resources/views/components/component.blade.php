{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_COMPONENTS_BEFORE) }}
<li class="px-4 py-3">
    <div class="flex items-center justify-between">
        <div class="flex flex-col grow gap-y-1">
            <div class="flex justify-between items-center gap-1.5">
                <div class="flex gap-x-1">
                    <div class="font-semibold leading-6">{{ $component->name }}</div>

                    <div x-data x-popover class="flex items-center">
                        <button x-ref="anchor" x-popover:button>
                            <x-heroicon-o-question-mark-circle class="size-4 text-zinc-500 dark:text-zinc-300" />
                        </button>
                        <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="rounded-sm bg-zinc-900 dark:bg-zinc-200 px-2 py-1 text-xs font-medium text-zinc-100 drop-shadow-sm dark:text-zinc-800">
                            <span class="pointer-events-none absolute -left-1.5 size-4 rotate-45 bg-zinc-900 dark:bg-zinc-200"></span>
                            <p class="relative">{{ __('cachet::component.last_updated', ['timestamp' => $component->updated_at]) }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    @if ($component->incidents_count > 0)
                        <a href="{{ route('cachet.status-page.incident', [$component->incidents->first()]) }}">
                            <x-cachet::badge :status="$component->latest_status" />
                        </a>
                    @else
                        <x-cachet::badge :status="$status" />
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-y-1 text-xs text-zinc-500 dark:text-zinc-300">
                @if($component->description)
                <p class="">{!! $component->description !!}</p>
                @endif
                @if($component->link)
                <a href="{{ $component->link }}" class="text-zinc-700 underline dark:text-zinc-300" target="_blank" rel="nofollow noopener">{{ __('cachet::component.view_details') }}</a>
                @endif
            </div>
        </div>
    </div>
</li>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_BODY_AFTER) }}
