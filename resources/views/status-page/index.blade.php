<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto flex max-w-5xl flex-col space-y-6 px-4 py-10 sm:px-6 lg:px-8">
        <x-cachet::status-bar />

        <x-cachet::about />

        <x-cachet::component-groups />
        
        <x-cachet::metrics />

        @if ($schedules->isNotEmpty())
            <x-cachet::schedules :schedules="$schedules" />
        @endif

        <x-cachet::incident-timeline />
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
