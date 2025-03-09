<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto flex max-w-5xl flex-col space-y-6 px-4 py-10 sm:px-6 lg:px-8">
        <x-cachet::status-bar />

        <x-cachet::about />
        @foreach ($componentGroups as $componentGroup)
            <x-cachet::component-group :component-group="$componentGroup" />
        @endforeach

        @foreach ($ungroupedComponents as $component)
            <x-cachet::component-ungrouped :component="$component" />
        @endforeach

        @if ($display_graphs)
        <x-cachet::metrics />
        @endif

        @if ($schedules->isNotEmpty())
            <x-cachet::schedules :schedules="$schedules" />
        @endif

        <x-cachet::incident-timeline />
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
