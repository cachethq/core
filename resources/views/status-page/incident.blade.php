<x-cachet::cachet>
    <x-cachet::header />

    <div class="container mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <x-cachet::about />

        <div class="mt-6 space-y-10">
            <x-cachet::status-bar />

            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-14 w-full">
                    <x-cachet::incident :date="$incident->occurred_at" :incidents="[$incident]" />
                </div>
            </div>

        </div>
    </div>

    <x-cachet::footer />
</x-cachet::cachet>
