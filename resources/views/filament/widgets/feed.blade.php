<x-filament::widget>
    <x-filament::section :heading="__('cachet::cachet.feed.section_heading')">
        <div class="relative">
            <ul role="list" class="gap-4 flex flex-col">
                @forelse ($items as $post)
                    <li>
                        <a class="flex items-center justify-between text-sm" href="{{ $post['link'] }}" target="_blank">
                            <div class="overflow-hidden text-sm leading-6 text-gray-500 dark:text-gray-400">
                                <h3 class="text-base font-medium text-gray-950 dark:text-white">{{ $post['title'] }}</h3>
                                <time class="text-muted text-xs" datetime="{{ $post['date']->toW3cString() }}" title="{{ $post['date']->toDateTimeString() }}">
                                    {{ __('cachet::cachet.feed.posted_at', ['date' => $post['date']->diffForHumans()]) }}
                                </time>
                                <p class="break-words truncate">{{ $post['description'] }}</p>
                            </div>
                            <div class="">
                                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="text-center filament-tables-text-column">
                        <p class="text-sm text-gray-500">{!! $noItems !!}</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </x-filament::section>
</x-filament::widget>

