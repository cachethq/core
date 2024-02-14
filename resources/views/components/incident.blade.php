@props([
    'date',
    'incidents',
])

<div class="relative flex flex-col">
    <h3 class="text-xl font-semibold">{{ $date }}</h3>
    @forelse($incidents as $incident)
    <div class="ml-9 mt-5 divide-y rounded-lg border bg-white dark:divide-zinc-700 dark:border-zinc-700 dark:bg-zinc-800">
        <div class="flex flex-col rounded-t-lg bg-zinc-50 p-4 dark:bg-zinc-900">
            <div class="text-xs font-medium">{{ $incident->components->pluck('name')->join(', ') }}</div>
            <div class="flex justify-between">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold">{{ $incident->name}}</h3>
                    <span class="text-xs text-zinc-500">{{ $incident->occurred_at->diffForHumans() }} — {{ $incident->occurred_at->toDayDateTimeString() }}</span>
                </div>
                <div>
                    <x-cachet::incident-badge :type="$incident->status" />
                </div>
            </div>
            <div class="mt-5 text-sm md:text-base w-1/2">
                {!! $incident->formattedMessage() !!}
            </div>
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 -left-9">
                <div class="ml-3.5 h-full border-l-2 border-dashed dark:border-zinc-700"></div>
                <div class="absolute inset-x-0 top-0 h-24 w-full bg-gradient-to-t from-transparent to-zinc-50 dark:from-transparent dark:to-zinc-900"></div>
                <div class="absolute inset-x-0 bottom-0 h-24 w-full bg-gradient-to-b from-transparent to-zinc-50 dark:from-transparent dark:to-zinc-900"></div>
            </div>
            <div class="flex flex-col divide-y px-4 dark:divide-zinc-700">
                @foreach ($incident->incidentUpdates as $update)
                <div class="relative py-4">
                    <x-cachet::incident-update-status :update="$update" />
{{--                    <h3 class="text-lg font-semibold">Incident Update Title</h3>--}}
                    <span class="text-xs text-zinc-500">{{ $update->created_at->diffForHumans() }} — {{ $update->created_at->toDayDateTimeString() }}</span>
                    <div class="prose-sm md:prose mt-2.5">
                        {!! $update->formattedMessage() !!}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
        <div class="text-sm text-zinc-500">
            {{ __('No incidents reported.') }}
        </div>
    @endforelse
</div>
