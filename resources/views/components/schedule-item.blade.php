@use('Cachet\Enums\ScheduleStatusEnum')
@props([
    'schedule',
])


<div x-data="{ timestamp: new Date(@js($schedule->get)) }" class="bg-white border divide-y rounded-lg ml-9 dark:divide-zinc-700 dark:border-zinc-700 dark:bg-white/5">
    <div @class([
        'flex flex-col bg-zinc-50 p-4 dark:bg-accent-background gap-2',
        'rounded-t-lg' => $schedule->updates->isNotEmpty(),
        'rounded-lg' => $schedule->updates->isEmpty(),
    ])>
        @if ($schedule->components()->exists())
        <div class="text-xs font-medium">
            {{ $schedule->components->pluck('name')->join(', ', ' and ') }}
        </div>
        @endif
        <div class="flex flex-col sm:flex-row justify-between gap-2 flex-col-reverse items-start sm:items-center relative">
            <div class="flex flex-col flex-1">
                <x-cachet::timeline-badge icon="cachet-maintenance" :color="\Filament\Support\Colors\Color::Slate" label="" />
                <div class="flex gap-2 items-center">
                    <h3 class="max-w-full text-base font-semibold break-words sm:text-xl">
                        <a href="javascript:void(0)">{{ $schedule->name}}</a>
                    </h3>
                    @auth
                        <a href="{{ $schedule->filamentDashboardEditUrl() }}" class="underline text-right text-sm text-zinc-500 hover:text-zinc-400 dark:text-zinc-400 dark:hover:text-zinc-300" title="{{ __('cachet::incident.edit_button_title') }}">
                            <x-heroicon-m-pencil-square class="size-4" />
                        </a>
                    @endauth
                </div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $schedule->timestamp->diffForHumans() }} — <time datetime="{{ $schedule->timestamp->toW3cString() }}" x-text="timestamp.toLocaleString()"></time>
                </span>
            </div>
            <div class="flex justify-start sm:justify-end">
                <x-cachet::badge :status="$schedule->latestStatus" />

            </div>
        </div>
    </div>

    <div class="relative">
        <div class="absolute inset-y-0 -left-9">
            <div class="ml-3.5 h-full border-l-2 border-dashed dark:border-zinc-700"></div>
            <div class="absolute inset-x-0 top-0 w-full h-24 bg-gradient-to-t from-transparent to-[rgb(var(--accent-background))]"></div>
            <div class="absolute inset-x-0 bottom-0 w-full h-24 bg-gradient-to-b from-transparent to-[rgb(var(--accent-background))]"></div>
        </div>
        <div class="flex flex-col px-4 divide-y dark:divide-zinc-700">
            @foreach ($schedule->updates as $update)
            <div class="relative py-4" x-data="{ timestamp: new Date(@js($update->created_at)) }">
                <x-cachet::schedule-update-status :status="$update->status" />
                <h3 class="text-lg font-semibold">{{ $update->status->getLabel() }}</h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $update->created_at->diffForHumans() }} — <time datetime="{{ $update->created_at->toW3cString() }}" x-text="timestamp.toLocaleString()"></time>
                </span>
                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">{!! $update->formattedMessage() !!}</div>
            </div>
            @endforeach
            <div class="relative py-4" x-data="{ timestamp: new Date(@js($schedule->timestamp)) }">
                <x-cachet::schedule-update-status :status="ScheduleStatusEnum::complete" />

                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $schedule->timestamp->diffForHumans() }} — <time datetime="{{ $schedule->timestamp->toW3cString() }}" x-text="timestamp.toLocaleString()"></time>
                </span>
                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">{!! $schedule->formattedMessage() !!}</div>
            </div>
        </div>
    </div>
</div>
