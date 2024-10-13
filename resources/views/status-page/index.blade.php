<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::status-bar />

        <x-cachet::about />

        @foreach($componentGroups as $componentGroup)
        <x-cachet::component-group :component-group="$componentGroup"/>
        @endforeach

        @foreach($ungroupedComponents as $component)
        <x-cachet::component-ungrouped :component="$component" />
        @endforeach

        <x-cachet::metrics />

        @if($schedules->isNotEmpty())
        <x-cachet::schedules :schedules="$schedules" />
        @endif

        <x-cachet::incident-timeline />
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
