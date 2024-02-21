<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::about />

        <x-cachet::status-bar />

        @foreach($componentGroups as $componentGroup)
        <x-cachet::component-group :component-group="$componentGroup"/>
        @endforeach

        @if($schedules->isNotEmpty())
        <x-cachet::schedules :schedules="$schedules" />
        @endif

        <x-cachet::incidents />
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
