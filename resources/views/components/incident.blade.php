@props([
    'date',
    'incidents',
])

<div class="relative flex flex-col gap-5" x-data="{ forDate: new Date(@js($date)) }">
    <h3 class="text-xl font-semibold" x-text="forDate.toLocaleString()"></h3>
    @forelse($incidents as $incident)
    <div x-data="{ timestamp: new Date(@js($incident->timestamp)) }" class="bg-white border divide-y rounded-lg ml-9 dark:divide-zinc-700 dark:border-zinc-700 dark:bg-zinc-800">
        <div @class([
            'flex flex-col bg-zinc-50 p-4 dark:bg-zinc-900 gap-2',
            'rounded-t-lg' => $incident->incidentUpdates->isNotEmpty(),
            'rounded-lg' => $incident->incidentUpdates->isEmpty(),
        ])>
            <div class="text-xs font-medium">{{ $incident->components->pluck('name')->join(', ') }}</div>
            <div class="flex flex-col sm:flex-row justify-between gap-2 flex-col-reverse items-center">
                <div class="flex flex-col flex-1">
                    <h3 class="max-w-full text-base font-semibold break-words sm:text-xl">
                        <a href="{{ route('cachet.status-page.incident', $incident) }}">{{ $incident->name}}</a>
                    </h3>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $incident->timestamp->diffForHumans() }} — <span x-text="timestamp.toLocaleString()"></span>
                    </span>
                </div>
                <div class="flex justify-start sm:justify-end">
                    <x-cachet::incident-badge :type="$incident->status" />
                </div>
            </div>
            <div class="prose-sm md:prose md:prose-zinc dark:text-zinc-100">
                {!! $incident->formattedMessage() !!}
            </div>
        </div>

        @if($incident->incidentUpdates->isNotEmpty())
        <div class="relative">
            <div class="absolute inset-y-0 -left-9">
                <div class="ml-3.5 h-full border-l-2 border-dashed dark:border-zinc-700"></div>
                <div class="absolute inset-x-0 top-0 w-full h-24 bg-gradient-to-t from-transparent to-zinc-50 dark:from-transparent dark:to-zinc-900"></div>
                <div class="absolute inset-x-0 bottom-0 w-full h-24 bg-gradient-to-b from-transparent to-zinc-50 dark:from-transparent dark:to-zinc-900"></div>
            </div>
            <div class="flex flex-col px-4 divide-y dark:divide-zinc-700">
                @foreach ($incident->incidentUpdates as $update)
                <div class="relative py-4" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                    <x-cachet::incident-update-status :update="$update" />
{{--                    <h3 class="text-lg font-semibold">Incident Update Title</h3>--}}
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $update->created_at->diffForHumans() }} — <span x-text="timestamp.toLocaleString()"></span>
                    </span>
                    <div class="mt-1 prose-sm md:prose md:prose-zinc dark:text-zinc-100">
                       {!! $update->formattedMessage() !!}
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @empty
        <div class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('No incidents reported.') }}
        </div>
    @endforelse
</div>
