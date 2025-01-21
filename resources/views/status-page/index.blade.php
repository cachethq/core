<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::status-bar />

        <x-cachet::about />

        <div class="flex items-center justify-end">
            <a href="{{ route('subscriptions.create') }}" class="text-sm text-custom-800 dark:text-custom-200 font-semibold underline">
                {{ __('cachet::subscriptions.button_label') }}
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
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
