<x-cachet::cachet :title="$incident->name">
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8 flex flex-col space-y-6">
        <x-cachet::about />

        <x-cachet::status-bar />

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-14 w-full">
                <x-cachet::incident :date="$incident->occurred_at" :incidents="[$incident]" />
            </div>
        </div>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
