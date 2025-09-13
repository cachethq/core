@use('Cachet\Enums\IncidentStatusEnum')
@props([
    'date',
    'incidents',
])

{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_BEFORE) }}
<div class="relative flex flex-col gap-5" x-data="{ forDate: new Date(@js($date)) }">
    <h3 class="text-xl font-semibold"><time datetime="{{ $date }}" x-text="forDate.toLocaleDateString(@if($appSettings->timezone !== '-')undefined, {timeZone: '{{$appSettings->timezone}}'}@endif )"></time></h3>
    @forelse($incidents as $incident)
        @if($incident instanceof \Cachet\Models\Incident)
            <x-cachet::incident-item :incident="$incident" />
        @elseif($incident instanceof \Cachet\Models\Schedule)
            <x-cachet::schedule-item :schedule="$incident" />
        @endif
    @empty
        <div class="bg-white border divide-y rounded-lg dark:divide-zinc-700 dark:border-zinc-700 dark:bg-white/5">
            <div class="flex flex-col p-4 divide-y dark:divide-zinc-700">
                <div class="prose-sm md:prose prose-zinc dark:prose-invert prose-a:text-accent-content prose-a:underline prose-p:leading-normal">
                    {{ __('cachet::incident.no_incidents_reported') }}
                </div>
            </div>
        </div>
    @endforelse
</div>
{{ \Cachet\Facades\CachetView::renderHook(\Cachet\View\RenderHook::STATUS_PAGE_INCIDENTS_AFTER) }}
