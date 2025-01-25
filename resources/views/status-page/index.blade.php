<x-cachet::layouts.cachet>

    <x-cachet::success-banner />

    <x-cachet::status-bar />

    <x-cachet::about />

    <div class="flex items-center justify-end">
        <a href="{{ route('cachet.subscribers.create') }}" class="text-sm text-custom-800 dark:text-custom-200 font-semibold underline">
            {{ __('cachet::subscriber.subscribe_button_label') }}
        </a>
    </div>

    @foreach($componentGroups as $componentGroup)
        <x-cachet::component-group :component-group="$componentGroup"/>
    @endforeach

    @foreach($ungroupedComponents as $component)
        <x-cachet::component-ungrouped :component="$component" />
    @endforeach

    @if($schedules->isNotEmpty())
        <x-cachet::schedules :schedules="$schedules" />
    @endif

    <x-cachet::incident-timeline />
</x-cachet::layouts.cachet>
