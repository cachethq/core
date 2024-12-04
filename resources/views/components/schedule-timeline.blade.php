@use('Cachet\Enums\ScheduleStatusEnum')
@props([
    'date',
    'schedules',
])

    @foreach($schedules as $schedule)
    <div x-data="{ timestamp: new Date(@js($schedule->completed_at)) }" class="bg-white border divide-y rounded-lg ml-9 dark:divide-zinc-700 dark:border-zinc-700 dark:bg-zinc-800">
        <div @class([
            'flex flex-col bg-zinc-50 p-4 dark:bg-zinc-900 gap-2',
            'rounded-lg',
        ])>
            <div class="flex flex-col sm:flex-row justify-between gap-2 flex-col-reverse items-start sm:items-center">
                <div class="flex flex-col flex-1">
                    <div class="flex gap-2 items-center">
                        <h3 class="max-w-full text-base font-semibold break-words sm:text-xl">
                            {{ $schedule->name}}
                        </h3>
                        @auth
                            <a href="{{ $schedule->filamentDashboardEditUrl() }}" class="underline text-right text-sm text-zinc-500 hover:text-zinc-400 dark:text-zinc-400 dark:hover:text-zinc-300" title="{{ __('Edit Incident') }}">
                                <x-heroicon-m-pencil-square class="size-4" />
                            </a>
                        @endauth
                    </div>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $schedule->completed_at->diffForHumans() }} — <time datetime="{{ $schedule->completed_at->toW3cString() }}" x-text="timestamp.toLocaleString()"></time>
                    </span>
                </div>
                <div class="flex justify-start sm:justify-end">
                    <x-cachet::badge :status="$schedule->status" />
                </div>
            </div>
        </div>

        <div class="relative">
            <div class="flex flex-col px-4 divide-y dark:divide-zinc-700">
                <div class="relative py-4" x-data="{ timestamp: new Date(@js($schedule->completed_at)) }">
                    <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-primary-500 prose-a:underline prose-p:leading-normal">{!! $schedule->formattedMessage() !!}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
