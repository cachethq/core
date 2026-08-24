<span x-data="{ tooltipOpen: false, timestamp: new Date(@js($timestamp)) }"
      @mouseenter="tooltipOpen = true"
      @mouseleave="tooltipOpen = false"
      @focusin="tooltipOpen = true"
      @focusout="tooltipOpen = false"
      class="relative inline-flex">
    <time x-ref="anchor" datetime="{{ $timestamp->toW3cString() }}" x-text="timestamp.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', timeZoneName: 'short'@if($appSettings->timezone !== '-'), timeZone: '{{ $appSettings->timezone }}'@endif })"></time>

    <div x-show="tooltipOpen"
         x-cloak
         x-transition.opacity
         x-anchor.top.offset.8="$refs.anchor"
         class="pointer-events-none z-10 w-max max-w-sm rounded-md bg-zinc-900 px-3 py-2 text-xs font-medium text-white shadow-lg dark:bg-zinc-100 dark:text-zinc-900">
        {{ $timestamp->diffForHumans() }}
        <span class="block">{{ $timestamp->avoidMutation()->utc()->format('Y-m-d H:i') }} UTC</span>
    </div>
</span>
